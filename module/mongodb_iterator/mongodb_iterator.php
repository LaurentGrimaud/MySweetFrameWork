<?php
namespace t0t1\mysfw\module;
use t0t1\mysfw\frame;

$this->_learn('frame\contract\resource_iterator');
$this->_learn('module\resource_iterator\exception\invalid_parameters');

class mongodb_iterator extends frame\dna implements frame\contract\resource_iterator,\Iterator{

    protected $_mns = '\t0t1\mysfw\module\resource_iterator'; //XXX used by dna:except()
    protected $_exceptions = array(   // XXX TEMP exceptions definition
        'invalid_parameters' =>     1,
    );

    private $_resource, $_operator_under_laying_type= false;

    public function __toString(){
        return json_encode(array('class'=>__CLASS__,'operator_type'=>$this->_operator_under_laying_type,'total'=>$this->count(),'returned'=>$this->count(true)));
    }

    public function wrap($resource,$operator_type){ // __contruct?
        if( ! $resource instanceof \MongoCursor){
            $err= sprintf('Result set must be a valid MongoCursor, found %s',get_class($resource));
            $this->report_error($e);
            throw $this->except($err, 'invalid_parameters');
        }
        $this->_resource=                   $resource;
        $this->_operator_under_laying_type= $operator_type;
        return $this;
    }

    public function count($use_limit=false){
        return $this->_resource->count($use_limit)?:0;
    }

    public function rewind(){$this->_resource->rewind();}
    public function next(){$this->_resource->getNext();}
    public function key(){return $this->_resource->key();}
    public function valid(){return $this->_resource->valid();}

    public function current(){
        //XXX if I only could retrieve uid composition from operator, I could check if this key exists and identify the operator
        //caveat you should always use foreach to get current
        return (array)$this->_resource->current(); //XXX
        return $this->pop('operator')
                ->morph($this->_operator_under_laying_type)
                ->set_values($this->_resource->current());
    }

    public function shift(){
        if($this->_resource->hasNext()){
            $this->next();
        }
        return $this->current();
    }
}
