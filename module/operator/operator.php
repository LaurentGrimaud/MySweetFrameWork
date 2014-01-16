<?php
 /**
  * ORM layer - Match _one_ object to _one_ DB entry
  * The match (identification) can be done in two ways:
  * 1. The primary key and its counterpart in object properties
  * 2. A set of object property values reputed to form an unique index
  *
  * @def uided: uniquely identified by "primary key"
  *
  * @XXX warning when accessing non-existent property
  * @XXX obsoletes return values due to exceptions introduction
  * @XXX check behavior with several uid parts
  * @XXX uid injectability depends on data storage being used, among others
  * @XXX uid should me more complicated than a single field
  * @XXX lacks a get_uid() to retrieve the value of the injected uid ?
  *
  * For the "new" operators:
  *  uid and fields for identification are not the same, sometimes...
  *  uid and identification fields may not be mandatory, only no fool-warranty if not provided
  *  uid are important (only ?) in creation
  *  'custom_definition' => [
  *    'uid'   => ['_id'],
  *    'crits' => [['name', 'pass'], ['email', 'pass']
  *   ]
  *  No needs to ensure that only one identification method is used ?
  */
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('module\operator\exception\no_entry');
 $this->_learn('module\operator\exception\too_many_entries');

 class operator extends frame\dna implements frame\contract\dna {
  private $_is_uided = false;
  private $_uid_def;          // array (flat) of uid parts
  private $_underlaying_type;
  private $_values;           // object properties
  private $_new;              // object properties potentially changed
  private $_uid_parts;        // set of uid components value in an object
  private $_criteria;
  private $_data_storage;
  private $_uid_injection = null;

  protected $_defaults = [
   'operators:generic_definitions' => ['_id' => null],  // XXX draft generic definition
   'operators:custom_definitions'  => [                 // XXX draft operator specific definitions
    'user' => ['id' => null]
    ]
   ];

  protected $_mns = '\t0t1\mysfw\module\operator'; //XXX used by dna:except()
  protected $_exceptions = [   // XXX TEMP exceptions definition
   'no_entry' => 1,
   'too_many_entries' => 1
    ];

  /**
   * Configure the current object to act as the given type
   *
   * @throw myswf\exception if no definitions available for the given operator
   * @return this current object
   *
   * XXX draft, refactor needed
   */
  public function morph($type) {
   if($this->_underlaying_type) throw $this->except('To morph an already morphed object is forbidden'); //XXX Why ?
   $this->_underlaying_type = $type;
   $this->_values = (object)null;
   $this->_new = (object)null;
   $this->_criteria = (object)null;
   $this->_uid_parts = (object)null;
   $step_to_identification = 0;
   $customer_defs = $this->inform('operators:custom_definitions');
   $defs = @$customer_defs[$type] ? : $this->inform('operators:generic_definitions');
   if(!$defs) throw $this->except("No definition available for `$type` operator");
   foreach($defs as $p => $v){ // XXX temp, not always correct !
    if(is_null($v)){
     $step_to_identification++;
     $missing_property = $p;
    }
   }
   $this->_uid_def = array_keys($defs);
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
   * Set the criteria values, the ones being used to identify the correct entry
   * in the underlaying data storage
   *
   * @throw myswf\exception on identification errors
   */
  public function identify($field, $value) {
   if($this->_get_uided()) throw $this->except("Trying to identify an already identified operator"); // XXX != loaded...
   if(!is_null(@$this->_criteria->$field)) throw $this->except("UID part `$field` already valued (to `{$this->_criteria->$field}`)");
   $this->_identify($field, $value);
   return $this;
  }

  /**
   * Setter dedicated to criteria value, used for identification
   *
   * @param $p string the criteria to set
   * @param $v the value to set
   */
  protected function _identify($p, $v){$this->_criteria->$p = $v;}

  /**
   * @return $this
   */
  protected function _check_uided(){
   if(!$this->_uid_def) return $this->_unset_uided();

   foreach($this->_uid_def as $p => $v){
    if(is_null($v)) return $this->_unset_uided();
   }

   return $this->_set_uided();
  }

  /**
   * Generic getter for operator property
   */
  public function get($property){
    if( ! isset($this->_values->$property)) return null;
    return $this->_values->$property;
  } 

  /**
   * Generic setter for operator property
   */
  public function set($property, $value){$this->_new->$property = $value; return $this->_set($property,$value);}
  protected function _set($property, $value){$this->_values->$property = $value; return $this;}

  public function get_values(){return $this->_values;}
  public function set_values($_){$this->_new=$this->_values=(object)$_;return $this->_check_uided();}

  /**
   * Internal set_values()
   */
  protected function _set_values($_){$this->_reset_new();$this->_values=(object)$_;return $this->_check_uided();return $this;}

  // XXX usefull ?
  public function get_new(){return $this->_new;}

  protected function _set_uided() {$this->_is_uided = true;return $this;}
  protected function _unset_uided() {$this->_is_uided = false;return $this;}
  protected function _get_uided() {return $this->_is_uided;}

  protected function _accept_uid_injection($_){$this->_uid_injection = $_;}
  protected function _get_uid_injection(){return $this->_uid_injection;}
  protected function _uid_injectable(){return ! is_null($this->_uid_injection);}


  protected function _reset_new(){$this->_new=(object)null;return $this;}

  // XXX to be checked
  protected function _set_uid($_){
   if(! $this->_uid_injectable()) return;
   $this->_set($this->_get_uid_injection(), $_);
   $this->_identify($this->_get_uid_injection(), $_);
   $this->_set_uided();
  }

 public function set_uid($_){$this->_set_uid($_);$this->_new->{$this->_get_uid_injection()} = $_;return $this;} // XXX temp and dangerous

  /**
   * Object's data are created in underlaying data storage
   * @throw myswf\exception on error
   */
  public function create() {   
   if($this->_get_uided())
    throw $this->except("`create` action requested on UIDed `operator` object (type is {$this->_underlaying_type})");
   if(!$uid = $this->get_data_storage()->add($this->_underlaying_type, $this->_criteria, $this->_new))
    throw $this->except("No (or bad) uid value `$uid` returned by data storage add() action");
   $this->_reset_new()->_set_uid($uid); // XXX to check: no need to notice if uid is uninjectable for this operator object ?
   return $this;
  }

  /**
   * Updates the object's data in underlaying data storage
   * 
   * @throw myswf\exception on error
   */
  public function update(){
   if($this->_get_uided()){
    return $this->get_data_storage()->change($this->_underlaying_type, $this->_criteria, $this->_new);
   }
   throw $this->except("`update` action requested on unidentified `operator` object (type is {$this->_underlaying_type})");
   return $this->_reset_new();
  }

  /**
   * Object's data are retrieved from underlaying data storage
   *
   * @throw myswf\exception on error
   */
  public function recall() {
   if(! sizeof($this->_criteria)) throw $this->except("No criteria available - Entry identification impossible");
   $values = $this->get_data_storage()->retrieve($this->_underlaying_type, $this->_criteria);
   switch(count($values)) {
    case 0:
     throw $this->except("No matching entry found in data storage", 'no_entry');
    case 1:
     return $this->_set_values($values[0]);

    default:
     throw $this->except("Too many entries found in data storage", 'too_many_entries');
   }
   return $this;
  }

  /**
   * Object's data are deleted from underlaying data storage
   * @throw myswf\exception on error, especially if there is no data to delete
   * @XXX Chainability is only here for interface homogeneity, but is useless, right ?
   */
  public function erase() {
   if(! $this->_get_uided()) throw $this->except("Couldn't erase unUIDed object");

   $r = $this->get_data_storage()->delete($this->_underlaying_type, $this->_criteria);

   if($r === 0) throw $this->except("No data to delete in underlaying data storage"); // XXX Exception or return code ?

   $this->report_debug("Mapped data are now deleted from underlaying data storage");
   return $this;
  }

 }

