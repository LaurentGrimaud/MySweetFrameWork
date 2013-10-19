<?php

namespace mysfw;

class dispatcher {
 protected $_defaults = [
  'controller_parameter' => 'controller',
  'controller_suffix'    => '_controller',
  'controller_default'   => 'index',
  ];

 protected function _get_ready() {
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
