<?php

 class mysfw_controller_base extends mysfw_core implements mysfw_controller, mysfw_dna {
  private $_v;    // Object implementing mysfw_view
  private $_tmpl; // Template to be used by the view
  protected $_defaults = array(
    'controller_base.view' => 'http_response' // Name of the view to be used by the controller
    );

  protected function _set_tmpl($_){$this->_tmpl = $_;return $this;}
  protected function _get_tmpl(){return $this->_tmpl;}

  protected function _set($k, $v){$this->_v->set($k, $v);return $this;}
  protected function _get($_){return $this->_v->get($_);}
  
  protected function _set_all($_){$this->_v->set_all($_);return $this;}

  // XXX _get_ready() may be overriden by custom controllers
  protected function _get_ready() {
   $this->report_debug("Popping ".$this->inform('controller_base.view'). " object");
   $this->_v = $this->get_popper()->pop($this->inform('controller_base.view'));
  }

  public function get_view(){return $this->_v;}

  public function control_and_reveal($p) {
   $this->control($p);
   $this->_v->reveal($this->_get_tmpl());
  }

  public function control($p){
   // XXX default implementation
  }

 }

?>
