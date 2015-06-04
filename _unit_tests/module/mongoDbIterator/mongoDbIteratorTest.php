<?php
 use t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 // XXX temp
 require_once '_unit_tests/unit_testing_init.php';
 $ut_initializer = new unit_testing_initializer();
 $ut_initializer->load('frame/contract/resource_iterator.php');
 $ut_initializer->load('module/mongodb_iterator/mongodb_iterator.php');
 $ut_initializer->load('module/resource_iterator/exception/invalid_parameters.php');

 class mongoDbIteratorTest extends PHPUnit_Framework_TestCase {
  protected $_x;

  public function setUp() {
   $this->_x = new module\mongodb_iterator;
   $mocked_popper = $this->getMock('t0t1\mysfw\frame\contract\popper');
   $this->_x->set_popper($mocked_popper);
   $configurator = $this->getMock('t0t1\mysfw\frame\contract\configurator');
   $this->_x->set_configurator($configurator);
   $this->_x->get_ready();
  }

  /**
   * @expectedException t0t1\mysfw\module\resource_iterator\exception\invalid_parameters
   */
  public function test_invalid_parameters_exception(){
    $this->_x->wrap(new StdClass);
  }
}
