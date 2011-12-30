<?php
 // XXX WIP

 class mysfw_view extends mysfw_core implements mysfw_view_interface {
  private $_values;
  protected $_defaults = array(
    'tmpl_dir' => 'tmpl/'
    );

  public function get($k) {return @$this->_values[$k];}
  public function set($k, $v) {$this->_values[$k] = $v;}

  public function reveal($t) {
   include $this->inform('main_root').$this->inform('tmpl_dir').$t.'.tmpl.php'; // XXX 
  }
 }
?>
