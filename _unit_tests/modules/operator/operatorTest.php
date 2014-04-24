<?php
 use t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 // XXX temp

 class test_init {
  protected function _learn() {
  }

  public function __construct() {
   // XXX liste des dépendances
   require_once 'frame/contract/dna.php';
   require_once 'frame/contract/popper.php';
   require_once 'frame/contract/configurator.php';

   require_once 'frame/exception/dna.php';
   require_once 'frame/dna.php';
   require_once 'frame/popper.php';

   require_once 'module/configurator/configurator.php';

   require_once 'module/operator/operator.php';
  }
 }

$xxx = new test_init();


 class operatorTest extends PHPUnit_Framework_TestCase {
  protected $_x;

  public function init_operator() {
   $this->_x->get_configurator()
    ->expects($this->any())
    ->method('inform')
    ->will($this->returnValue(['operators:custom_definitions' => ['a defined operator' => ['_id_' => null]]]));
  }

  public function setUp() {
   $this->_x = new module\operator;
   $this->_x->set_popper($this->getMock('t0t1\mysfw\frame\contract\popper'));
   $this->_x->set_configurator($this->getMock('t0t1\mysfw\frame\contract\configurator'));
   $this->_x->get_ready();
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage No definition available
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

 }

