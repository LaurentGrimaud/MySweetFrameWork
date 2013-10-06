<?php

 // XXX temp static requires
 require_once 'substructure/mysfw_popper.interface.php';
 require_once 'substructure/mysfw_dna.interface.php';

 class mysfw_default_popper implements mysfw_popper {
  private static $_itself;
  private $_home;
  private $_register = array(); // XXX here, or in configurator ... ?


  /**
   * Privatized constructor, preventing external instanciation
   * 
   * @param $root the path for the project (using mysfw)
   */
  private function __construct($root) {
   $this->set_home(__DIR__); // XXX useful ?

   $c = $this->register('configurator', 'default_configurator'); // XXX error handling ?
   $c->define('home', __DIR__.'/');
   $c->define('root', $root.'/');
   $c->define('extensions_dir', $root.'/../includes/mysfw_extensions/'); // XXX temp
  }


  /**
   * Singletonnized getter for this class
   *
   * @return the only one popper object
  */
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

  /**
   * Simple module load, aimed to replace autoload process
   * First look for the files in mysfw hierarchy, and defaults
   * to project's one if not found
   */
  public function swallow($modulename) {
   if(@include_once($this->_build_module_name($modulename))) return;
   include_once($this->_build_custom_file_name($modulename));
  }

  public function set_home($v) {$this->_home = $v;}
  public function get_home() {return $this->_home;}

  /**
   * References the given stuff in the internal register
   * for later use.
   * If $stuff is a string, pops the matching object before registering it
   *
   * @param $name string the name the stuff will have in the register
   * @param $stuff string/object the stuff being registered
   * @return object thing registered (created or simply referenced)
   */
  public function register($name, $stuff) {
   if(is_string($stuff)) {
    return $this->_register[$name] = &$this->pop($stuff);
   }
   return $this->_register[$name] = $stuff;
  }

  /**
   * Searches the register for the given entry name and returns
   * the matching object
   *
   * @param $name string the name of the register's entry to investigate
   * @return object the register object
  */
  public function indicate($name) {return @$this->_register[$name];}

  /**
   * Builds an usuable path for inclusion of the given element
   * Internaly and indirectly used by modules to load their components
   *
   * @param string the relative path of the element
   * @return strinf the usuable path of the element
   * XXX bad name
   */
  private function _build_file_name($f) {
   return $this->_home.'/'.$f;
  }

  /**
   * Builds an usuable path for inclusion to the matching main file
   * of the given module
   *
   * @param $module string the name of a mysfw module
   * @return string usuable path of module's main file
   * XXX bad name
   */
  private function _build_module_name($module) {
   return $this->_home."/modules/$module/$module.module.php";
  }

  /**
   * Builds an usuable path for external object
   * Used to pop custom objects, like project's controllers
   *
   * @param $f string the name of an external object
   * @return string the path for inclusion
   */
  private function _build_custom_file_name($f) {
   return $this->indicate('configurator')->inform('extensions_dir')."$f.class.php";
  }

  /**
   * Loads the given component
   * Internally used by module main file to load components
   *
   * @param $it string the path of the component in the mysfw's hierarchy
   */
  private function _learn($it) {
   require_once($this->_build_file_name($it));
  }
 }
?>
