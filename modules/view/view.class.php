<?php
 // XXX WIP

 class mysfw_view extends mysfw_core implements mysfw_view_interface {
  protected $_home = ''; // XXX

  public function get_ready() {
   if(($c = $this->get_configurator()) && ($dir = $c->inform('tmpl_root'))){
    $this->_home = $dir;
    return $this;
   }

   $this->_home = 'tmpl/'; // XXX temp
   return $this;
  }

  public function get($k) {return $this->_values[$k];}
  public function set($k, $v) {$this->_values[$k] = $v;}

  public function reveal($t) {
   include $this->_home.$t.'.tmpl.php'; // XXX 
  }
 }
?>
