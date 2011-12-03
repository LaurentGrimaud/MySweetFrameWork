<?php

 class mysfw_popper {
  private static $_itself;
  private $_home;
  private $_register = array();


  private function __construct() { // No external instanciation
   $this->set_home(__DIR__);
  }


  public static function itself() { // Singletonization
   if(! self::$_itself) {self::$_itself = new mysfw_popper;}

   return self::$_itself;
  }

  public function pop($classname) {
   $full_name = "mysfw_$classname";
   if(! class_exists($full_name)){
    $this->swallow($classname);
   }
   $o = new $full_name;
   $o->set_popper($this);
   // Does a default reporter exist ?
   // XXX
   if($r = $this->indicate('reporter')){$o->set_reporter($r);}
   $o->get_ready();
   return $o;
  }

  public function swallow($modulename) {
   require_once($this->_build_module_name($modulename));
  }

  public function set_home($v) {$this->_home = $v;}
  public function get_home() {return $this->_home;}

  // XXX temp
  public function register($name, $object_name) {
   if(is_string($object_name)) {
    $this->_register[$name] = &$this->pop($object_name);
    return true;
   }
   $this->_register[$name] = $object_name;
   return true;
  }

  // XXX temp
  public function indicate($name) {return @$this->_register[$name];}


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
