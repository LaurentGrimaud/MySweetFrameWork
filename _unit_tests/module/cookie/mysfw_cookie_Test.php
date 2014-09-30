<?php
 use t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 // XXX temp
 require_once '_unit_tests/unit_testing_init.php';
 $ut_initializer = new unit_testing_initializer();
 $ut_initializer->load('module/cookie/cookie.php');


 class mysfw_cookie_Test extends PHPUnit_Framework_TestCase {

  public function setUp() {
   $this->x = new  module\cookie;
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
   * @expectedException Exception
   * @expectedMessage Failed to set cookie with values : {"name":"gni","value":"gna","expire":null,"path":null,"domain":null,"secure":null,"http_only":null}
   */
  public function test_set($v) {
   $v->set('gni', 'gna');
   $this->assertEquals('gna', $v->get('gni'));
   return $v;
  }

  /**
   * @depends test_init
   * @expectedException Exception
   * @expectedMessage Failed to set cookie with values : {"name":"test","value":"test","expire":null,"path":null,"domain":null,"secure":null,"http_only":null}
   */
  public function test_delete($v) {
   $v->set('test','test');
   $v->delete('test');
   $this->assertNull($v->get('test'));
   return $v;
  }

 }
