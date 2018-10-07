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

  /*
   * Build left join clauses using data_storage definition
   *
   * XXX Should handle more cases for ON part
   [ [table => [
    a => table_alias,
    on => [
     [f => [a => table_alias, f => from_field], t => [a => table_alias, f => to_field], o => operator],
     [f => [a => table_alias, f => from_field], t => [a => table_alias, f => to_field], o => operator]
    ]
    ]
   ]]

   left join table2 t2 on (t1.f1 = t2.f2) left join table3 t3 on (t1.f3 = t3.f4)
   [
    [table2 => [
      a => t2
      on => [
        [ f => [a => t1, f => f1], t => [ a => t2, f => f2], o => '=']
      ]
    ],
    [table3 => [
      a => t3
      on => [
        [ f => [a => t1, f => f3], t => [ a => t3, f => f4], o => '=']
      ]
    ],


   ]
   ]
  */
  protected function _left_join_clause($left_joins = null) {
   if(! $left_joins) return '';
   if(!is_array($left_joins))
    throw $this->except('Invalid parameter for `left join` clause: '.json_encode($left_joins));

   $sql = '';
   foreach($left_joins as $table => $table_data) {
    $sql .= " LEFT JOIN `$table` AS `{$table_data['a']}` ON (";
    $sep = '';
    foreach($table_data['on'] as $on_data) {
     if(isset($on_data['t']['v'])) {
      $sql .= $sep.sprintf("`%s`.`%s`%s%s", $on_data['f']['a'], $on_data['f']['f'], $on_data['o'], $on_data['t']['v']);
     }else{
      $sql .= $sep.sprintf("`%s`.`%s`%s`%s`.`%s`", $on_data['f']['a'], $on_data['f']['f'], $on_data['o'], $on_data['t']['a'], $on_data['t']['f']);
     }
     $sep = ' AND ';
    }
    $sql .= ')';
   }

   return $sql;
  }

  protected function _from_clause($type) {
   if(is_string($type)) return " FROM `$type` ";
   if(is_array($type) && count($type) == 2) return " FROM `".join('` AS `', $type)."` ";
   throw $this->except('Invalid parameter for `from` clause: '.json_encode($type));
  }

  /*
   * $fields == null => all fields
   * $fields == numeric array starting at 0 => simple case, flat list
   * $fields == associative array => complex cases
   * $fields = [ [
   *  a => result alias,
   *  r => result is from a sub-retrieve,
   *  ed => result is from an external definition, treated as raw SQL fragment
   * ],
   * ...
   * ]
   */
  protected function _select_clause($c, $fields = null) {
   if(! $fields) return 'SELECT *';
   if(! is_array($fields))
    throw $this->except('Invalid parameter for `select` clause: '.json_encode($fields));

   // Simple case - flat array of pure column names
   if(is_string($fields[0]))
    return 'SELECT `'.join('`, `', $fields).'`';

   // Complex case
   // XXX some cases are mutually exclusive
   $sql = 'SELECT ';
   $sep = ' ';
   foreach($fields as $field) {
    if(isset($field['r'])) { // sub-retrieve
     $sql .= $sep.'('.$this->_select_statement($c, $field['r']).')';
    }
    if(isset($field['ed'])) { // external definition, ie raw SQL fragment
     $sql .= $sep.' '.$field['ed'];  // XXX Dangerous, SQL injection friendly
    }
    if(isset($field['f'])) { // field reference
     // * wildcard escaping bothers MariaDB (v10.1.26)
     if($field['f'] == '*') {
      $f_value = $field['f'];
     }else{
      $f_value = "`{$field['f']}`";
     }
     if(isset($field['t'])) { // table alias/name
      $sql .= "$sep`{$field['t']}`.$f_value";
     }else{
      $sql .= "$sep$f_value`";
     }
    }
    if(isset($field['a'])) { // column alias
     $sql .= " AS `{$field['a']}`";
    }
    $sep = ', ';
   }
   
   return $sql;
  }

  /*
   * Handles s and l meta-criteria
   * XXX missing column aliases 
   */
  protected function _metacrit($c, $metacrit = null) {
   $sql = '';
   if(! $metacrit) return $sql;

   // order by clause
   if(isset($metacrit['s']) and is_array($metacrit['s'])){
    $sql .= ' ORDER BY';
    $s = '';
    foreach($metacrit['s'] as $field => $sort){
     switch($sort){
      case -1:
       $sql .= "$s `$field` DESC";
       break;
      default:
       $sql .= "$s `$field` ASC";
       break;
     }
     $s = ',';
    }
   }

   // limit clause
   if(isset($metacrit['l'])) {
    if(isset($metacrit['o'])) {
     $sql .= ' LIMIT '.$c->real_escape_string($metacrit['o']).', '.$c->real_escape_string($metacrit['l']);
    }else{
     $sql .= ' LIMIT '.$c->real_escape_string($metacrit['l']);
    }
   }

   return $sql;
  }

  /*
   * Returns the full SELECT statement defined by `def`
   */
  protected function _select_statement($c, $def) {
   $sql = $this->_select_clause($c, isset($def['f']) ? $def['f'] : null);
   $sql .= $this->_from_clause(isset($def['t']) ? $def['t'] : null);
   $sql .= $this->_left_join_clause(isset($def['lj']) ? $def['lj'] : null);
   $sql .= $this->_criteria_talk($c, $def['t'], isset($def['c']) ? $def['c'] : null, isset($def['ft']) ? $def['ft'] : null, isset($def['rft']) ? $def['rft'] : null);
   $sql .= $this->_metacrit($c, isset($def['m']) ? $def['m'] : null);

   return $sql;
  }

  // XXX Refactor needed
  public function retrieve($type, $crit = null, $metacrit = null, $fields = null, $ft_crit = null, $rft_crit = null, $left_joins = null) { // XXX temp
   $this->report_info('`retrieve` action requested');
   $c = $this->_connect();

   // XXX Temp wrapping 'til refactoring of data_storage::retrieve() definition
   $def = [
    't'   => $type,
    'c'   => $crit,
    'm'   => $metacrit,
    'f'   => $fields,
    'ft'  => $ft_crit,
    'rft' => $rft_crit,
    'lj'  => $left_joins // XXX Bad key, too SQL-related
   ];

   $sql = $this->_select_statement($c, $def);

   // XXX Is result hashing a real meta-criterion ?
   $result_hash = isset($metacrit['h']) ? $metacrit['h'] : null;

   return $this->_query_and_fetch($sql, $c, $result_hash, (boolean)$left_joins);
  }

  public function count($type, $crit = null, $ft_crit = null, $rft_crit = null) {
   $this->report_info('`count` action requested');
   $c = $this->_connect();
   $sql = "SELECT COUNT(*) FROM `$type` ";
   if($crit || $ft_crit || $rft_crit) $sql .= $this->_criteria_talk($c, $type, $crit, $ft_crit, $rft_crit);
   return $this->sql_count($sql);
  }

  protected function _query_and_fetch($sql, $c, $k = null, $a = false) {
   $r = $this->_query($c, $sql);
 
   $res = [];

   if($a) {
    $defs = $r->fetch_fields();
    while($row = $r->fetch_row()) {
     $i = 0;
     $row_o = (object)[];
     foreach($row as $f) {
      if($defs[$i]->table) {
       $row_o->{$defs[$i]->table.'.'.$defs[$i]->orgname} = $f;
      }else{
       $row_o->{$defs[$i]->name} = $f;
      }
      $i++;
     }
     if($k) {
      $res[$row_o->$k] = $row_o;
     }else{
      $res[] = $row_o;
     }
    }
    return $res;
   }

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

   $sql = "INSERT INTO `$type` ".$this->_values_talk($c, $mysfw_data_object);
   $this->_query($c, $sql);

   return $c->insert_id;
  }

  // XXX to be implemented
  public function change($type, $crit, $values) {
   $this->report_info('`change` action requested');
   $c = $this->_connect();

   $sql = "UPDATE `$type` {$this->_values_talk($c, $values)} {$this->_criteria_talk($c, $type, $crit)}";

   $this->_query($c, $sql);

   return $c->affected_rows;
  }

  // XXX Meta criteria handling is missing
  public function delete($type, $crit, $metacrit = [], $ft_crit = []) {
   $this->report_info('`delete` action requested');
   $c = $this->_connect();

   $sql = "DELETE FROM `$type` {$this->_criteria_talk($c, $type, $crit, $ft_crit)}";

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

  // XXX Draft
  private function _criteria_operator($c, $table, $field, $value, $operator = '=', $f_alias = null, $t_field = null, $t_alias = null) {
   if(! $operator) $operator = '=';
   if(! in_array($operator, ['=', '<', '<=', '>', '>=', '!='])) throw $this->except('Invalid criteria operator: '.$operator);

   if($f_alias) {
    $from = " `$f_alias`.`$field`";
   }else{
    $from = " `$table`.`$field`";
   }

   if($t_field) {
    if($t_alias) {
     return "$from$operator`$t_alias`.`$t_field`"; 
    }
    return "$from$operator`$t_field`"; 
   }

   // XXX temp
   if(is_array($value)){
    if($operator != '=') throw $this->except('Invalid criteria operator for array value: '.$operator);
    return sprintf("$from IN (%s)", implode(', ', array_map($c->real_escape_string, $value)));
   }

   if($value === null){
    switch($operator) {
     case '=':
      return "$from IS NULL ";

     case '!=':
      return "$from IS NOT NULL ";
    }
   }

   return "$from $operator '{$c->real_escape_string($value)}'";
  }

  // XXX Draft
  private function _criteria_talk($c, $t, $o, $ft = null, $rft = null) {
   if(! $o && ! $ft && ! $rft) return '';
   $sql = ' WHERE';
   $s = ' ';
   if($o) {
    foreach($o as $k => $v){
     if(is_array($v)) {
      foreach($v as $vx){
       $sql .= $s.$this->_criteria_operator($c, $t, $k, @$vx['v'], @$vx['o'], @$vx['a'], @$vx['f'], @$vx['t']);
       $s = ' AND';
      }
     }else{
      if(null === $v) {
       $sql .= "$s `$t`.`$k` IS NULL";
      }else{
       $sql .= "$s `$t`.`$k` = '{$c->real_escape_string($v)}'";
      }
     }
     $s = ' AND';
    }
   }

   if($ft) {
    foreach($ft as $k => $v) {
     $sql .= "$s `$t`.`$k` LIKE '%{$c->real_escape_string($v)}%'";
     $s = ' AND';
    }
   }

   if($rft) {
    foreach($rft as $k => $v) {
     $sql .= $s." MATCH(`$t`.`$k`) AGAINST ('{$c->real_escape_string($v)}' IN BOOLEAN MODE)";
     $s = ' AND';
    }
   }

   return $sql;
  }
 }
