<?php
 use t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 // XXX temp
 require_once '_unit_tests/unit_testing_init.php';
 $ut_initializer = new unit_testing_initializer();
 $ut_initializer->load('frame/contract/data_storage.php');
 $ut_initializer->load('module/operator/operator.php');
 $ut_initializer->load('module/operator/exception/too_many_entries.php');
 $ut_initializer->load('module/operator/exception/no_entry.php');

 class operatorTest extends PHPUnit_Framework_TestCase {
  protected $_x;

  public function init_operator() {
   $this->_x->get_configurator()
    ->expects($this->any())
    ->method('inform')
    ->will($this->returnValue(['_id_' => null]));
  }

  public function setUp() {
   $this->_x = new module\operator;
   $mocked_popper = $this->getMock('t0t1\mysfw\frame\contract\popper');
   $mocked_data_storage = $this->getMock('t0t1\mysfw\frame\contract\data_storage');
   $mocked_popper->expects($this->any())
	   ->method('indicate')
	   ->with('data_storage')
	   ->will($this->returnValue($mocked_data_storage));
   $this->_x->set_popper($mocked_popper);
   $this->_x->set_configurator($this->getMock('t0t1\mysfw\frame\contract\configurator'));
   $this->_x->get_ready();
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage No definitions available
   */
  public function test_undefined_operator() {
   $this->_x->morph('an undefined operator');
  }

  public function test_defined_operator() {
   $operator_name = "a defined operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
  }
 
  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage To morph an already morphed object is forbidden
   */
  public function test_already_defined_operator() {
   $operator_name = "an already defined operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $this->_x->morph($operator_name);
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage UID part `_id_` already valued (to `1234`)
   */
  public function test_identify_an_already_identified_property() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->identify('_id_', 1234);
   $this->_x->identify('_id_', 34567);
  }

  public function test_simple_create() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $mocked_data_storage->expects($this->once())
	   ->method('add')
	   ->with($operator_name, ['_id_' => 1234], ['_id_' => 1234, 'name' => 'Roger'])
	   ->will($this->returnValue(1234));
   $this->_x->set('_id_', 1234)->set('name', 'Roger')->create();
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage `create` action requested on UIDed `operator` object (type is `a nice operator`) 
   */
  public function test_create_error_if_already_created() {
   $this->test_simple_create(); // XXX tests inter-dependency
   $this->_x->create();
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage `create` action requested on UIDed `operator` object 
   */
  public function test_create_on_identified_operator() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $this->_x->identify('_id_', 1234)->create();
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage No (or bad) uid value `` returned by data storage add() action 
   */
  public function test_create_with_bad_uid() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $mocked_data_storage->expects($this->once())
	   ->method('add')
	   ->with($operator_name, ['_id_' => 1234], ['_id_' => 1234, 'name' => 'Roger'])
	   ->will($this->returnValue(null));
   $this->_x->set('_id_', 1234)->set('name', 'Roger')->create();
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage UID part `_id_` already valued (to `1234`)
   */
  public function test_identify_an_already_identified_operator() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->identify('_id_', 1234);
   $this->_x->identify('_id_', 34567);
  }

  public function test_simple_update() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $mocked_data_storage->expects($this->once())
	   ->method('change')
	   ->with($operator_name, ['_id_' => 1234], ['name' => 'Roger'])
	   ->will($this->returnValue(1234));
   $this->_x->identify('_id_', 1234)->set('name', 'Roger')->update();
   $this->assertEquals($this->_x->get_new(), []);
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage `update` action requested on unidentified `operator` object (type is `a nice operator`)
   */
  public function test_update_while_unidentified() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $this->_x->set('name', 'Roger')->update();
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage `change` action failed in underlaying data storage
   */
  public function test_update_with_data_storage_error() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $mocked_data_storage->expects($this->once())
	   ->method('change')
	   ->will($this->returnValue(false));
   $this->_x->identify('_id_', 1234)->set('name', 'Roger')->update();
   $this->assertEquals($this->_x->get_new(), []);
  }


  public function test_recall_on_uid() {
   $operator_name = "a nice operator";
   $operator_values = ['_id_' => 1234, 'name' => 'Roger'];
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $mocked_data_storage->expects($this->once())
	   ->method('retrieve')
	   ->with($operator_name, ['_id_' => 1234], null)
	   ->will($this->returnValue([0 => $operator_values]));
   $this->_x->identify('_id_', 1234)->recall();
   $this->assertEquals($this->_x->get_values(), $operator_values);
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage `recall` action requested on un-UIDed `operator` object (type is `a nice operator`)
   */
  public function test_recall_while_unidentified() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $this->_x->recall();
  }

  /**
   * @expectedException t0t1\mysfw\module\operator\exception\too_many_entries
   * @expectedExceptionMessage Too many entries found in data storage
   */
  public function test_recall_with_too_manies_return_values() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $mocked_data_storage->expects($this->once())
	   ->method('retrieve')
	   ->with($operator_name, ['_id_' => 1234], null)
	   ->will($this->returnValue([0 => ['_id' => 1], 1 => ['_id_' => 2]]));
   $this->_x->identify('_id_', 1234)->recall();
  }

  /**
   * @expectedException t0t1\mysfw\module\operator\exception\no_entry
   * @expectedExceptionMessage No matching entry found in data storage
   */
  public function test_recall_with_no_return_values() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $mocked_data_storage->expects($this->once())
	   ->method('retrieve')
	   ->with($operator_name, ['_id_' => 1234], null)
	   ->will($this->returnValue([]));
   $this->_x->identify('_id_', 1234)->recall();
  }

  public function test_recall_with_alternative_uid() {
   $operator_name = "a nice operator";
   $operator_values = ['_id_' => 567, 'name' => 'Roger', 'login' => 'CoolaMan', 'pass' => 'LoveMum'];
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $mocked_data_storage->expects($this->once())
	   ->method('retrieve')
	   ->with($operator_name, ['login' => 'CoolaMan', 'pass' => 'LoveMum'], null)
	   ->will($this->returnValue([0 => $operator_values]));
   $this->_x->identify('login', 'CoolaMan')->identify('pass', 'LoveMum')->recall();
   $this->assertEquals($this->_x->get_values(), $operator_values);
  }



  public function test_simple_erase() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $mocked_data_storage->expects($this->once())
	   ->method('delete')
	   ->with($operator_name, ['_id_' => 1234])
	   ->will($this->returnValue(true));
   $this->_x->identify('_id_', 1234)->erase();
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage No data to delete in underlaying data storage
   */
  public function test_erase_with_no_entries() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $mocked_data_storage = $this->_x->get_data_storage();
   $mocked_data_storage->expects($this->once())
	   ->method('delete')
	   ->with($operator_name, ['_id_' => 1234])
	   ->will($this->returnValue(0));
   $this->_x->identify('_id_', 1234)->erase();
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage Couldn't erase unUIDed object
   */
  public function test_erase_while_unidentified() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $this->_x->erase();
  }

  public function test_status() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $status = "Is alternatively uided ? false\nIs primary uided ? false\n";
   $this->assertEquals($status, $this->_x->status());
  }

  /******* Chainability tests - @XXX To be extended ! ******/

  public function test_identify_chainability() {
   $operator_name = "a nice operator";
   $this->init_operator($operator_name);
   $this->_x->morph($operator_name);
   $this->assertSame($this->_x->identify('login', 'CoolaMan'), $this->_x);
  }



 }
