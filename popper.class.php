<?php

 mysfw_popper::itself();

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
   require_once($this->_build_file_name($classname));
   $o = new $classname;
   $o->setPopper($this);
   return $o;
  }

  public function set_home($v) {$this->_home = $v;}
  public function get_home() {return $this->_home;}


  private function _build_file_name($classname) {
   return $this->_home.$classname.'.php';
  }
 }
?>
