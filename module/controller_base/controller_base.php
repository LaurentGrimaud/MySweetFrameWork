<?php
 //XXX bad name ?

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\controller');

 class controller_base extends frame\dna implements frame\contract\controller, frame\contract\dna {
  private $_v;    // Object implementing mysfw_view
  private $_tmpl; // Template to be used by the view
  protected $_defaults = array(
    'controller_base:view' => 'http_response' // Name of the view to be used by the controller
    );

  protected function _set_tmpl($_){$this->_tmpl = $_;return $this;}
  protected function _get_tmpl(){return $this->_tmpl;}

  protected function _set($k, $v){$this->_v->set($k, $v);return $this;}
  protected function _get($_){return $this->_v->get($_);}

  protected function _set_all($_){$this->_v->set_all($_);return $this;}

  // XXX good place for that ?
  final function __construct() {
   $this->report_debug("Popping ".$this->inform('controller_base:view'). " object");
   $this->_v = $this->get_popper()->pop($this->inform('controller_base:view'));
  }

  public function get_view(){return $this->_v;}

  public function control_and_reveal($p) {
   $this->control($p);
   $this->_v->reveal($this->_get_tmpl() ? : $this->_default_tmpl());
  }

  protected function _default_tmpl(){
   $classname = get_class($this);
   $_classname = ( false === ($pos = strrpos($classname, $this->inform('dispatcher:controller_suffix'))))?$classname:substr_replace($classname, '', $pos, strlen($this->inform('dispatcher:controller_suffix'))); // if trailing dispatcher:controller_suffix found, strip it
   return join('', array_slice(explode('\\', $_classname), -1)); // strips namespace
  }

  public function control($p){
   // XXX default implementation
  }

 }

?>
