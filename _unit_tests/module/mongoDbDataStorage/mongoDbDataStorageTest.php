<?php
 use t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 // XXX temp
 require_once '../../unit_testing_init.php';
 $ut_initializer = new unit_testing_initializer();
 $ut_initializer->load('t0t1/mysfw/frame/contract/data_storage.php');
 $ut_initializer->load('t0t1/mysfw/module/mongodb_data_storage/mongodb_data_storage.php');
 $ut_initializer->load('t0t1/mysfw/module/data_storage/exception/too_many_entries.php');
 $ut_initializer->load('t0t1/mysfw/module/data_storage/exception/no_entry.php');
 $ut_initializer->load('t0t1/mysfw/module/data_storage/exception/db_failure.php');
 $ut_initializer->load('t0t1/mysfw/module/data_storage/exception/duplicate_key.php');
 $ut_initializer->load('t0t1/mysfw/module/data_storage/exception/connection_failure.php');
 $ut_initializer->load('t0t1/mysfw/module/data_storage/exception/data_storage_exception.php');
 $ut_initializer->load('t0t1/mysfw/module/data_storage/exception/invalid_parameters.php');
 $ut_initializer->load('t0t1/mysfw/module/data_storage/exception/no_entry.php');
 $ut_initializer->load('t0t1/mysfw/module/data_storage/exception/wrong_key.php');

 class mongoDbDataStorageTest extends PHPUnit_Framework_TestCase {
  protected $_x;
  protected $_mongo;

  public function setUp() {
   $this->_x = new module\mongodb_data_storage;
   $mocked_popper = $this->getMock('t0t1\mysfw\frame\contract\popper');
   $this->_x->set_popper($mocked_popper);
   $configurator = $this->getMock('t0t1\mysfw\frame\contract\configurator');
   $this->_x->set_configurator($configurator);
   $this->_x->get_ready();
   // test mongo db creation, will be destroy on tearDown function
   $m = new \MongoClient();
   $this->_mongo = $m->selectDB('phpunit_tests');
  }
  
  
  /**
   * @expectedException t0t1\mysfw\module/data_storage\connection_failure exception
   */
  public function test_wrong_connection_data(){
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue('test_serveur'));
    $this->_x->get_connection('users');    
  }
  
  public function test_successfull_mongodb_connection(){
    $this->_mongo->createCollection('users');
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $obj = $this->_x->get_connection('users');
    $this->assertTrue($obj instanceof \MongoCollection); 
  }
  
  public function tearDown(){
    $this->_mongo->drop();
    unset( $this->_mongo);
}
  
  public function test_empty_retrieve(){
    $this->_mongo->createCollection('users');
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $crit = ['name'=>'TEST'];
    $result = $this->_x->retrieve('users', $crit);
    $this->assertEquals($result, []);        
  }
  
    public function test_successfull_retrieve(){
    $c = $this->_mongo->createCollection('users');
    $document = ["firstname"=>"TEST", "lastname"=>"TEST2", "age"=>25];
    $c->save( $document);
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $crit = ['firstname'=>'TEST'];
    $result = $this->_x->retrieve('users', $crit);
    $this->assertNotEmpty($result);
    $this->assertEquals($result[0]['lastname'], 'TEST2');        
  }
  
  public function test_successfull_add_without_uid(){
    $configurator = $this->_x->get_configurator();
    $c = $this->_mongo->createCollection('users');
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $document = ["firstname"=>"TEST", "lastname"=>"TEST2", "age"=>25];
    $result = $this->_x->add('users', null, $document);
    $this->assertNotEmpty($result);
    $this->assertTrue($result instanceof \MongoId);
  }
  
  public function test_successfull_add_with_uid(){
    $configurator = $this->_x->get_configurator();
    $c = $this->_mongo->createCollection('users');
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $document = ["_id"=>"12345", "firstname"=>"TEST", "lastname"=>"TEST2", "age"=>25];
    $result = $this->_x->add('users', null, $document);
    $this->assertNotEmpty($result);
    $this->assertEquals($result, "12345");
  }
  
  /**
   * @expectedException t0t1\mysfw\module/data_storage\wrong_parameters exception
   */
  public function test_add_with_wrong_uid(){
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $c = $this->_mongo->createCollection('users');
    $document = ["_id"=>NULL, "firstname"=>"TEST", "lastname"=>"TEST2", "age"=>25];
    $result = $this->_x->add('users', null, $document);
  }
  
  /**
   * @expectedException t0t1\mysfw\module/data_storage\duplicate_key exception
   */
  public function test_add_with_duplicate_key(){
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $c = $this->_mongo->createCollection('users');
    $document1 = ["_id"=>"12345", "firstname"=>"TEST", "lastname"=>"TEST2", "age"=>25];
    $result = $this->_x->add('users', null, $document1);
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $document2 = ["_id"=>"12345", "firstname"=>"TEST1", "lastname"=>"TEST3", "age"=>30];
    $result = $this->_x->add('users', null, $document2);
  }
  
  /**
   * @expectedException t0t1\mysfw\module/data_storage\data_storage_exception
   */
  public function test_change_without_uid(){
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $c = $this->_mongo->createCollection('users');
    $document1 = ["_id"=>"12345", "firstname"=>"TEST", "lastname"=>"TEST2", "age"=>25];
    $result = $this->_x->add('users', null, $document1);
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $crit = [];
    $new = ['lastname'=>"GRRRRRRRR"];
    $result = $this->_x->change('users', $crit, $new);
  }
  
  /**
   * @expectedException t0t1\mysfw\module/data_storage\no_entry exception
   */
  public function test_change_with_unknown_uid(){
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $c = $this->_mongo->createCollection('users');
    $document1 = ["_id"=>"12345", "firstname"=>"TEST", "lastname"=>"TEST2", "age"=>25];
    $result = $this->_x->add('users', null, $document1);
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $crit = ['_id'=>'123'];
    $new = ['lastname'=>"GRRRRRRRR"];
    $result = $this->_x->change('users', $crit, $new);
  }
  
  public function test_successfull_change(){
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $c = $this->_mongo->createCollection('users');
    $document1 = ["_id"=>"12345", "firstname"=>"TEST", "lastname"=>"TEST2", "age"=>25];
    $result = $this->_x->add('users', null, $document1);
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $crit = ['_id'=>'12345'];
    $new = ['lastname'=>"GRRRRRRRR"];
    $result = $this->_x->change('users', $crit, $new);
    $this->assertTrue( $result, true);
  }
  
  public function test_successfull_delete(){
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $c = $this->_mongo->createCollection('users');
    $document1 = ["_id"=>"12345", "firstname"=>"TEST", "lastname"=>"TEST2", "age"=>25];
    $result = $this->_x->add('users', null, $document1);
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $crit = ['_id'=>'12345'];
    $result = $this->_x->delete('users', $crit);
    $this->assertEquals( $result, 1);
  }
  
  /**
   * @expectedException t0t1\mysfw\module/data_storage\no_entry exception
   */
    public function test_delete_with_unknown_uid(){
    $configurator = $this->_x->get_configurator();
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $c = $this->_mongo->createCollection('users');
    $document1 = ["_id"=>"12345", "firstname"=>"TEST", "lastname"=>"TEST2", "age"=>25];
    $result = $this->_x->add('users', null, $document1);
    $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:pass')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(5))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(7))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
    $configurator->expects($this->at(8))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    $crit = ['_id'=>'123'];
    $result = $this->_x->delete('users', $crit);
  }
 }
