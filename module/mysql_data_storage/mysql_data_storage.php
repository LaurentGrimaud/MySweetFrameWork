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
  protected $_m;
  protected $_statement_prefix = 'sql_statements';

  protected $_defaults = [
   'mysql:host'    => 'localhost',
   'mysql:port'    => 3306,
   'mysql:user'    => 'mysfw',
   'mysql:pass'    => 'mysfw',
   'mysql:db'      => 'mysfw',
   'mysql:charset' => 'utf8'
   ];

  protected function _get_ready() {
  }

  /** XXX TEMP **/
  public function sql_retrieve($statement) {
   if(! $sql = $this->inform($this->_statement_prefix.':'.$statement)) throw $this->except("No statement `$statement` found");
   $c = $this->_connect();
   return $this->_query_and_fetch($sql, $c);
  }

  public function sql_count($sql) {
   $c = $this->_connect();
   $r = $this->_query($c, $sql);
   $row = $r->fetch_row();
   return $row[0];
  }

  public function sql_query($sql, $k = null) {
   $c = $this->_connect();
   return $this->_query_and_fetch($sql, $c, $k);
  }

  public function sql_query_on_key($sql, $k) {
   $c = $this->_connect();
   return $this->_query_and_fetch_on_key($sql, $c, $k);
  }

  public function sql_exec($sql) {
   $c = $this->_connect();
   $this->_query($c, $sql);
   return $c->affected_rows;
  }

  // XXX Refactor needed
  public function retrieve($type, $crit = null, $metacrit = null, $fields = null, $ft_crit = null) { // XXX temp
   $this->report_info('`retrieve` action requested');
   $c = $this->_connect();
   if ($fields !== null && is_array($fields) && count($fields)) {
    $sql = 'SELECT ' . join(',', $fields) . " FROM $type ";
   } else {
    $sql = "SELECT * FROM $type ";
   }
   if($crit || $ft_crit) $sql .= $this->_criteria_talk($c, $crit, $ft_crit);
   if($metacrit){
    if(isset($metacrit['s']) and is_array($metacrit['s'])){
      $order_by= null;
      foreach( $metacrit['s'] as $field=>$sort){
        switch($sort){
         case -1:
           $order_by= sprintf('%s DESC',$field );
         break;
         case 1:
           $order_by= sprintf('%s ASC',$field );
         break;
        }
      }
      if( $order_by) $sql= sprintf('%s ORDER BY %s',$sql,$order_by);
    }
    if(isset($metacrit['l'])) {
     if(isset($metacrit['o'])) {
      $sql = sprintf('%s LIMIT %s, %s', $sql, $c->real_escape_string($metacrit['o']), $c->real_escape_string($metacrit['l']));
     }else{
      $sql = sprintf('%s LIMIT %s', $sql, $c->real_escape_string($metacrit['l']));
     }
    }
   }
   $result_hash = isset($metacrit['h']) ? $metacrit['h'] : null;
   return $this->_query_and_fetch($sql, $c, $result_hash);
  }

  public function count($type, $crit = null, $ft_crit = null) {
   $this->report_info('`count` action requested');
   $c = $this->_connect();
   $sql = "SELECT COUNT(*) FROM $type ";
   if($crit || $ft_crit) $sql .= $this->_criteria_talk($c, $crit, $ft_crit);
   return $this->sql_count($sql);
  }

  protected function _query_and_fetch($sql, $c, $k = null) {
   $r = $this->_query($c, $sql);

   $res = [];
   if (! $k) {
    while($row = $r->fetch_object()) {
     $res[] = $row;
    }
   } else {
    while($row = $r->fetch_object()) {
     $res[$row->$k] = $row;
    }
   }

   return $res;
  }

  protected function _query_and_fetch_on_key($sql, $c, $k) {
   $r = $this->_query($c, $sql);

   $res = [];
   while($row = $r->fetch_object()) {
    $res[$row->$k][] = $row;
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

  // XXX Meta criteria handling is missing
  public function delete($type, $crit, $metacrit = [], $ft_crit = []) {
   $this->report_info('`delete` action requested');
   $c = $this->_connect();

   $sql = "DELETE FROM $type {$this->_criteria_talk($c, $crit, $ft_crit)}";

   $this->_query($c, $sql);

   return $c->affected_rows;
  }

  // XXX temp
  protected function _connect() {
   if(! $this->_m || !$this->_m->ping()) {
    $this->_m = new \mysqli($this->inform('mysql:host'), $this->inform('mysql:user'), $this->inform('mysql:pass'), $this->inform('mysql:db'), $this->inform('mysql:port'));
    if($this->_m->connect_errno) {
     throw $this->except("Failed to connect to mysql data storage. Message was: ".$this->_m->connect_error);
    }
    $this->_m->set_charset($this->inform('mysql:charset'));
   }

   return $this->_m;
  }

  /**
   * @throw mysfw\exception if query failed
   */
  protected function _query($c, $sql) {
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
     $sql .= "$s$k = NULL";
    }else{
     $sql .= "$s$k = '{$c->real_escape_string($v)}'";
    }
    $s = ', ';
   }
   return $sql;
  }

  private function _criteria_talk($c, $o, $ft = null) {
   $sql = 'WHERE ';
   $s = '';
   foreach($o as $k => $v){
    if(null === $v) {
     $sql .= "$s$k IS NULL";
    }else{
     $sql .= "$s$k = '{$c->real_escape_string($v)}'";
    }
    $s = ' AND ';
   }

   if($ft) {
    foreach($ft as $k => $v) {
     $sql .= "$s$k LIKE '%{$c->real_escape_string($v)}%'";
     $s = ' AND ';
    }
   }

   return $sql;
  }
 }
