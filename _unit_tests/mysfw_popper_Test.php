<?php

 require_once 'popper.class.php';

 class mysfw_default_popper_Test extends PHPUnit_Framework_TestCase {

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function XXX_test_no_direct_intanciation(){
   $x = new mysfw_default_popper(__DIR__);
  }

  public function setUp(){
   $this->x = mysfw_default_popper::itself(__DIR__);
  }

  public function test_singletonisation(){
   $this->assertEquals(get_class($this->x), "mysfw_default_popper");

   $popper2 = mysfw_default_popper::itself(__DIR__);
   $this->assertEquals(get_class($popper2), "mysfw_default_popper");

   $this->assertSame($this->x, $popper2);
  }

  public function test_pop_returns_correct_object() {
   $v = $this->x->pop('view');
   $this->assertTrue(in_array('mysfw_view_interface', class_implements($v)));
   $v = $this->x->pop('redis_data_storage');
   $this->assertTrue(in_array('mysfw_data_storage', class_implements($v)));
  }

  /**
   * @dataProvider home_values
  **/
  public function test_set_and_get_home($h) {
   $this->x->set_home($h);
   $this->assertEquals($this->x->get_home(), $h);
  }

  public function home_values() {
   return array(
     array('gni'),
     array('gno'),
     array('gna')
     );
  }

  public function test_register() {
   $a = (object)array('hhhhh' => 'ffgfg', 'gbghbghbg' => 0);
   $this->x->register('a', $a);
   $this->assertSame($a, $this->x->indicate('a'));
  }

 }

?>
