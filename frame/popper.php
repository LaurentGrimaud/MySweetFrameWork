<?php
 /**
 * XXX use of exceptions factory ?
 * XXX this class should be "final" ?
 * XXX Depends on PHP `directories` extension, due to DIRECTORY_SEPARATOR use ?
 * XXX swallow() should check classname existence, and pop() shouldn't
 */
 namespace t0t1\mysfw\frame; 
 use t0t1\mysfw\frame\exception; // XXX useless

 class popper implements contract\popper {
  private static $_itself;
  private $_home;
  private $_register = array(); // XXX here, or in configurator ... ?

  public $_stats = []; /// XXX temp


  /**
   * Privatized constructor, preventing external instanciation
   * 
   * @param $root the path for the project (using mysfw)
   * @param $home the path for mysfw
   */
  final private function __construct($root, $home) {
   $this->set_home($home); // XXX useful ?

   $c = $this->register('configurator', 'configurator');
   $c->define('home', $home.'/');
   $c->define('root', $root.'/');
   $c->define('extension_dir', $root.'/../include/'); // XXX temp
  }


  /**
   * Singletonnized getter for this class
   *
   * @return the only one popper object
  */
  public static function itself($root, $home) {
   if(! self::$_itself) {self::$_itself = new popper($root, $home);}

   return self::$_itself;
  }

  /**
   * Creates, configures and returns an instance of the requested class
   * Handles the includes eventually needed
   * 
   * @return an instance of the requested class
  **/
  public function pop($classname, $conf_context = '_default_', $custom_conf = null) {
   $full_name = "\\t0t1\\mysfw\\module\\$classname"; // XXX static and absolute namespace
   if(! class_exists($full_name)){
    $this->swallow($classname);
   }
   $o = new $full_name;
   $o->set_popper($this)->set_configuration_context($conf_context)->set_custom_conf($custom_conf);
   try {
    $o->set_configurator($this->indicate('configurator'));
   } catch(exception\dna $e) { } // No configurator is OK
   try {
    $o->set_reporter($this->indicate('reporter'));
   }catch(exception\dna $e){ } // No reporter is OK

   @$this->_stats[$classname]++;
 
   return $o->get_ready();
  }

  /**
   * Simple module load, aimed to replace autoload process
   * First look for the files in mysfw hierarchy, and defaults
   * to project's one if not found
   *
   * @param $modulename string name of the module to load
   */
  public function swallow($modulename) {
   $file = $this->_build_module_name($modulename);
   if(file_exists($file)){
    require_once($file); // XXX require_once, really ?
    return;
   }
   $file_alt = $this->_build_custom_file_name($modulename);
   if(file_exists($file_alt)){
    require_once($file_alt);
    return;
   }
   throw new exception\dna("No `$file` nor `$file_alt` files found for module `$modulename`");  // XXX use of exception factory ?
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
    return $this->_register[$name] = $this->pop($stuff);
   }
   return $this->_register[$name] = $stuff;
  }

  /**
   * Searches the register for the given entry name and returns
   * the matching object
   *
   * @param $name string the name of the register's entry to investigate
   * @return object the register object
   * @throws exception\dna if nothing found in register
  */
  public function indicate($name, $register_if_missing = false) {
   if(! @$this->_register[$name]){
    if(! $register_if_missing) {
     throw new exception\dna("Nothing in register for name `$name`"); //XXX use of exceptions factory ?
    }
    return $this->register($name, $name);
   }
   return $this->_register[$name];
  }

  /**
   * Builds an usuable path for inclusion of the given element
   * Internaly and indirectly used by modules to load their components
   *
   * @param string the relative path of the element
   * @return strinf the usuable path of the element
   * XXX bad name
   */
  private function _build_file_name($f) {
   return $this->_home.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $f).".php";
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
   return $this->_home."/module/$module/$module.php";
  }

  /**
   * Builds an usuable path for external object
   * Used to pop custom objects, like project's controllers
   *
   * @param $f string the name of an external object
   * @return string the path for inclusion
   */
  private function _build_custom_file_name($module) {
   return $this->indicate('configurator')->inform('extension_dir')."/module/$module/$module.php";
  }

  /**
   * Loads the given component
   * Internally used by module main file to load components
   *
   * @param $it string the path of the component in the mysfw's hierarchy
   * XXX what about dependencies against aliens ?
   */
  private function _learn($it) {
   require_once($this->_build_file_name($it));
  }
  
  private function _build_custom_file_name_contract($f) {
   return $this->indicate('configurator')->inform('extension_dir') . DIRECTORY_SEPARATOR . "module" . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $f) . ".php";
  }
  
  private function _learn_custom($it) {
   require_once($this->_build_custom_file_name_contract($it));
  }
  
 }
