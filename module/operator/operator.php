<?php
 /**
  * ORM layer - Match _one_ object to _one_ DB entry
  * The match (identification) can be done in two ways:
  * 1. The primary key and its counterpart in object properties
  * 2. A set of object property values reputed to form an unique index
  *
  * @def uided: uniquely identified
  * @def p-uided: uniquely identified by "primary key"
  * @def a-uided: uniquely identified by alternative key
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
  private $_p_uided = false;  // Uniquely identified via primary key ?
  private $_a_uided = false;  // Uniquely identified via alternate key ?
  private $_uid_def;          // array (flat) of uid parts
  private $_underlaying_type;
  private $_values;           // array of properties
  private $_new;              // array of properties potentially changed
  private $_uid_parts;        // set of uid components value in an object
  private $_criteria;         // array of criteria to use in identification
  private $_data_storage;     // mysfw data storage to use
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
   * @throw t0t1\mysfw\frame\exception\dna if no definitions available for the given operator
   * @return this current object
   *
   * XXX draft, refactor needed
   */
  public function morph($type) {
   if($this->_underlaying_type) throw $this->except('To morph an already morphed object is forbidden'); //XXX Why ?
   $this->_underlaying_type = $type;
   $this->_values = [];
   $this->_new = [];
   $this->_criteria = [];
   $this->_uid_parts = [];
   return $this->_define_uid();
  }

  /**
   * Configure the object uid strategy according to the operator type
   * Determine if uid injection from data storage is allowed
   *
   * @return this current object
   *
   * XXX draft
   */
  protected function _define_uid() {
   $step_to_identification = 0;
   $customer_defs = $this->inform('operators:custom_definitions');
   $defs = @$customer_defs[$this->_underlaying_type] ? : $this->inform('operators:generic_definitions');
   if(!$defs) throw $this->except("No definition available for `{$this->_underlaying_type}` operator");
   $this->_uid_def = array_keys($defs);
   foreach($defs as $p => $v){ // XXX temp, not always correct !
    $this->_set($p, $v);
    if(is_null($v)){
     $step_to_identification++;
     $missing_property = $p;
    }
   }
   $this->_uid_parts=$defs; // XXX draft
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
   if($this->_is_uided()) throw $this->except("Trying to identify an already identified operator"); // XXX != loaded...
   if(!is_null(@$this->_criteria[$field])) throw $this->except("UID part `$field` already valued (to `{$this->_criteria[$field]}`)");
   $this->_identify($field, $value);
   return $this;
  }

  /**
   * Setter dedicated to criteria value, used for identification
   *
   * @param $p string the criteria to set
   * @param $v the value to set
   */
  protected function _identify($p, $v){$this->_criteria[$p] = $v;}

  /**
   * @return $this
   */
  protected function _check_uided(){
   $this->_check_a_uided();
   $this->_check_p_uided();
   return $this;
  }

  /**
   * Checks if current operator is p-uided
   * Sets the correct value to _p_uided
   *
   * @return $this
   */
  protected function _check_p_uided() {
   $this->_p_uided = false;
   if(!$this->_uid_def) return $this;
   if(!$this->_criteria) return $this;

   foreach($this->_uid_def as $_uid_part){
    if(!(@$this->_criteria[$_uid_part])) return $this;
   }

   return $this->_set_uided();
  }


  /**
   * Checks is the current operator is uniquely identified via
   * the alternative method and records the result into the
   * internal flag _a_uided.
   * 
   * @return $this
   */
  protected function _check_a_uided() {
   $this->_a_uided = false;
   if(!$this->_criteria) return $this;
   $this->_a_uided = true;
   return $this;
  }

  /**
   * Generic getter for operator property
   */
  public function get($property){
    if( ! isset($this->_values[$property])) return null;
    return $this->_values[$property];
  } 

  /**
   * Generic setter for operator property
   */
  public function set($property, $value){$this->_new[$property] = $value; return $this->_set($property,$value);}
  protected function _set($property, $value){$this->_values[$property] = $value; return $this;}

  public function get_values(){return $this->_values;}
  public function set_values($_){$this->_new=$this->_values=[];return $this->_check_uided();}

  /**
   * Internal set_values()
   *
   * XXX $_ is an object, but is stored as an array
   */
  protected function _set_values($_){$this->_reset_new();$this->_values=(array)$_;return $this->_check_uided();}

  // XXX usefull ?
  public function get_new(){return $this->_new;}

  protected function _set_uided() {$this->_p_uided = true;return $this;}
  protected function _unset_uided() {$this->_p_uided = false;return $this;}
  protected function _is_uided() {return ($this->_p_uided || $this->_a_uided);}

  protected function _accept_uid_injection($_){$this->_uid_injection = $_;}
  protected function _get_uid_injection(){return $this->_uid_injection;}
  protected function _uid_injectable(){return ! is_null($this->_uid_injection);}


  protected function _reset_new(){$this->_new=[];return $this;}

  // XXX to be checked
  protected function _set_uid($_){
   if(! $this->_uid_injectable()) return;
   $this->_set($this->_get_uid_injection(), $_);
   $this->_identify($this->_get_uid_injection(), $_);
   $this->_set_uided();
  }

 public function set_uid($_){$this->_set_uid($_);$this->_new[$this->_get_uid_injection()] = $_;return $this;} // XXX temp and dangerous
 public function get_uid(){$r=[];foreach($this->_uid_def as $_)$r[$_] = $this->get($_);return $r;}

  /**
   * Object's data are created in underlaying data storage
   * @throw myswf\exception on error
   */
  public function create() {   
   $this->_check_uided();
   if($this->_is_uided())
    throw $this->except("`create` action requested on UIDed `operator` object (type is {$this->_underlaying_type})");
   if(!$uid = $this->get_data_storage()->add($this->_underlaying_type, $this->get_uid(), $this->_new))
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
   $this->_check_uided();
   if($this->_is_uided()){
    return $this->get_data_storage()->change($this->_underlaying_type, $this->_criteria, $this->_new);
   }
   throw $this->except("`update` action requested on unidentified `operator` object (type is {$this->_underlaying_type})");
   return $this->_reset_new();
  }

  /**
   * Object's data are retrieved from underlaying data storage
   * Operator needs to be uided (primary or alternatively)
   *
   * @throw myswf\exception on error
   */
  public function recall() {
   $this->_check_uided();
//   print $this->status();
    if(! $this->_is_uided()) throw $this->except("`recall` action requested on un-UIDed `operator` object (type is {$this->_underlaying_type})");
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
   if(! $this->_is_uided()) throw $this->except("Couldn't erase unUIDed object");

   $r = $this->get_data_storage()->delete($this->_underlaying_type, $this->_criteria);

   if($r === 0) throw $this->except("No data to delete in underlaying data storage"); // XXX Exception or return code ?

   $this->report_debug("Mapped data are now deleted from underlaying data storage");
   return $this;
  }


  public function status() {
   $res = '';
   $res .= 'Is alternatively uided ? '.($this->_a_uided?"true":"false")."\n";
   $res .= 'Is primary uided ? '.($this->_p_uided?"true":"false")."\n";
   return $res;
  }

 }

