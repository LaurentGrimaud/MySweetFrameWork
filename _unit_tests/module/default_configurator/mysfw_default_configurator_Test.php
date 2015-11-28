<?php
 /** XXX what about object initialisation (get_ready()) ? **/
 use t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 // XXX temp
 require_once '_unit_tests/unit_testing_init.php';
 $ut_initializer = new unit_testing_initializer();

 // XXX liste des dépendances
 // $ut_initializer->load('frame/contract/dna.php');

 class configurator_Test extends PHPUnit_Framework_TestCase {

  public function setUp() {
   $this->x = new  module\configurator;
   $this->x->get_ready();
  }

  public function test_define_and_inform(){
   $this->x->define('key', 'value');
   $this->assertEquals('value', $this->x->inform('key'));

   $this->x->define('other key', 'other value');
   $this->assertEquals('other value', $this->x->inform('other key'));
   $this->assertEquals('value', $this->x->inform('key'));
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   * @expectedExceptionMessage Configuration entry (_default_, key) already exists
   */
  public function test_define_is_immutable() {
   $this->x->define('key', 'value');
   $this->x->define('key', 'other value');
  }

 }


?>
