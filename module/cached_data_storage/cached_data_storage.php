<?php
 /**
  * XXX Work in progress
  * XXX Needs exceptions
 */
 namespace t0t1\mysfw\module\cached_data_storage;
 use t0t1\mysfw\frame;

 $this->_learn("substructure/mysfw_core.class.php");
 $this->_learn("substructure/mysfw_data_storage.interface.php");
 $this->_learn("modules/cached_data_storage/cached_data_storage.class.php");

 class cached_data_storage extends frame\dna implements frame\contract\data_storage, frame\contract\dna {
  private $_cache_data_storage;
  private $_base_data_storage;

  // Automatic initialisation method
  // XXX draft
  protected function _get_ready() {
   $this->set_cache_data_storage($this->get_popper()->indicate('cache_data_storage'));
   $this->set_base_data_storage($this->get_popper()->indicate('base_data_storage'));
  }
  
  public function add($type, $crit, $values){
   $this->report_info('`add` action requested');
   if(false === $uid = $this->_get_base_data_storage()->add($type, $crit, $values)){
    $this->report_error('Failed to add data to base data storage');
    return false;
   }
   /// XXX what about composite index, so no uid returned ?
   if(false === $cache_crit = $this->_build_cache_uid($crit, $uid)){
    $this->report_error("Failed to complete cache criteria with newly created uid $uid - Aborting");
    return false;
   }

   if(false === $this->_get_cache_storage()->add($type, $cache_crit, $values)){
    $this->report_error('Failed to add data to cache data storage');
    return false;
   }

   $this->report_info("Data added to both base and cache data storage, base uid is $uid");
   return $uid;
  }


  public function retrieve($type, $crit){
   $this->report_info('`retrieve` action requested');
   if($res = $this->_get_cache_storage()->retrieve($type, $crit)){
    $this->report_info('Data found on cache data storage');
    return $res;
   }

   $this->report_info('Data not found on cache data storage, will query the base one');
   if(! $res = $this->_get_base_data_storage()->retrieve($type, $crit)){
    $this->report_error('Failed to get data from base storage');
    return false;
   }
   $this->report_info('Data found on base data storage, will cache it');

   if(false === $this->_get_cache_storage()->add($type, $crit, $values)){
    $this->report_error('Failed to cache data');
    return false;
   }

   $this->report_info('Data cached');
   return $res;
  }


  public function change($type, $crit, $values) {
   $this->report_info('`change` action requested');
   if(! $this->_get_base_data_storage()->change($type, $crit, $values)){
    $this->report_error('Failed to change data in base data storage');
    return false;
   }

   // cache data update, with full data set
   if(! $res = $this->_get_base_data_storage()->retrieve($type, $crit)){
    $this->report_error('Failed to get complete data from base data storage');
    return false;
   }

   if(! $this->_get_cache_storage()->add($type, $crit, $values)){
    $this->report_error('Failed to cache updated data');
    return false;
   }

   $this->report_info('Data updated in both cache and base data storage');
   return true;
  }


  public function delete($type, $crit) {
   $this->report_info('`delete` action requested');
   if(! $this->_get_cache_storage()->delete($type, $crit)) {
    $this->report_warning('Failed to delete data from cache data storage');
   }

   if(! $this->_get_base_data_storage()->delete($type, $crit)){
    $this->report_error('Failed to delete data from base data storage');
    return false;
   }

   $this->report_info('Data deleted from base and cache data storages');
   return true;
  }


  public function set_base_data_storage($_) {$this->_base_data_storage=$_;}
  public function set_cache_data_storage($_) {$this->_cache_data_storage=$_;}

  // XXX robustness and incorrect use case handling
  private function _build_cache_uid($crit, $uid){
   foreach($crit as $k => $v){
    if(is_null($v)) {
     $crit->$k = $uid;
     break;
    }
   }
   return $crit;
  }

  private function _get_base_data_storage() {return $this->_base_data_storage;}
  private function _get_cache_storage() {return $this->_cache_data_storage;}
 }
