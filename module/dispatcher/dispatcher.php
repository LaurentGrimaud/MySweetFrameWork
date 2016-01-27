<?php
 //XXX no contract for this class ?

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 class dispatcher extends frame\dna implements frame\contract\dna {
  protected $_defaults = [
   'dispatcher:controller'        => 'generic_controller',
   'dispatcher:controller_suffix' => '_controller',
   'dispatcher:parameter'         => 'controller',
   'dispatcher:default'           => 'index',
   ];

  protected function _get_ready() {
   $this->report_debug("Dispatcher is ready");
  }

  /**
   * XXX params in configurator ?
   * XXX params object or array ?
   * XXX bad balance between base_controller and generic_controller
   */
  public function dispatch(request $request) {
   $filter = $this->pop('filter');
   if($controller = $filter->apply($request->get_query($this->inform('dispatcher:parameter')),array(array($filter,'filter_string')))){
    $this->report_debug("Specified controller `$controller` requested");
   }else{
    $controller = $this->inform('dispatcher:default');
    $this->report_debug("No specified controller requested, will use default `{$this->inform('dispatcher:default')}` one");
   }

   try {
    $controller_o = $this->pop($controller.$this->inform('dispatcher:controller_suffix')); // First, try to find a controller object
   } catch(frame\exception\dna $e) { // Fallback to generic_controller
    $this->report_debug("Specific controller not found, will try the generic ".$this->inform('dispatcher:controller'));
    $controller_o = $this->pop($this->inform('dispatcher:controller'))->set_param($controller);
   }

   $controller_o->control_and_reveal($request);
  }
 }
