<?php

 /**
  * Implementation of MySFW data storage agains MongoDB
  *
  * This is WORK IN PROGRESS
  *
  * XXX uid support is not yet correct
  * XXX everything here needs to be carefully checked and tested
  *
  * Requires mongo pecl extension
  * pecl install mongo
  *
  */

 class mysfw_mongodb_data_storage extends mysfw_core implements mysfw_data_storage {
  protected $_defaults = array(
    "mongo_db_name" => "mysfw_demo"
   );

  // XXX temp - draft
  private function _get_connection($type){
   try {
    $m = new Mongo();
    return $m->selectCollection($this->inform('mongo_db_name'), $type); // XXX Conf should be taken from configurator object
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
  public function retrieve($type, $crit, $metacrit){
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
     $this->report_warning("Failed to retrieve data with uid $uid");
     return null;
    }

    if($metacrit && $metacrit['l']){
     $data->limit($metacrit['l']);
    }

    $results = iterator_to_array($data);

    $this->report_debug("Item of type $type and uid $uid retrieved");
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


  private function _save($type, $crit, $values) {
   if(! $c = $this->_get_connection($type)){
    $this->report_error("Failed to get MongoDB connection");
    return false;
   }

   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    return false;
   }

   $values = (array)$values;
   $values['_id'] = new MongoId($uid);

   try {
    if(! $c->save($values)){
     $this->report_error("Failed to set data in MongoDB - Aborting");
     return false;
    }

    $this->report_debug("`$type` item set, with uid $uid");
    return $uid;

   }catch(exception $e){
    $this->report_error("Exception thrown, message is: ".$e->getMessage());
    return false;
   }
  }


  public function change($type, $crit, $values){
   $this->report_info('`change` action requested');

   if(! $entity_list = (array)$this->retrieve($type, $crit)){ // XXX temp cast
    return false;
   }

   $preivous_data = array_pop($entity_list);

   foreach($values as $p => $v){
    $previous_data[$p] = $v;
   }

   return $this->_save($type, $crit, $previous_data);
  }


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
    if($c->remove(array("_id" => new MongoId($uid)))) {
     $this->report_debug("Item of type $type and uid $uid removed from MongoDB");
     return true;
    }

    $this->report_error("Failed to remove item of type $type and uid $uid from MongoDB");
    return false;
   }catch(exception $e){
    $this->report_error("Exception thrown, message is: ".$e->getMessage());
    return false;
   }
  }

 }

?>

