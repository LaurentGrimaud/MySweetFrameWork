<?php
namespace t0t1\mysfw\module;
use t0t1\mysfw\frame;

$this->_learn('frame\contract\resource_iterator');
$this->_learn('module\resource_iterator\exception\invalid_parameters');

 /**
  * Iterate a mongodb cursor
  *
  * This wrapper around mongodb cursor allow more control over data returned and allow to parse large result sets without extensive memory usage as data is put into memory only when needed/wanted.
  * It is designed to be consumed using foreach or while constructs.
  */

class mongodb_iterator extends frame\dna implements frame\contract\resource_iterator{

    protected $_mns = '\t0t1\mysfw\module\resource_iterator'; //XXX used by dna:except()
    protected $_exceptions = array(   // XXX TEMP exceptions definition
        'invalid_parameters' =>     1,
    );


 /**
  * Handler for \MongoCursor instance
  *
  * @access private
  * @var \MongoCursor 
  */
    private $_resource= false;

 /**
  * Simple helper for logging purposes
  *
  * @return string
  */
    public function __toString(){
        return json_encode(array('class'=>__CLASS__,'total'=>$this->count(),'returned'=>$this->count(true)));
    }

 /**
  * Wrap a \MongoCursor instance
  *
  * @param \MongoCursor $resource
  * @return this
  */
    public function wrap($resource){ // I do not use typehint since this function is defined in resource_iterator interface
        if( ! $resource instanceof \MongoCursor){
            $err= sprintf('Result set must be a valid MongoCursor, found %s',get_class($resource));
            $this->report_error($err);
            throw $this->except($err, 'invalid_parameters');
        }
        $this->_resource= $resource;
        return $this;
    }

 /**
  * Count results referenced by the \MongoCursor wrapped
  *
  * @param $use_limit indicat if count should return the total number of results or limited number of results using limit/offset
  * @return int
  */
    public function count($use_limit=false){
        return $this->_resource->count($use_limit)?:0;
    }

 /**
  * Iterator::rewind implementation
  */
    public function rewind(){$this->_resource->rewind();}

 /**
  * Iterator::next implementation
  */
    public function next(){$this->_resource->getNext();}

 /**
  * Iterator::key implementation
  * @return mixed
  */
    public function key(){return $this->_resource->key();}

 /**
  * Iterator::valid implementation
  * @return boolean
  */
    public function valid(){return $this->_resource->valid();}

 /**
  * Iterator::current implementation
  * @return array
  */
    public function current(){return $this->_resource->current();}

 /**
  * Fetch next wrapped \MongoCursor result set or current one if cursor has been exhausted.
  *
  * @return array
  */
    public function shift(){
        if($this->_resource->hasNext()){
            $this->next();
        }
        return $this->current();
    }
}
