<?php
 /**
  * First implementation
  *
  * @XXX warning when accessing non-existent property
  * @XXX check behavior with several uid parts
  */

 class mysfw_operator extends mysfw_core {
  private $_is_identified = false;
  private $_underlaying_type;
  private $_values;
  private $_criteria;
  private $_data_storage;
  private $_uid_injection = null;

  protected $_mns = '\mysfw\module\operator';

  protected $_defaults = [
   'operators:generic_definitions' => ['_id' => null],  // XXX draft generic definition
   'operators:custom_definitions'  => [                 // XXX draft operator specific definitions
    'user' => ['id' => null]
    ]
   ];

  protected $_exceptions = [
   'no_entry' => 1,
   'too_many_entries' => 1
    ];

  /**
   * XXX draft, refactor needed
   * @throw myswf\exception if no definitions available for the given operator
   * @return this current object
   */
  public function morph($type) {
   $this->_underlaying_type = $type;
   $this->_values = (object) null;
   $this->_criteria = (object) null;
   $identified = true;
   $step_to_identification = 0;
   $defs = $this->inform('operators:custom_definitions')[$type] ? : $this->inform('operators:generic_definitions');
   if(!$defs) $this->except("No definition available for `$type` operator");
   foreach($defs as $p => $v){ // XXX temp
    $this->_identify($p, $v);
    if(is_null($v)){
     $step_to_identification++;
     $missing_property = $p;
    }
   }
   $this->_check_identification();
   if($step_to_identification == 1){
    $this->_accept_uid_injection($missing_property);
   }
   return $this;  // XXX draft
  }

  // Automatic initialisation method
  // XXX draft
  protected function _get_ready() {
   $this->set_data_storage($this->get_popper()->indicate('data_storage'));
  }

  public function get_data_storage() {return $this->_data_storage;}
  public function set_data_storage($_) {$this->_data_storage = $_;}

  /**
   * @throw myswf\exception on identification errors
   */
  public function identify($field, $value) {
   if($this->_is_identified()) $this->except("Trying to identify an already identified operator");
   if(!is_null(@$this->_criteria->$field)) $this->except("UID part `$field` already valued (to `{$this->_criteria->$field}`)");
   $this->_identify($field, $value);
   $this->_check_identification();
   return true;
  }

  protected function _identify($p, $v){$this->_criteria->$p = $v;}

  protected function _check_identification(){
   if(!$this->_criteria) return;

   foreach($this->_criteria as $p => $v){
    if(is_null($v)) return;
   }

   $this->_set_identified();
  }

  public function get($property){return $this->_values->$property;} 
  public function set($property, $value){$this->_values->$property = $value;}

  public function get_values(){return $this->_values;}
  public function set_values($_){$this->_values=$_;}

  protected function _set_identified() {$this->_is_identified = true;}
  protected function _is_identified() {return $this->_is_identified;}

  protected function _accept_uid_injection($_){$this->_uid_injection = $_;}
  protected function _get_uid_injection(){return $this->_uid_injection;}
  protected function _uid_injectable(){return ! is_null($this->_uid_injection);}

  // XXX to be checked
  protected function _set_uid($_){
   if(! $this->_uid_injectable()) return;
   $this->set($this->_get_uid_injection(), $_);
   $this->_identify($this->_get_uid_injection(), $_);
   $this->_set_identified();
  }

 public function set_uid($_){$this->_set_uid($_);} // XXX temp

  /**
   * Object's data are created in underlaying data storage
   * @throw myswf\exception on error
   */
  public function create() {   
   if($this->_is_identified()) $this->except("`create` action requested on identified `operator` object (type is {$this->_underlaying_type})");

   if(!$uid = $this->get_data_storage()->add($this->_underlaying_type, $this->_criteria, $this->_values)) $this->except("No (or bad) uid value `$uid` returned by data storage add() action");

   $this->_set_uid($uid); // XXX to check: no need to notice if uid is uninjectable for this operator object ?

   return $uid;
  }

  /**
   * Object's data are updated in underlaying data storage
   * @throw myswf\exception on error
   */
  public function update(){
   if($this->_is_identified()){
    return $this->get_data_storage()->change($this->_underlaying_type, $this->_criteria, $this->_values);
   }
   $this->except("`update` action requested on unidentified `operator` object (type is {$this->_underlaying_type})");
  }

  /**
   * Object's data are retrieved from underlaying data storage
   * @throw myswf\exception on error
   */
  public function recall() {
   $values = $this->get_data_storage()->retrieve($this->_underlaying_type, $this->_criteria);
   switch(count($values)) {
    case 0:
     throw $this->except("No matching entry found in data storage", 'no_entry');
    case 1:
     $this->set_values($values[0]);
     return;

    default:
     throw $this->except("Too many entries found in data storage", 'operator\too_many_entries');
   }
  }

  /**
   * Object's data are deleted from underlaying data storage
   * @throw myswf\exception on error, especially if there is no data to delete
   */
  public function erase() {
   if(! $this->_is_identified()) $this->except("Couldn't erase non-identified object");

   $r = $this->get_data_storage()->delete($this->_underlaying_type, $this->_criteria);

   if($r === 0) $this->except("No data to delete in underlaying data storage"); // XXX Exception or return code ?

   $this->report_debug("Mapped data are now deleted from underlaying data storage");
   return 1;
  }

 }

?>
