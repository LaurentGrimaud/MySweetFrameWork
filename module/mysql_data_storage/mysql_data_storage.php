<?php
 /**
  * XXX Several concerns need to be separated:
  * ° SQL translation
  * ° mySQL querying
  * ° connections handling
  * This should be done once the `submodules` concept implemented
  */
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn("frame\contract\data_storage");

 class mysql_data_storage extends frame\dna implements frame\contract\data_storage, frame\contract\dna { 
  protected $_statement_prefix = 'sql_statements';
  protected $_defaults = [
   'mysql:host' => 'localhost',
   'mysql:port' => 3306,
   'mysql:user' => 'mysfw',
   'mysql:pass' => 'mysfw',
   'mysql:db'   => 'mysfw',
   ];


  /** XXX TEMP **/
  public function sql_retrieve($statement) {
   if(! $sql = $this->inform($this->_statement_prefix.':'.$statement)) throw $this->except("No statement `$statement` found");
   $c = $this->_connect();
   return $this->_query_and_fetch($sql, $c);
  }

  // XXX Refactor needed
  public function retrieve($type, $crit = null, $metacrit = null) {
   $this->report_info('`retrieve` action requested');
   $c = $this->_connect();

   $sql = "SELECT * FROM $type ";
   if($crit) $sql .= $this->_criteria_talk($c, $crit);
   return $this->_query_and_fetch($sql, $c);
  }

  protected function _query_and_fetch($sql, $c) {
   $r = $this->_query($c, $sql);

   $res = [];
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
   $this->_query($c, $sql);

   return $c->insert_id;
  }

  // XXX to be implemented
  public function change($type, $crit, $values) {
   $this->report_info('`change` action requested');
   $c = $this->_connect();

   $sql = "UPDATE $type {$this->_values_talk($c, $values)} {$this->_criteria_talk($c, $crit)}";

   $this->_query($c, $sql);

   return $c->affected_rows;
  }

  // XXX to be implemented
  public function delete($type, $crit) {
   $this->report_info('`delete` action requested');
   $c = $this->_connect();

   $sql = "DELETE from $type {$this->_criteria_talk($c, $crit)}";

   $this->_query($c, $sql);

   return $c->affected_rows;
  }

  // XXX temp
  private function _connect() {
   $c = new \mysqli($this->inform('mysql:host'), $this->inform('mysql:user'), $this->inform('mysql:pass'), $this->inform('mysql:db'), $this->inform('mysql:port'));
   if($c->connect_errno) {
    throw $this->except("Failed to connect to mysql data storage. Message was: ".$c->connect_error);
   }
   return $c;
  }

  /**
   * @throw mysfw\exception if query failed
   */
  private function _query($c, $sql) {
   $this->report_debug("Will execute: $sql"); // XXX test
   if(false === $res = $c->query($sql)){
    $this->report_error("mySQL error: ".$c->error); // XXX test
    throw $this->except("mysql query `$sql` failed with message: ".$c->error);
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
