<?php

 class mysfw_default_configurator extends mysfw_core implements mysfw_configurator, mysfw_dna {
  private $_repository; 

  protected function _get_ready(){
   $this->_repository = (object) null;
  }

  public function memorize($c, $v){$this->_repository->$c = $v;}
  public function get_back($c){return @$this->_repository->$c;}
 }

?>
