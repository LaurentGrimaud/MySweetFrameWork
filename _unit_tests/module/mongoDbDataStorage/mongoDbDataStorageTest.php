<?php
use t0t1\mysfw\module;
use t0t1\mysfw\frame;

// XXX temp
require_once '_unit_tests/unit_testing_init.php';
$ut_initializer = new unit_testing_initializer();
$ut_initializer->load('frame/contract/data_storage.php');
$ut_initializer->load('module/mongodb_data_storage/mongodb_data_storage.php');
$ut_initializer->load('module/data_storage/exception/too_many_entries.php');
$ut_initializer->load('module/data_storage/exception/no_entry.php');
$ut_initializer->load('module/data_storage/exception/db_failure.php');
$ut_initializer->load('module/data_storage/exception/duplicate_key.php');
$ut_initializer->load('module/data_storage/exception/connection_failure.php');
$ut_initializer->load('module/data_storage/exception/data_storage_exception.php');
$ut_initializer->load('module/data_storage/exception/invalid_parameters.php');
$ut_initializer->load('module/data_storage/exception/no_entry.php');
$ut_initializer->load('module/data_storage/exception/wrong_key.php');
$ut_initializer->load('module/stream_pool/stream_pool.php');
$ut_initializer->load('module/stream_pool/exception/invalid_parameters.php');

class mongoDbDataStorageTest extends PHPUnit_Framework_TestCase {
    protected $_x;
    protected $_mongo;

    public function setUp() {
        $m = new \MongoClient();
        $this->_mongo = $m->selectDB('phpunit_tests');
        $this->_x = new module\mongodb_data_storage;
        $mocked_popper = $this->getMock('t0t1\mysfw\frame\contract\popper');
        $this->_x->set_popper($mocked_popper);
        $configurator = $this->getMock('t0t1\mysfw\frame\contract\configurator');
        $this->_x->set_configurator($configurator);
        $this->_x->get_ready();
        $pool = new module\stream_pool;
        $this->_x->set_pool($pool); //XXX used to get the test working, need to look for a better way to do it
    }

    public function tearDown(){
        if($this->_mongo) $this->_mongo->drop();
        unset( $this->_mongo);
    }


    public function documentProvider(){
        return array(
                array(
                    array(
                        "firstname"=>"TEST",
                        "lastname"=>"TEST2",
                        "age"=>25
                    )
                )
            );
    }

    public function uidedDocumentProvider(){
        return array(
                array(
                    array(
                        "_id"=>"12345",
                        "firstname"=>"TEST",
                        "lastname"=>"TEST2",
                        "age"=>25
                    )
                )
            );
    }

    public function badUidedDocumentProvider(){
        return array(
                array(
                    array(
                        "_id"=>null,
                        "firstname"=>"TEST",
                        "lastname"=>"TEST2",
                        "age"=>25
                    )
                )
            );
    }

    public function reset(){
        $configurator= $this->_x->get_configurator();
        $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
        $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue(''));
        $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:port')
                 ->will($this->returnValue(''));
        $configurator->expects($this->at(3))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
        $configurator->expects($this->at(4))
                 ->method('inform')
                 ->with('mongo:db')
                 ->will($this->returnValue('phpunit_tests'));
    }

    /**
     * @expectedException \t0t1\mysfw\module\data_storage\exception\connection_failure
     */

    public function test_connection_failure(){
        $configurator= $this->_x->get_configurator();
        $configurator->expects($this->at(0))
                 ->method('inform')
                 ->with('mongo:user')
                 ->will($this->returnValue(''));
        $configurator->expects($this->at(1))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue('non_existent_server'));
        $configurator->expects($this->at(2))
                 ->method('inform')
                 ->with('mongo:host')
                 ->will($this->returnValue('non_existent_server'));
        $this->_x->get_connection('users');
    }


    public function test_successfull_connection(){
        $this->reset();
        $obj = $this->_x->get_connection('users');
        $this->assertTrue($obj instanceof \MongoCollection);
    }

    /**
     * @depends test_successfull_connection
     */

    public function test_empty_retrieve(){
        $this->reset();
        $crit = array('name'=>'TEST');
        $result = $this->_x->retrieve('users', $crit);
        $this->assertEquals(count($result), 0);
    }

    /**
     * @depends test_successfull_connection
     * @dataProvider documentProvider
     */

    public function test_successfull_retrieve($document){
        $this->reset();
        $c = $this->_mongo->createCollection('users');
        $c->save($document);
        $crit = array('firstname'=>'TEST');
        $results = $this->_x->retrieve('users', $crit);
        $result= current($results);
        $this->assertNotEmpty($result);
        $this->assertEquals($result['lastname'], 'TEST2');
    }

    /**
     * @depends test_successfull_connection
     * @dataProvider documentProvider
     */

    public function test_successfull_add_without_uid($document){
        $this->reset();
        $c = $this->_mongo->createCollection('users');
        $result = $this->_x->add('users', null, $document);
        $this->assertNotEmpty($result);
        $this->assertTrue($result instanceof \MongoId);
    }

    /**
     * @depends test_successfull_connection
     * @dataProvider uidedDocumentProvider
     */

    public function test_successfull_add_with_uid($document){
        $this->reset();
        $c = $this->_mongo->createCollection('users');
        $result = $this->_x->add('users', null, $document);
        $this->assertNotEmpty($result);
        $this->assertEquals($result, $document['_id']);
    }

    /**
     * @depends test_successfull_connection
     * @dataProvider badUidedDocumentProvider
     * @expectedException t0t1\mysfw\module\data_storage\exception\data_storage_exception
     */

    public function test_add_with_wrong_uid($document){
        $this->reset();
        $c = $this->_mongo->createCollection('users');
        $this->_x->add('users', null, $document);
    }

    /**
     * @depends test_successfull_connection
     * @dataProvider uidedDocumentProvider
     * @expectedException t0t1\mysfw\module\data_storage\exception\duplicate_key
     */

    public function test_add_with_duplicate_key($document){
        $this->reset();
        $c = $this->_mongo->createCollection('users');
        $result = $this->_x->add('users', null, $document);
        $this->reset();
        $this->_x->add('users', null, $document);
    }

    /**
     * @depends test_successfull_connection
     * @dataProvider uidedDocumentProvider
     * @expectedException t0t1\mysfw\module\data_storage\exception\data_storage_exception
     *
     */

    public function test_change_without_uid($document){
        $this->reset();
        $c = $this->_mongo->createCollection('users');
        $result = $this->_x->add('users', null, $document);
        $this->reset();
        $crit = array();
        $new = array('lastname'=>"GRRRRRRRR");
        $this->_x->change('users', $crit, $new);
    }

    /**
     * @depends test_successfull_connection
     * @dataProvider uidedDocumentProvider
     * @expectedException t0t1\mysfw\module\data_storage\exception\no_entry
     */

    public function test_change_with_unknown_uid($document){
        $this->reset();
        $c = $this->_mongo->createCollection('users');
        $result = $this->_x->add('users', null, $document);
        $this->reset();
        $crit = array('_id'=>'unknown_uid');
        $new = array('lastname'=>"GRRRRRRRR");
        $this->_x->change('users', $crit, $new);
    }

    /**
     * @depends test_successfull_connection
     * @dataProvider uidedDocumentProvider
     */

    public function test_successfull_change($document){
        $this->reset();
        $c = $this->_mongo->createCollection('users');
        $result = $this->_x->add('users', null, $document);
        $this->reset();
        $crit = array('_id'=>$document['_id']);
        $new = array('lastname'=>"GRRRRRRRR");
        $result = $this->_x->change('users', $crit, $new);
        $this->assertTrue( $result, true);
    }

    /**
     * @depends test_successfull_connection
     * @dataProvider uidedDocumentProvider
     */

    public function test_successfull_delete($document){
        $this->reset();
        $c = $this->_mongo->createCollection('users');
        $result = $this->_x->add('users', null, $document);
        $this->reset();
        $crit = array('_id'=>$document['_id']);
        $result = $this->_x->delete('users', $crit);
        $this->assertEquals( $result, 1);
    }

    /**
     * @depends test_successfull_connection
     * @dataProvider uidedDocumentProvider
     * @expectedException t0t1\mysfw\module\data_storage\exception\no_entry
     */

    public function test_delete_with_unknown_uid($document){
        $this->reset();
        $c = $this->_mongo->createCollection('users');
        $result = $this->_x->add('users', null, $document);
        $this->reset();
        $crit = array('_id'=>'unknown_uid');
        $this->_x->delete('users', $crit);
    }
}
