<?php
/**
 * Yet naive implementation of MySFW Data Storage against Memcache
 *
 * XXX error handling
 * XXX TTL handling
 * XXX Connection handling
 */

 class mysfw_memcache_data_storage extends mysfw_core implements mysfw_data_storage {
  protected $_ttl = 10; // XXX need parametrisation

  // XXX temp - draft
  private function _get_connection(){
   $memcache = new Memcache;
   if(! $memcache->connect('localhost', 11211)){
    $this->report_error('Failed to connect to memcache');
    return false;
   }

   return $memcache;
  }

  /**
   * Build data set uid according to given criteria object
   *
   * @param $type string
   * @param $crit object of criteria key/value
   * @return uid as string, false on error
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

   $r = $sep = '';
   foreach($crit as $k => $v){
    if(!is_null($v)){$r .= "$k:$v";}
    $sep = '/';
   }

   $uid = "$type|$r";
   $this->report_info("data uid is $uid");
   return $uid;
  }


  public function retrieve($type, $crit){
   $this->report_info('`retrieve` action requested');
   if(! $c = $this->_get_connection()){
    $this->report_error("Failed to get memcache connection");
    return false;
   }

   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to get data uid");
    return false;
   }

   if(false === $data = $c->get($uid)){
    $this->report_warning("Failed to retrieve data with uid $uid");
    return null;
   }

   $this->report_debug("Item of type $type and uid $uid retrieved");
   return $data;
  }


  public function add($type, $crit, $values){
   $this->report_info('`add` action requested');
   if(! $c = $this->_get_connection()){
    $this->report_error("Failed to get memcache connection");
    return false;
   }

   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    return false;
   }

   if(! $c->set($uid, $values, 0, $this->_ttl)){
    $this->report_error("Failed to set data in memcache - Aborting");
    return false;
   }

   $this->report_debug("`$type` item set, with uid $uid");
   return $uid;
  }


  public function change($type, $crit, $values){
   $this->report_info('`change` action requested');
   if(! $c = $this->_get_connection()){
    $this->report_error("Failed to get memcache connection");
    return false;
   }

   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    return false;
   }

   if(false === $data = $c->get($uid)){
    $this->report_error("Failed to retrieve data with uid $uid");
    return false;
   }

   foreach($values as $k => $v){
    $data->$k = $v;
   }

   if(false === $c->set($this->_criteria_talk($type, $crit), $values, 0, $this->_ttl)){
    return false;
   }

   return true;
  }

  public function delete($type, $crit){
   $this->report_info('`delete` action requested');
   if(! $c = $this->_get_connection()){
    $this->report_error("Failed to get memcache connection");
    return false;
   }

   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    return false;
   }

   if($c->delete($uid)) {
    $this->report_debug("Item of type $type and uid $uid removed from memcache");
    return true;
   }

   $this->report_error("Failed to remove item of type $type and uid $uid from memcache");
   return false;
  }

 }

?>
