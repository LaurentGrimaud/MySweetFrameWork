<?php
 /**
  * Abstract class providing the shell for the specific logic
  *  for a given entity (concept) 
  *
  * XXX Draft
  * XXX Bad name ?
  */

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 //$this->_learn('frame\contract\concept');
 $this->_learn('frame\contract\operator');

 abstract class concept_base extends frame\dna implements frame\contract\operator, frame\contract\dna {
  protected $_op;              // mysfw operator object
  protected $_op_type;
  protected $_op_conf;
  protected $_defaults = [
   "concept:operator" => "operator"
  ];

  protected function _get_ready(){
   $this->_op = $this->pop($this->inform('concept:operator'))->morph($this->_op_type);
  }

  protected function _identify($k, $v){$this->_op->identify($k, $v);return $this;} // XXX to be checked

  public function get_values(){return $this->_op->get_values();}

  /** operator interface **/
  public function get($property) {return $this->_op->get($property);}
  public function set($property, $value){$this->_op->set($property, $value); return $this;}
  public function create(){$this->_op->create();return $this;}
  public function update($uptodate_is_error = true){$this->_op->update($uptodate_is_error);return $this;}
  public function recall(){$this->_op->recall();return $this;}
  public function erase(){$this->_op->erase();return $this;}
 }

?>
