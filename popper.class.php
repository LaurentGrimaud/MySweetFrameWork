<?php

 // XXX temp static requires
 require_once 'substructure/mysfw_popper.interface.php';
 require_once 'substructure/mysfw_dna.interface.php';

 class mysfw_default_popper implements mysfw_popper {
  private static $_itself;
  private $_home;
  private $_register = array(); // XXX here, or in configurator ... ?


  private function __construct($root) { // No external instanciation
   $this->set_home(__DIR__); // XXX useful ?

   $c = $this->register('configurator', 'default_configurator'); // XXX error handling ?
   $c->define('home', __DIR__.'/');
   $c->define('root', $root.'/');
   $c->define('extensions_dir', $root.'/../includes/mysfw_extensions/'); // XXX temp
  }


  public static function itself($root) { // Singletonization
   if(! self::$_itself) {self::$_itself = new mysfw_default_popper($root);}

   return self::$_itself;
  }

  /**
   * Creates, configures and returns an instance of the requested class
   * Handles the includes eventually needed
   * 
   * @return an instance of the requested class
  **/
  public function pop($classname, $conf_context = null) {
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

   if($conf_context) $o->set_configuration_context($conf_context);

   return $o->get_ready();
  }

  public function swallow($modulename) {
   if(! @include_once($this->_build_module_name($modulename))){
    include_once($this->_build_custom_file_name($modulename));
   }
  }

  public function set_home($v) {$this->_home = $v;}
  public function get_home() {return $this->_home;}

  // XXX temp
  /**
   * @return thing registered (created or simply referenced)
   */

  public function register($name, $stuff) {
   if(is_string($stuff)) {
    return $this->_register[$name] = &$this->pop($stuff);
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

  private function _build_custom_file_name($f) {
   return $this->indicate('configurator')->inform('extensions_dir')."$f.class.php";
  }

  private function _learn($it) {
   require_once($this->_build_file_name($it));
  }
 }
?>
