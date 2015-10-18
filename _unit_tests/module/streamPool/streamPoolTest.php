<?php
use t0t1\mysfw\module;
use t0t1\mysfw\frame;

// XXX temp
require_once '_unit_tests/unit_testing_init.php';
$ut_initializer = new unit_testing_initializer();
$ut_initializer->load('module/stream_pool/stream_pool.php');
$ut_initializer->load('module/stream_pool/exception/invalid_parameters.php');

class streamPoolTest extends PHPUnit_Framework_TestCase {
    protected $_x;

    public function setUp() {
        $this->_x = new module\stream_pool;
        $mocked_popper = $this->getMock('t0t1\mysfw\frame\contract\popper');
        $this->_x->set_popper($mocked_popper);
        $configurator = $this->getMock('t0t1\mysfw\frame\contract\configurator');
        $this->_x->set_configurator($configurator);
        $this->_x->get_ready();
    }

    public function invalid_dsn(){
        return array(
            array('this-is-not-a-valid-dsn'),
            array(0),
            array(null),
            array('//this-is-not-a-valid-dsn'),
        );
    }

    /**
     * @dataProvider invalid_dsn
     * @expectedException t0t1\mysfw\module\stream_pool\exception\invalid_parameters
     */

    public function test_invalid_dsn($dsn){
        $this->_x->get($dsn);
    }

    public function test_successfull_connection(){
        $connection= $this->_x->get('mongodb://localhost/phpunit_tests/users');
        $this->assertInstanceOf('\MongoClient',$connection);
    }

    /**
     * @depends test_successfull_connection
     */

    public function test_inserts(){
        $connection= $this->_x->get('mongodb://localhost/');
        $collection= $connection->selectCollection('phpunit_tests','users');
        for($i=0;$i<20;$i++){
            $collection->save(array("firstname"=>"TEST".$i,"lastname"=>"TEST".$i));
        }
    }

    /**
     * @depends test_successfull_connection
     */

    public function test_simultaneous_connections(){
        $connection1= $this->_x->get('mongodb://localhost/');
        $connection2= $this->_x->get('mongodb://localhost/');
        $collection1= $connection1->selectCollection('phpunit_tests','users');
        for($i=0;$i<20;$i++){
            $collection1->save(array("firstname"=>"f".$i,"lastname"=>"l".$i));
        }
        $collection2= $connection2->selectCollection('phpunit_tests','users2');
        for($i=0;$i<20;$i++){
            $collection2->save(array("firstname"=>"f".$i,"lastname"=>"l".$i));
        }
        foreach( $collection1->find() as $r){
            $this->assertNotEmpty($collection2->find(array('lastname',$r['lastname'])));
        }
    }


}
