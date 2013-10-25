<?php
 //XXX no contract for this class ?

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 class dispatcher extends frame\dna implements frame\contract\dna {
  protected $_defaults = [
   'controller_parameter' => 'controller',
   'controller_suffix'    => '_controller',
   'controller_default'   => 'index',
   ];

  protected function _get_ready() {
   $this->report_debug("Dispatcher is ready");
  }

  /**
   * XXX params in configurator ?
   * XXX params object or array ?
   */
  public function dispatch($params) {
   if($controller = @$params[$this->inform('controller_parameter')]){
    $this->report_debug("Found specified controller `$controller`");
   }else{
    $controller = $this->inform('controller_default');
    $this->report_debug("No specified controller, will use default `{$this->inform('controller_default')}` one");
   }

   $this->get_popper()->pop($controller.$this->inform('controller_suffix'))->control_and_reveal((object)$params);
  }
 }
