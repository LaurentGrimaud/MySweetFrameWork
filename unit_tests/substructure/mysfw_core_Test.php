<?php
/**
 * XXX mysfw_view::reveal() tests to be completed
 */

 require_once 'substructure/mysfw_dna.interface.php';
 require_once 'substructure/mysfw_reporter.interface.php';

 require_once 'substructure/mysfw_core.class.php';

 /** Just to test the underlaying abstract class ... **/
 class mysfw_core_instance extends mysfw_core {
 }

 class mysfw_core_Test extends PHPUnit_Framework_TestCase {

  public function setUp(){
   $this->x = new mysfw_core_instance;
  }

  public function test_define_failure_when_configurator_not_set() {
   $this->assertFalse($this->x->define('fjfjgjgjgj', 'lsldjfkfkfk'));
  }
 }

?>
