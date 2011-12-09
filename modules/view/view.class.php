<?php
 // XXX WIP

 class mysfw_view extends mysfw_core implements mysfw_view_interface {
  const _dir  = 'tmpl';

  protected $_home = ''; // XXX useful ?

  public function get_ready() {
   if($dir = $this->inform('main_root')){
    $this->_home = $dir.self::_dir;
    return $this;
   }

   $this->_home = self::_dir; // XXX temp
   return $this;
  }

  public function get($k) {return $this->_values[$k];}
  public function set($k, $v) {$this->_values[$k] = $v;}

  public function reveal($t) {
   include $this->_home.$t.'.tmpl.php'; // XXX 
  }
 }
?>
