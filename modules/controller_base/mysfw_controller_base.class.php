<?php

 class mysfw_controller_base extends mysfw_core implements mysfw_controller, mysfw_dna {
  private $_v;    // Object implementing mysfw_view
  private $_tmpl; // Template to be used by the view

  protected function _set_tmpl($_){$this->_tmpl = $_;}
  protected function _get_tmpl(){return $this->_tmpl;}

  protected function _set($k, $v){$this->_v->set($k, $v);}
  protected function _get($_){return $this->_v->get($_);}

  protected function _get_ready() {
   $this->_v = $this->get_popper()->pop('view'); // XXX temp
  }

  public function control_and_reveal($p) {
   $this->control($p);
   $this->_v->reveal($this->_get_tmpl());
  }

  public function control($p){
   // XXX default implementation
  }

 }

?>
