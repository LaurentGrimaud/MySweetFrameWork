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

  final public function test_pop_interface(){
   $module_name = "WunderbarModule";
   $returned_object = (object)["property" => "value"];
   $p = $this->getMockBuilder('t0t1\mysfw\frame\popper')->disableOriginalConstructor()->getMock();
   $p->expects($this->any())
    ->method('pop')
    ->with($module_name)
    ->will($this->returnValue($returned_object));
   $this->x->set_popper($p);
   $this->assertSame($this->x->pop($module_name), $returned_object);
  }

  final public function test_pop_interface_with_conf_context(){
   $module_name = "ShöneModule";
   $conf_context = "tricky";
   $returned_object = (object)["property" => "value"];
   $p = $this->getMockBuilder('t0t1\mysfw\frame\popper')->disableOriginalConstructor()->getMock();
   $p->expects($this->any())
    ->method('pop')
    ->with($module_name, $conf_context)
    ->will($this->returnValue($returned_object));
   $this->x->set_popper($p);
   $this->assertSame($this->x->pop($module_name, $conf_context), $returned_object);
  }

  final public function test_indicate_interface(){
   $module_name = "ModuleDeMesReves";
   $returned_object = (object)["property" => "LA valeur"];
   $p = $this->getMockBuilder('t0t1\mysfw\frame\popper')->disableOriginalConstructor()->getMock();
   $p->expects($this->any())
    ->method('indicate')
    ->with($module_name)
    ->will($this->returnValue($returned_object));
   $this->x->set_popper($p);
   $this->assertSame($this->x->indicate($module_name), $returned_object);
  }



 }

?>
