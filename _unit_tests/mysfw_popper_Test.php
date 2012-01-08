<?php

 require_once 'popper.class.php';

 class mysfw_default_popper_Test extends mysfw_core_Test {

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function XXX_test_no_direct_intanciation(){
   $x = new mysfw_default_popper(__DIR__);
  }
 }

?>
