<?php

 require_once 'substructure/mysfw_dna.interface.php';
 require_once 'substructure/mysfw_reporter.interface.php';
 require_once 'substructure/mysfw_popper.interface.php';

 require_once 'substructure/mysfw_core.class.php';

 /** Just to test the underlaying abstract class ... **/
 class mysfw_core_instance extends mysfw_core {
 }

 class mysfw_core_Test extends PHPUnit_Framework_TestCase {

  public function setUp(){
   $this->x = new mysfw_core_instance;
  }

  final public function test_define_failure_when_configurator_not_set() {
   if(in_array('mysfw_configurator', class_implements($this->x))) {
    $this->assertNull($this->x->define('fjfjgjgjgj', 'lsldjfkfkfk'));
   }else{
    $this->assertFalse($this->x->define('fjfjgjgjgj', 'lsldjfkfkfk'));
   }
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
   $p = $this->getMock('mysfw_popper');
   $this->x->set_popper($p);
  }

 }

?>
