<?php

 class mysfw_default_configurator extends mysfw_core implements mysfw_configurator, mysfw_dna {
  private $_repository; 

  protected function _get_ready(){
   $this->_repository = (object) null;
  }

  public function dump() {
   return print_r($this->_repository, true);
  }

  /** Overrides of the generic behaviour implemented in mysfw_core **/
  public function define($c, $v, $cc = null){$_c = $cc? "$cc.$c" : $c;$this->_repository->$_c = $v;}
  public function inform($c, $cc = null){$_c = $cc ? "$cc.$c" : $c; return @$this->_repository->$_c;}
 }

?>
