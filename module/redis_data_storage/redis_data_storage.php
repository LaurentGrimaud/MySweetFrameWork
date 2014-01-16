<?php
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn("frame\contract\data_storage");
 $this->_learn("aliens\predis\lib\Predis"); //XXX

 class redis_data_storage extends frame\dna implements frame\contract\data_storage, frame\contract\dna {
  protected $_defaults = array(
   'redis_host'    => '127.0.0.1',
   'redis_port'    => 6379,
   'redis_db_name' => 'redis_demo'
  );

  // XXX temp - draft
  private function _get_connection(){
   $single_server = array(
     'host'     => $this->inform('redis_host'),
     'port'     => $this->inform('redis_port'),
     'database' => $this->inform('redis_db_name')
     );
   if(! $predis = new \Predis\Client($single_server)) {
    $this->report_error('Failed to connect to Redis server');
    return false;
   }

   return $predis;
  }


  /**
   * Build data set uid according to given criteria object
   * Based (!) on memcache_data_storage::_criteria_talk()
   *
   * @param $type string
   * @param $crit object of criteria key/value
   * @return uid as string, false on error
   *
   * XXX Error if $crit is empty ?
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

   $uid = "$type|";
   $sep = '';
   foreach($crit as $k => $v){
    if(is_null($v)){
     $this->report_error("No value for `$k` part of uid");
     return false;
    }
    $uid .= "$sep$k:$v";
    $sep = '/';
   }

   $this->report_info("data uid is $uid");
   return $uid;
  }


  private function _encode($values) {
   return json_encode($values);
  }

  private function _decode($data) {
   return json_decode($data);
  }


  public function retrieve($type, $crit = null, $metacrit = null) {
   $this->report_info('`retrieve` action requested');
   if(! $c = $this->_get_connection()){
    $this->report_error("Failed to get redis connection");
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
   return $this->_decode($data);
  }


  public function add($type, $crit, $values){
   $this->report_info('`add` action requested');
   if(! $c = $this->_get_connection()){
    $this->report_error("Failed to get redis connection");
    return false;
   }

   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    return false;
   }

   if(! $data = $this->_encode($values)){
    $this->report_error("Failed to encode `$values`");
    return false;
   }

   if(! $c->set($uid, $data)){
    $this->report_error("Failed to set data in redis - Aborting");
    return false;
   }

   $this->report_debug("`$type` item set, with uid $uid");
   return $uid;
  }


  public function change($type, $crit, $values){
   $this->report_info('`change` action requested');
   if(! $c = $this->_get_connection()){
    $this->report_error("Failed to get redis connection");
    return false;
   }

   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    return false;
   }

   if(false === $raw_data = $c->get($uid)){
    $this->report_error("Failed to retrieve data with uid $uid");
    return false;
   }

   if(! $previous_values = $this->_decode($raw_data)){
    $this->report_error("Failed to decode `$raw_data`");
    return false;
   }

   foreach($values as $k => $v){
    $previous_values->$k = $v;
   }

   if(! $raw_data = $this->_encode($previous_values)){
    $this->report_error("Failed to encode `$previous_values`");
    return false;
   }

   if(false === $c->set($this->_criteria_talk($type, $crit), $raw_data)){
    return false;
   }

   return true;
  }


  public function delete($type, $crit){
   $this->report_info('`delete` action requested');
   if(! $c = $this->_get_connection()){
    $this->report_error("Failed to get redis connection");
    return false;
   }

   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    return false;
   }

   if($c->delete($uid)) {
    $this->report_debug("Item of type $type and uid $uid removed from redis");
    return true;
   }

   $this->report_error("Failed to remove item of type $type and uid $uid from redis");
   return false;
  }

 }

?>
