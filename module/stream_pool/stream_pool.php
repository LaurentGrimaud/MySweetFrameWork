<?php
namespace t0t1\mysfw\module;
use t0t1\mysfw\frame;

$this->_learn('module\stream_pool\exception\invalid_parameters');

 /**
  * Handle connections to streams
  *
  * Open, re-use or close connections depending on stream type
  * @todo As the number of handled stream type will increase it whould be wiser to delegate to specialized objects ( mongodb_stream_pool, mysql_stream_pool ... )
  */

class stream_pool extends frame\dna implements frame\contract\dna{

    protected $_mns = '\t0t1\mysfw\module\stream_pool'; //XXX used by dna:except()
    protected $_exceptions = array(   // XXX TEMP exceptions definition
        'invalid_parameters' =>     1,
    );

 /**
  * Pool of streams
  *
  * @access private
  * @var array
  */
    protected $_pool= array();


 /**
  * Get a connection to the stream described by the URI passed as argument
  *
  * Reuse an opened and idle connections referenced in $_pool property prior to open a new one.
  *
  * @todo review and enhance the process of closing idle and unused conections
  * @param URI $dsn stream connection string ( ~dsn )
  * @return resource
  */
    public function get($dsn){
        if( isset($this->_pool[$dsn])){
            foreach($this->_pool[$dsn] as $i=>$resource){ // tidying
                $resource_type= get_class($resource);
                $this->report_debug(sprintf('Tested resource type is %s for dsn %s', $resource_type,$dsn));
                switch($resource_type){
                    case 'MongoClient':
                        return $resource;
                    break;
                }
            }
        }
        // fallthrough, accessed if there is no available resource
        return $this->connect($dsn);
    }

 /**
  * Create a new connection to the stream described by the URI passed as argument
  *
  * @param URI $dsn stream connection string ( ~dsn )
  * @return resource
  */

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

 /**
  * Create a new mongo connection to the stream described by the URI passed as argument
  *
  * @access protected
  * @todo Could be delegated to a mongodb_stream_pool object
  * @param URI $dsn stream connection string ( ~dsn )
  * @return \MongoClient
  */

    protected function _mongo_connect($dsn){
        $this->report_debug(sprintf('Attemping to connect to mongo'));
        return new \MongoClient( $dsn);
    }
}
