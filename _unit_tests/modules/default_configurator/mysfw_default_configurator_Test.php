<?php
 /** XXX what about object intialisation (get_ready()) ? **/
 require_once '_unit_tests/substructure/mysfw_core_Test.php';

 require_once 'substructure/mysfw_dna.interface.php';
 require_once 'substructure/mysfw_configurator.interface.php';
 require_once 'substructure/mysfw_core.class.php';

 require_once 'modules/default_configurator/mysfw_default_configurator.class.php';

 class mysfw_default_configurator_Test extends mysfw_core_Test {

  public function setUp() {
   $this->x = new  mysfw_default_configurator;
  }

  public function test_define_and_inform(){
   $this->x->define('key', 'value');
   $this->assertEquals('value', $this->x->inform('key'));

   $this->x->define('other key', 'other value');
   $this->assertEquals('other value', $this->x->inform('other key'));
   $this->assertEquals('value', $this->x->inform('key'));

   $this->x->define('key', 'other value');
   $this->assertEquals('other value', $this->x->inform('key'));
   $this->assertEquals('other value', $this->x->inform('other key'));
  }

 }


?>
