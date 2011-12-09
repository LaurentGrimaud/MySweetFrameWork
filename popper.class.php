<?php

 class mysfw_popper {
  private static $_itself;
  private $_home;
  private $_register = array(); // XXX here, or in configurator ... ?


  private function __construct() { // No external instanciation
   $this->set_home(__DIR__);
  }


  public static function itself() { // Singletonization
   if(! self::$_itself) {self::$_itself = new mysfw_popper;}

   $c = $this->register('configurator', 'configurator'); // XXX error handling ?
   $c->define('main_root', __DIR__);

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
   if($_ = $this->indicate('reporter')){$o->set_reporter($_);}
   // Does a default configurator exist ?
   // XXX
   if($_ = $this->indicate('configurator')){$o->set_configurator($_);}

   $o->get_ready();
   return $o;
  }

  public function swallow($modulename) {
   require_once($this->_build_module_name($modulename));
  }

  public function set_home($v) {$this->_home = $v;}
  public function get_home() {return $this->_home;}

  // XXX temp
  /**
   * @return thing registered (created or simply referenced)
   */

  public function register($name, $stuff) {
   if(is_string($stuff)) {
    $this->_register[$name] = &$this->pop($stuff);
    return $this->_register[$name];
   }
   $this->_register[$name] = $stuff;
   return $stuff;
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
