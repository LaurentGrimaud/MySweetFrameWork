<?php
 //XXX bad name ?

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\controller');

 class generic_controller extends frame\dna implements frame\contract\controller, frame\contract\dna {
  private $_param;    // XXX
  private $_v;    // Object implementing mysfw_view
  private $_tmpl; // Template to be used by the view
  protected $_defaults = array(
    'generic_controller:view' => 'http_response',                // Name of the view to be used by the controller
    'generic_controller:control_dir' => '../include/control/' // Directory to search for controls implementations
    );

  protected function _set_tmpl($_){$this->_tmpl = $_;return $this;}
  protected function _get_tmpl(){return $this->_tmpl;}

  protected function _set($k, $v){$this->_v->set($k, $v);return $this;}
  protected function _get($_){return $this->_v->get($_);}
  
  protected function _set_all($_){$this->_v->set_all($_);return $this;}

  // XXX _get_ready() may be overriden by custom controllers
  protected function _get_ready() {
   $this->report_debug("Popping ".$this->inform('generic_controller:view'). " object");
   $this->_v = $this->get_popper()->pop($this->inform('generic_controller:view'));
  }
  
  public function set($k, $v){return $this->_set($k, $v);}
  public function set_param($param){$this->_param = $param;$this->_set_tmpl($param);return $this;}
  public function get_view(){return $this->_v;}

  public function control_and_reveal($p) {
   $this->control($p);
   $this->_v->reveal($this->_get_tmpl());
  }

  public function control($p){
   // XXX default implementation
   include $this->inform('generic_controller:control_dir').$this->_param.'.php'; // XXX 
  }

 }

?>
