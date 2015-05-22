<?php
namespace t0t1\mysfw\module;
use t0t1\mysfw\frame;

$this->_learn('module\stream_pool\exception\invalid_parameters');

class stream_pool extends frame\dna implements frame\contract\dna{

    protected $_mns = '\t0t1\mysfw\module\stream_pool'; //XXX used by dna:except()
    protected $_exceptions = array(   // XXX TEMP exceptions definition
        'invalid_parameters' =>     1,
    );

    protected $_pool= array();

    public function get($dsn){
        if( isset($this->_pool[$dsn])){
            foreach($this->_pool[$dsn] as $i=>$resource){ // tidying
                $resource_type= get_class($resource);
                $this->report_debug(sprintf('Tested resource type is %s for dsn %s', $resource_type,$dsn));
                switch($resource_type){
                    case 'MongoCursor':
                        if($resource->dead() and ! $resource->hasNext()){
                            $this->report_debug(sprintf('Assuming cursor #%s to %s has been exhausted, re-using it', $i, $dsn));
                            return $resource;
                        }
                    break;
                }
            }
        }
        // fallthrough, accessed if there is no available resource
        return $this->connect($dsn);
    }

    public function connect($dsn){
        $this->report_debug(sprintf('Connecting to %s',$dsn));
        if( false === $scheme= parse_url($dsn, PHP_URL_SCHEME)){
            $err= sprintf('Invalid dsn %s',$dsn);
            $this->report_error($err);
            throw $this->except($err, 'invalid_parameters');
        }
        $this->report_debug(sprintf('driver %s',$scheme));
        switch($scheme){
            case 'mongodb' :
                $connection= $this->_mongo_connect($dsn);
            break;
            default:
                $err= sprintf('Unsupported driver %s for %s',$scheme,$dsn);
                $this->report_error($err);
                throw $this->except($err, 'invalid_parameters');
            break;
        }
        $this->_pool[$dsn][]= $connection;
        $this->report_debug(sprintf('%s connections to %s are currently open',count($this->_pool[$dsn]),$dsn));
        return $connection;
    }

    protected function _mongo_connect($dsn){
        $mongodb_dsn= $dsn;
        list($db,$collection)= explode('/',trim(parse_url($mongodb_dsn, PHP_URL_PATH),'/'));
        if( ! $db ){
            $err= sprintf('Invalid dsn: failed to extract database from %s',$dsn);
            $this->report_error($err);
            throw $this->except($err, 'invalid_parameters');
        }
        if( ! $collection ){
            $err= sprintf('Invalid dsn: failed to extract collection from %s',$dsn);
            $this->report_error($err);
            throw $this->except($err, 'invalid_parameters');
        }
        $this->report_debug(sprintf('Assuming db is %s and collection is %s, attemping to connect to mongo',$db,$collection));
        return (new \MongoClient( $dsn))->selectCollection($db,$collection);
    }
}
