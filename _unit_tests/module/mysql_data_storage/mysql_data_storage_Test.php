<?php
 use t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 /**
  * XXX To test multi-values and multi-criteria SQL statements
  * XXX To test all exceptions potentially raised
  * XXX To test _all_ data needing escaping
  * XXX _get_ready() is out in coverage report. Why ?
  */

 // XXX temp
 require_once '_unit_tests/unit_testing_init.php';
 $ut_initializer = new unit_testing_initializer();
 $ut_initializer->load('frame/contract/data_storage.php');
 $ut_initializer->load('module/mysql_data_storage/mysql_data_storage.php');


 class mysql_data_storage_fake extends module\mysql_data_storage {
  protected function _get_ready() {
  }

  public function set_mysqli($x){$this->_m = $x;return $this;}
  public function get_mysqli(){return $this->_m;}
 }

 class mysql_data_storage_Test extends PHPUnit_Framework_TestCase {
  protected $_x;


  public function setUp() {
   $this->_x = new mysql_data_storage_fake;
   $mocked_popper = $this->getMock('t0t1\mysfw\frame\contract\popper');
   $this->_x->set_popper($mocked_popper);
   $this->_x->set_configurator($this->getMock('t0t1\mysfw\frame\contract\configurator'));
   $this->_x->get_ready();
   $this->_x->set_mysqli($this->getMock('FakeMysqli', ['real_escape_string', 'query']));
  }


  public function retrieve_data_provider() {
   return [
       ['table', 'property', 'value', null,"SELECT * FROM table WHERE property = 'value'"]
     , ['table', 'property', null, null, "SELECT * FROM table WHERE property IS NULL"]
     , ['table', 'property', 'strange value', ['s' => ['sort' => 1]], "SELECT * FROM table WHERE property = 'strange value' ORDER BY sort ASC"]
     , ['table', 'incredible_property', 'beautiful value', ['s' => ['sort' => -1]], "SELECT * FROM table WHERE incredible_property = 'beautiful value' ORDER BY sort DESC"]
   ];
  }

  /**
   * @dataProvider retrieve_data_provider
   */
  public function test_retrieve($type, $property, $value, $metacrit, $query){

   $mmr = $this->getMock('FakeMysqliResult', ['fetch_object']);
   $mmr->expects($this->at(0))->method('fetch_object')->with()->will($this->returnValue([0 => (object)[$property => $value]]));
   $mmr->expects($this->at(1))->method('fetch_object')->with()->will($this->returnValue(false));

   $mm = $this->_x->get_mysqli();

   $mm->expects($this->any())
                          ->method('real_escape_string')
                          ->with($value)
                          ->will($this->returnValue($value));
   $mm->expects($this->any())
                          ->method('query')
                          ->with($query)
                          ->will($this->returnValue($mmr));

   $this->_x->set_mysqli($mm);

   $this->_x->retrieve($type, [$property => $value], $metacrit);
  }


  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage mysql query `SELECT * FROM table WHERE property = 'value'` failed with message: mysqli error message
   */
  public function test_retrieve_with_failed_query(){
   $type = 'table';
   $prop = 'property';
   $value = 'value';
   $query = "SELECT * FROM $type WHERE $prop = '$value'";
   $query_error = 'mysqli error message';

   $mm = $this->_x->get_mysqli();
   $mm->expects($this->once())
                          ->method('real_escape_string')
                          ->with("value")
                          ->will($this->returnValue($value));
   $mm->expects($this->once())
                          ->method('query')
                          ->will($this->returnValue(false));

   $mm->error = $query_error;

   $this->_x->set_mysqli($mm);

   $this->_x->retrieve($type, [$prop => $value]);
  }


  public function add_data_provider() {
   return [
      ['table', 'property', 'value', null,"INSERT INTO table SET property = 'value'"]
    , ['table', 'property', null, null,"INSERT INTO table SET property = NULL"]
   ];
  }


  /**
   * @dataProvider add_data_provider
   */
  public function test_add($type, $property, $value, $metacrit, $query){
   $mm = $this->_x->get_mysqli();
   $mm->expects($this->any())
                          ->method('real_escape_string')
                          ->with($value)
                          ->will($this->returnValue($value));
   $mm->expects($this->any())
                          ->method('query')
                          ->with($query)
                          ->will($this->returnValue(true));
   $mm->insert_id = 4;

   $this->_x->set_mysqli($mm);

   $this->assertEquals(4, $this->_x->add($type, null, [$property => $value]));
  }


  public function change_data_provider() {
   return [
        ['table', ['property' => 'value'], ['other_property' => 'other value'], "UPDATE table SET other_property = 'other value' WHERE property = 'value'", 1]
      , ['table', ['property' => 'value'], ['other_property' => 'other value'], "UPDATE table SET other_property = 'other value' WHERE property = 'value'", 0]
      , ['table', ['property' => 'value'], ['other_property' => 'other value'], "UPDATE table SET other_property = 'other value' WHERE property = 'value'", 2]
      , ['table', ['property' => 'value', 'second_property' => 'second value'], ['other_property' => 'other value'], "UPDATE table SET other_property = 'other value' WHERE property = 'value' AND second_property = 'second value'", 2]
   ];
  }


  /**
   * @dataProvider change_data_provider
   */
  public function test_change($type, $criteria, $values, $query, $affected_rows){
   $mm = $this->_x->get_mysqli();

   $call = 0;
   foreach($values as $k => $v) {
    $mm->expects($this->at($call))
                          ->method('real_escape_string')
                          ->with($v)
                          ->will($this->returnValue($v));
    $call++;
   }
   foreach($criteria as $k => $v) {
    $mm->expects($this->at($call))
                          ->method('real_escape_string')
                          ->with($v)
                          ->will($this->returnValue($v));
    $call++;
   }
   $mm->expects($this->any())
                          ->method('query')
                          ->with($query)
                          ->will($this->returnValue(true));
   $mm->affected_rows = $affected_rows;

   $this->_x->set_mysqli($mm);

   $this->assertEquals($affected_rows, $this->_x->change($type, $criteria, $values));
  }

 public function delete_data_provider() {
   return [
        ['table', ['property' => 'value'], "DELETE FROM table WHERE property = 'value'", 1]
      , ['table', ['property' => 'value', 'other_property' => 'other value'], "DELETE FROM table WHERE property = 'value' AND other_property = 'other value'", 0]
   ];
  }


  /**
   * @dataProvider delete_data_provider
   */
  public function test_delete($type, $criteria, $query, $affected_rows){
   $mm = $this->_x->get_mysqli();

   $call = 0;
   foreach($criteria as $k => $v) {
    $mm->expects($this->at($call))
                          ->method('real_escape_string')
                          ->with($v)
                          ->will($this->returnValue($v));
    $call++;
   }
   $mm->expects($this->any())
                          ->method('query')
                          ->with($query)
                          ->will($this->returnValue(true));
   $mm->affected_rows = $affected_rows;

   $this->_x->set_mysqli($mm);

   $this->assertEquals($affected_rows, $this->_x->delete($type, $criteria));
  }

 public function sql_retrieve_data_provider() {
   return [
        ['select_hahah', "SELECT * FROM wouhouhou WHERE hahah != 'yes'", "huge_property", "tiny value"]
   ];
 }

  /**
   * @dataProvider sql_retrieve_data_provider
   */
  public function test_sql_retrieve($statement_name, $statement, $property, $value) {
   $this->_x->get_configurator()
    ->expects($this->any())
    ->method('inform')
    ->with("sql_statements:$statement_name")
    ->will($this->returnValue($statement));

   $mmr = $this->getMock('FakeMysqliResult', ['fetch_object']);
   $mmr->expects($this->at(0))->method('fetch_object')->with()->will($this->returnValue([0 => (object)[$property => $value]]));
   $mmr->expects($this->at(1))->method('fetch_object')->with()->will($this->returnValue(false));

   $mm = $this->_x->get_mysqli();

   $mm->expects($this->any())
                          ->method('query')
                          ->with($statement)
                          ->will($this->returnValue($mmr));

   $this->_x->set_mysqli($mm);

   $this->_x->sql_retrieve($statement_name);
  } 
}
