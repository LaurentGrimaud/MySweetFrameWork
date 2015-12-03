<?php
 //XXX bad name ?

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\controller');
 $this->_learn('module\controller_base\controller_base');

 class generic_controller extends controller_base implements frame\contract\controller, frame\contract\dna {
  private $_param;    // XXX
  protected $_defaults = array(
    'generic_controller:view'        => 'http_response',      // Name of the view to be used by the controller
    'generic_controller:control_dir' => '../include/control/' // Directory to search for controls implementations
    );

  protected function _get_ready() {
   $this->report_debug("Popping ".$this->inform('generic_controller:view'). " object");
   $this->_v = $this->pop($this->inform('generic_controller:view'));
  }

  public function control_and_reveal($p) {
   $this->control($p);
   $this->_v->reveal($this->_get_tmpl());
  }

  public function control($p){
   $this->report_debug("Will use control file `{$this->_param}`");
   $this->report_debug("Template set to `{$this->_get_tmpl()}`");
   include $this->inform('generic_controller:control_dir').$this->_param.'.php'; // XXX 
  }

  public function set_param($param){$this->_param = $param;$this->_set_tmpl($param);return $this;}
 }
