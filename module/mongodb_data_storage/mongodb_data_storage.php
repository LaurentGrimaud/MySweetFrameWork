<?php
 /**
  * Implementation of MySFW data storage agains MongoDB
  *
  * This is WORK IN PROGRESS
  *
  * XXX uid support is not yet correct
  * XXX everything here needs to be carefully checked and tested
  * XXX migration to exceptions
  *
  * Requires mongo pecl extension
  * pecl install mongo
  *
  */
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn("frame\contract\data_storage");

 class mongodb_data_storage extends frame\dna implements frame\contract\data_storage, frame\contract\dna {
  protected $_defaults = array(
    "mongo:db" => "mysfw_demo"
   );
  protected $_collection_options = array(
    "safe" => true
   );

  // XXX temp - draft
  private function _get_connection($type){
   try {
    $m = new \Mongo();
    return $m->selectCollection($this->inform('mongo:db'), $type); // XXX Conf should be taken from configurator object
   }catch(exception $e){
    $this->report_error("Failed to connect to MongoDB, message is: ".$e->getMessage());
    return false;
   }
  }


  // XXX temp - draft - Needs to be in data storage interface
  public function get_connection() {
   try {
    $m = new \Mongo();
    return $m->selectDB($this->inform('mongo:db'));
   }catch(exception $e){
    $this->report_error("Failed to connect to MongoDB, message is: ".$e->getMessage());
    return false;
   }
  }


  /**
   * Build data set uid according to given criteria object
   * Based (!) on memcache_data_storage::_criteria_talk()
   *
   * @param $type string
   * @param $crit object of criteria key/value
   * @return uid as string, false on error
   *
   * XXX useful ?
   */
  private function _criteria_talk($type, $crit){
   if(! $crit){
    $this->report_error("Can't build uid without criteria");
    return false;
   }

   if(! $type){
    $this->report_error("Can't build uid without type");
    return false;
   }

   $crit = (array)$crit;

   if(count($crit) !== 1){
    $this->report_error("Only one part uid supported in this version. Sorry...");
    return false;
   }

   $uid = array_pop($crit);
   $this->report_info("data uid is $uid");
   return $uid;
  }


  // XXX WIP
  /**
   * metacrit[l] = 10
   * metacrit[o] = 10
   * metacrit[s][field_name] = 1|-1 (asc ou desc)
   *
   * crit[field_name] = value               // exact match
   * crit[field_name] = ["$gt"  => value]   // strictly greater than
   * crit[field_name] = ["$gte" => value]   // greater than or equal to
   * crit[field_name] = ["$lt"  => value]   // strictly lower than
   * crit[field_name] = ["$lte" => value]   // lower than or equal to
   */
  public function retrieve($type, $crit = null, $metacrit = null) {
   try{
    $this->report_info('`retrieve` action requested');
    if(! $c = $this->_get_connection($type)){
     $this->report_error("Failed to get MongoDB connection");
     return false;
    }

    if(! $crit) {
     $crit = array();
    }

    if(false === $data = $c->find($crit)){
     $this->report_warning("Failed to retrieve data (type $type)");
     return null;
    }

    if($metacrit) {
     if( $metacrit['o']){
      $data->skip($metacrit['o']);
     }
     if($metacrit['l']){
      $data->limit($metacrit['l']);
     }

     if($metacrit['s']) {
      $data->sort($metacrit['s']);
     }
     $this->report_debug(print_r($metacrit, true));
    }

    $results = iterator_to_array($data, false);

    $this->report_debug(count($results)." `$type` item(s) retrieved");

    return $results;

   }catch(exception $e){
    $this->report_error("Exception thrown, message is: ".$e->getMessage());
    return false;
   }
  }


  public function add($type, $crit, $values){
   $this->report_info('`add` action requested');

   if(! $c = $this->_get_connection($type)){
    $this->report_error("Failed to get MongoDB connection");
    return false;
   }

   try {
    $data_to_insert = (array)$values;

    if(! $c->save($data_to_insert)) {
     $this->report_error("Failed to set data in MongoDB - Aborting");
     return false;
    }

    $uid = (string)(@$data_to_insert['_id']);

    $this->report_debug("`$type` item set, with uid $uid");
    return $uid;

   }catch(exception $e){
    $this->report_error("Exception thrown, message is: ".$e->getMessage());
    return false;
   }
  }

 // XXX duplicate of add 
  private function _save($type, $crit, $values) {
   if(! $c = $this->_get_connection($type)){
    $this->report_error("Failed to get MongoDB connection");
    return false;
   }

   $values = (array)$values;

   try {
    if(! $c->save($values)){
     $this->report_error("Failed to set data in MongoDB - Aborting");
     return false;
    }

    $this->report_debug("`$type` item set, with uid {$values['_id']}");
    return (string)$values['_id'];

   }catch(exception $e){
    $this->report_error("Exception thrown, message is: ".$e->getMessage());
    return false;
   }
  }


  // XXX old implementation, using retrieve then change
  public function OLD_change($type, $crit, $values){
   $this->report_info('`change` action requested');

   if(! $entity_list = (array)$this->retrieve($type, $crit)){ // XXX temp cast
    return false;
   }

   $previous_data = array_pop($entity_list);

   foreach($values as $p => $v){
    $previous_data[$p] = $v;
   }

   return $this->_save($type, $crit, $previous_data);
  }

  // New implementation, using MongoCollection::update()
  public function change($type, $crit, $values){
   $this->report_info('`change` action requested');

   if(! $c = $this->_get_connection($type)){
    $this->report_error("Failed to get MongoDB connection");
    return false;
   }

   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    return false;
   }
   $values_id= $values->_id;
   unset($values->_id);
   $this->report_debug(print_r((array)$values, true));

   try {
    if($res = $c->update(array("_id" => $uid), array('$set' => (array)$values))) {
     $values->_id = $values_id;
     $this->report_debug(print_r($res, true));
     $this->report_debug("Item of type $type and uid $uid updated");
     return true;
    }

    $this->report_error("Failed to update item of type $type and uid $uid from MongoDB");
    return false;
   }catch(exception $e){
    $this->report_error("Exception thrown, message is: ".$e->getMessage());
    return false;
   }
  }


  /**
   * @return false on error
   *         1 if one item found and deleted
   *         0 if no item found (and deleted)
   **/
  public function delete($type, $crit){
   $this->report_info('`delete` action requested');
   if(! $c = $this->_get_connection($type)){
    $this->report_error("Failed to get MongoDB connection");
    return false;
   }

   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    return false;
   }

   try {
    $res = $c->remove(array("_id" => new \MongoId($uid)), $this->_collection_options);

    if($res === false || @$res['err']) {
     $this->report_error("Failed to remove item of type $type and uid $uid from MongoDB");
     return false;
    }

    if($res === true || $res['n'] === 1) {
     $this->report_debug("Item of type $type and uid $uid removed from MongoDB - ".print_r($res, true));
     return 1;
    }

    if($res['n'] === 0) {
     $this->report_warning("No item of type $type and uid $uid to remove from MongoDB");
     return 0;
    }

    $this->report_error("Something strange just happened: {$res['n']} items of $type and uid $uid removed from MongoDB");
    return false;

   }catch(exception $e){
    $this->report_error("Exception thrown, message is: ".$e->getMessage());
    return false;
   }
  }
 }
