<?php
 /**
  * Defines common roles for ALL mysfw objects, except popper
  * Currently, five things:
  * 1. Gives knowledge and access to the popper object
  * 2. Provides logging capabilities, by interfacing to a reporter object 
  * 3. Defines an auto-initialise mechanism, called by the popper
  * 4. Provides configuration access, by interfacing a configurator object
  * 5. Provides exception handling facility (factory method except())
  *
  *
  * WIP about configuration
  *  Needs:
  *   1. each module has its own default configuration
  *   2. each module could have its custom configuration
  *   3. ... ? 
  **/

 namespace t0t1\mysfw\frame;

 abstract class dna implements contract\dna {
  private $_p; // mysfw popper
  private $_r; // mysfw reporter
  private $_c; // mysfw configurator
  protected $_defaults; // array of configurations entries needed by the modules, with its default value associated
  protected $_conf_context = null; // configuration context, as a string
  protected $_custom_conf = null; // custom configuration
  protected $_conf = [];     /** Object configuration repository **/


  /** Imposed behaviors **/
  final public function set_popper(contract\popper $_) {$this->_p = $_; return $this;}
  final public function get_popper() {if(!$this->_p) throw $this->except('No popper ?! Something is wrong in the mysfw kingdom...');return $this->_p;}

  final public function set_reporter(contract\reporter $_) {$this->_r = $_;return $this;}
  final public function get_reporter() {try {return $this->get_popper()->indicate('reporter');}catch(\Exception $e) {}} //XXX BERK !

  final public function set_configurator(contract\configurator $_) {$this->_c = $_;return $this;}
  final public function get_configurator() {return $this->_c;}

  final public function set_configuration_context($_ = null) {$this->_conf_context = $_;return $this;}
  final public function get_configuration_context(){return $this->_conf_context;}

  final public function set_custom_conf($_ = null) {$this->_custom_conf = $_;return $this;}
  final public function get_custom_conf(){return $this->_custom_conf;}

  final public function get_ready(){
   $this->_defaults();
   $this->_get_ready();
   return $this;
  }

  /** popper interface **/
  final public function pop($classname, $conf_context = null) {return $this->get_popper()->pop($classname, $conf_context);}
  final public function indicate($name, $register_if_missing = false) {return $this->get_popper()->indicate($name, $register_if_missing);}


  /** Utilities **/
  public function report_debug($msg){return $this->_report("debug", $msg);}
  public function report_info($msg){return $this->_report("info", $msg);}
  public function report_warning($msg){return $this->_report("warning", $msg);}
  public function report_error($msg){return $this->_report("error", $msg);}

  public function inform($c, $cc = '_default', $p = null){
   if(! $_c = $this->get_configurator()) throw $this->except("No configurator defined");
   return $_c->inform($c, $cc, $p);
  }

  public function define($c, $v, $cc = '_default', $p = null){
   if(! $_c = $this->get_configurator()) return null; // XXX TO BE CHECKED
   return $_c->define($c, $v, $cc, $p);
  }

  /**
   * factory of mysfw exceptions
   * @param string $m message of the exception
   * @param string $t the requested type of mysfw exception
   *
   * @return instance of mysfw\exception or of one of this child
   * XXX namespace finalization needed
   */
  public function except($m, $t = null){
   if($t && @$this->_exceptions[$t]){
    $ens = $this->_mns.'\exception\\'.$t;
   }else{
    $ens = 't0t1\mysfw\frame\exception\dna'; // XXX until the raise of mysfw exceptions
   }
   return new $ens($m, $t);
  }


  /**
   * Should be overriden to follow specific initialisation requirements
   */
  protected function _get_ready() {
   $this->report_warning("This is the default (empty) implementation of _get_ready() method, seems that this object lacks specific implementation");
  }

  /**
   * XXX temp
   **/
  final private function _report($method, $msg){
   if(! $r = $this->get_reporter()) return false;
   $method_name = "report_$method";
   return $r->$method_name($this->_emsg($msg));
  }

  /**
   * Build encapsulated message for reports
   */
  final private function _emsg($msg){
   return '['.get_class($this)."] $msg";
  }

  /**
   * Retrieve configuration data declared 'needed' by the module
   * and set default value if needed
   **/
  final protected function _defaults(){
   if(! $this->_defaults) return; /** No conf to handle **/
   $cc_prefix = '';

   if($this->get_configuration_context() != '_default_'){
    $cc_prefix = $this->get_configuration_context().":";
   }

   foreach($this->_defaults as $conf => $default_value){
    list($prefix, $label) = explode(':', $conf);
    if(isset($this->_custom_conf[$conf])){
     self::define($conf, $this->_custom_conf[$conf], $this->get_configuration_context(), $prefix); // XXX TEMP potential override!
     $this->_conf[$conf] = $this->_custom_conf[$conf]; // XXX TEMP DRAFT
    }else{
     if(! self::inform($cc_prefix.$conf)){
      self::define($cc_prefix.$conf, $default_value);
     }
     $this->_conf[$conf] = self::inform($cc_prefix.$conf); // XXX TEMP DRAFT
    }
   }
  }

// XXX DRAFT
  final public function dump_all_conf_data(){
   return $this->dump_conf();
  }

// XXX DRAFT
  final public function dump_defaults() {
   return "Current defaults:\n".print_r($this->_defaults, true);
  }

// XXX DRAFT
  final public function dump_conf() {
return "Current conf:\n".print_r($this->_conf, true);
  }
 }
