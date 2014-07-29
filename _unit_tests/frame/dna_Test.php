<?php

 require_once 'frame/contract/dna.php';
 require_once 'frame/contract/reporter.php';
 require_once 'frame/contract/popper.php';

 require_once 'frame/dna.php';

 /** Just to test the underlaying abstract class ... **/
 class core_instance extends t0t1\mysfw\frame\dna {
 }

 class core_Test extends PHPUnit_Framework_TestCase {

  public function setUp(){
   $this->x = new core_instance;
  }

  final public function test_define_failure_when_configurator_not_set() {
    $this->assertNull($this->x->define('fjfjgjgjgj', 'lsldjfkfkfk'));
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  final public function test_incorrect_popper_injection() {
   $this->x->set_popper('hhh');
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  final public function test_incorrect_popper_injection_object() {
   $this->x->set_popper((object)array('djdjdjdjd' => 'dkskfjfj'));
  }

  final public function test_correct_type_popper_injection() {
   $p = $this->getMockBuilder('t0t1\mysfw\frame\popper')->disableOriginalConstructor()->getMock();
   $this->x->set_popper($p);
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   */
  final public function test_inform_no_configurator() {
   $this->x->inform('xxx');
  }
 }

?>
