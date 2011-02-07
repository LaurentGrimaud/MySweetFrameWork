<?php

 class mysfw_popper {
  private static $_itself;
  private $_home;

  private function __construct() { // No external instanciation
   $this->set_home(dirname(__FILE__));
  }


  public static function itself() { // Singletonization
   if(! self::$_itself) {self::$_itself = new mysfw_popper;}

   return self::$_itself;
  }

  public function pop($classname) {
   $full_name = "mysfw_$classname";
   $o = new $full_name;
   $o->set_popper($this);
   return $o;
  }

  public function swallow($modulename) {
   require_once($this->_build_module_name($modulename));
  }

  public function set_home($v) {$this->_home = $v;}
  public function get_home() {return $this->_home;}


  private function _build_file_name($f) {
   return $this->_home.'/'.$f;
  }

  private function _build_module_name($module) {
   return $this->_home."/modules/$module/$module.module.php";
  }

  private function _learn($it) {
   require_once($this->_build_file_name($it));
  }
 }
?>
