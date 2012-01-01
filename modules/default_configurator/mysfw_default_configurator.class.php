<?php

 class mysfw_default_configurator extends mysfw_core implements mysfw_configurator, mysfw_dna {
  private $_repository; 

  protected function _get_ready(){
   $this->_repository = (object) null;
  }

  /** Overrides of the generic behaviour implemented in mysfw_core **/
  public function define($c, $v){$this->_repository->$c = $v;}
  public function inform($c){return @$this->_repository->$c;}
 }

?>
