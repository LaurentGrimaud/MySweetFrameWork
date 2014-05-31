<?php
 use t0t1\mysfw\module;
 use t0t1\mysfw\frame;
/**
 * XXX mysfw_view::reveal() tests to be completed
 */

 // XXX temp
 require_once '_unit_tests/unit_testing_init.php';
 $ut_initializer = new unit_testing_initializer();
 $ut_initializer->load('frame/contract/view.php');
 $ut_initializer->load('module/view/view.php');


 class mysfw_view_Test extends PHPUnit_Framework_TestCase {

  public function setUp() {
   $this->x = new  module\view;
   $mocked_popper = $this->getMock('t0t1\mysfw\frame\contract\popper');
   $mocked_configurator = $this->getMock('t0t1\mysfw\frame\contract\configurator');
  // $this->init_configurator($mocked_configurator);
   $this->x->set_popper($mocked_popper);
   $this->x->set_configurator($mocked_configurator);
   $this->x->get_ready();
  }

  public function test_init() {
   $this->assertNull($this->x->get('gni'));
   return $this->x;
  }

  /**
   * @depends test_init
   */
  public function test_set($v) {
   $v->set('gni', 'gna');
   $this->assertEquals('gna', $v->get('gni'));
   return $v;
  }

  /**
   * @depends test_set
   */
  public function test_reset($v) {
   $v->set('gni', 'gno');
   $this->assertEquals('gno', $v->get('gni'));
   return $v;
  }

  /**
   * @depends test_reset
   */
  public function test_reinit($v) {
   $v->set('gni', null);
   $this->assertNull($v->get('gni'));
   return $v;
  }

  /**
   */
  public function test_set_all() {
   $d = array('un' => 'un', 'deux' => 'deux');
   $this->x->set_all($d);
   $this->assertEquals($this->x->get_all(), $d);
  }



  /**
   * @expectedException PHPUnit_Framework_Error_Warning
   * @expectedMessage include(non/existing/tmpl.tmpl.php): failed to open stream: No such file or directory 
   */
  public function test_error_on_non_existing_tmpl() {
   $this->x->reveal('non/existing/tmpl');
  }

 }
