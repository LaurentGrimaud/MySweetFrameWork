<?php

 class mysfw_configurator extends mysfw_core {
  private $_repository; 

  public function get_ready(){
   $this->_repository = (object) null;
  }

  public function define($c, $v){$this->_repository->$c = $v;}
  public function inform($c){return $this->_repository->$c;}
 }

?>
