<?php
 //XXX no contract for this class ?

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 class dispatcher extends frame\dna implements frame\contract\dna {
  protected $_defaults = [
   'dispatcher:controller' => 'generic_controller',
   'dispatcher:parameter'  => 'controller',
   'dispatcher:default'    => 'index',
   ];

  protected function _get_ready() {
   $this->report_debug("Dispatcher is ready");
  }

  /**
   * XXX params in configurator ?
   * XXX params object or array ?
   */
  public function dispatch($params) {
   if($controller = @$params[$this->inform('dispatcher:parameter')]){
    $this->report_debug("Found specified controller `$controller`");
   }else{
    $controller = $this->inform('dispatcher:default');
    $this->report_debug("No specified controller, will use default `{$this->inform('dispatcher:default')}` one");
   }

   $this->get_popper()->pop($this->inform('dispatcher:controller'))->set_param($controller)->control_and_reveal((object)$params);
  }
 }
