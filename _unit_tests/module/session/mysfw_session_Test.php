<?php
 use t0t1\mysfw\module;
 use t0t1\mysfw\frame;
/**
 * XXX mysfw_view::reveal() tests to be completed
 */

 // XXX temp
 require_once '_unit_tests/unit_testing_init.php';
 $ut_initializer = new unit_testing_initializer();
 $ut_initializer->load('module/session/session.php');


 class mysfw_session_Test extends PHPUnit_Framework_TestCase {

  public function setUp() {
   $this->x = new  module\session;
   $mocked_popper = $this->getMock('t0t1\mysfw\frame\contract\popper');
   $mocked_configurator = $this->getMock('t0t1\mysfw\frame\contract\configurator');
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
    public function test_start($v){
        $this->assertEquals($v->start(),$v);
        return $v;
    }

  /**
   * @depends test_start
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
   * @depends test_start
   * @expectedException Exception
   * @expectedMessage Failed to delete session cookie with values : {"lifetime":0,"path":"\/","domain":"","secure":false,"httponly":false,"name":"PHPSESSID","time":1409515449,"value":""}
   */
    public function test_destroy($v){
        $v->set('gni', 'gno')->destroy();
        $this->assertNull($v->get('gni'));
        return $v;
    }

  /**
   * @depends test_start
   */
    public function test_is_active($v){
        $v->start();
        $this->assertTrue($v->is_active());
        return $v;
    }

  /**
   * @expectedException Exception
   * @expectedMessage Failed to delete session cookie with values : {"lifetime":0,"path":"\/","domain":"","secure":false,"httponly":false,"name":"PHPSESSID","time":1409515679,"value":""}
   * @depends test_init
   */
    public function test_is_active_return_false($v){
        $v->destroy();
        $this->assertFalse($v->is_active());
        return $v;
    }

  /**
   * @depends test_start
   */
    public function test_get_all($v){
        $values=array('gni'=>'gno','gna'=>'gnu');
        $v->set('gni','gno')->set('gna','gnu');
        $this->assertEquals($v->get(),$values);
        return $v;
    }
 }
