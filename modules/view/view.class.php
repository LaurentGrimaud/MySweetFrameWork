<?php

// require_once 'mysfw_core.class.php';
// require_once 'mysfw_view.interface.php';

 class mysfw_view extends mysfw_core implements mysfw_view_interface {
  protected $_home = ''; // XXX

  public function get($k) {return $this->_values[$k];}
  public function set($k, $v) {$this->_values[$k] = $v;}

  public function reveal($t) {
   include $this->_home.$t.'.tmpl.php'; // XXX 
  }
 }
?>
