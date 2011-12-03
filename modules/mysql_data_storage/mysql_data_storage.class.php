<?php

 class mysfw_mysql_data_storage extends mysfw_core implements mysfw_data_storage { 

  public function retrieve($type, $crit) {
   $this->report_info('`retrieve` action requested');
   $c = $this->_connect();

   $sql = "SELECT * FROM $type ".$this->_criteria_talk($c, $crit);

   if(!$r = $this->_query($c, $sql)) {
    return false;
   }

   while($row = $r->fetch_object()) {
    $res[] = $row;
   }

   return $res;
  }

  // XXX to be implemented
  public function add($type, $crit, $mysfw_data_object){
   $this->report_info('`add` action requested');
   $c = $this->_connect();

   $sql = "INSERT INTO $type ".$this->_values_talk($c, $mysfw_data_object);
   if(! $this->_query($c, $sql)) {
    $this->report_error("Failed to add data");
    return false;
   }

   return $c->insert_id;
  }

  // XXX to be implemented
  public function change($type, $crit, $values) {
   $this->report_info('`change` action requested');
   $c = $this->_connect();

   $sql = "UPDATE $type {$this->_values_talk($c, $values)} {$this->_criteria_talk($c, $crit)}";

   if(! $this->_query($c, $sql)) {
    return false;
   }

   return $c->affected_rows;
  }

  // XXX to be implemented
  public function delete($type, $crit) {
   $this->report_info('`delete` action requested');
   $c = $this->_connect();

   $sql = "DELETE from $type {$this->_criteria_talk($c, $crit)}";

   if(! $this->_query($c, $sql)) {
    return false;
   }

   return $c->affected_rows;
  }

  // XXX temp
  private function _connect() {
   return new mysqli('localhost', 'mysfw_test', 'grrrr', 'mysfw_test');
  }

  private function _query($c, $sql) {
   $this->report_debug("Will execute: $sql"); // XXX test
   if(false === $res = $c->query($sql)){
    $this->report_error("mySQL error: ".$c->error); // XXX test
    return false;
   }

   return $res;
  }

  private function _values_talk($c, $o) {
   $sql = 'SET ';
   $s = '';
   foreach($o as $k => $v){
    if(null === $v) {
     $sql .= "$s$k = null";
    }else{
     $sql .= "$s$k = '{$c->real_escape_string($v)}'";
    }
    $s = ', ';
   }
   return $sql;
  }

  private function _criteria_talk($c, $o) {
   $sql = 'WHERE ';
   $s = '';
   foreach($o as $k => $v){
    if(null === $v) {
     $sql .= "$s$k is null";
    }else{
     $sql .= "$s$k = '{$c->real_escape_string($v)}'";
    }
    $s = ' AND ';
   }
   return $sql;
  }



 }

?>
