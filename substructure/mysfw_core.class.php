<?php
 /**
  * Defines common roles for ALL mysfw objects, except popper
  * Currently, four things:
  * 1. Gives knowledge and access to the popper object
  * 2. Provides logging capabilities, by interfacing to a reporter object 
  * 3. Defines an auto-initialise mechanism, called by the popper
  * 4. Provides configuration access, by interfacing a configurator object
  **/

 abstract class mysfw_core implements mysfw_dna {
  private $_p; // mysfw popper
  private $_r; // mysfw reporter
  private $_c; // mysfw configurator
  protected $_defaults; // array of configurations entries needed by the modules, with its default value associated
  protected $_conf_context = null; // configuration context, as a string

  /** Imposed behaviors **/
  final public function set_popper(mysfw_popper $_) {$this->_p = $_; return $this;}
  final public function get_popper() {return $this->_p;}

  final public function set_reporter(mysfw_reporter $_) {$this->_r = $_;return $this;}
  final public function get_reporter() {return $this->_r;}

  final public function set_configurator(mysfw_configurator $_) {$this->_c = $_;return $this;}
  final public function get_configurator() {return $this->_c;}

  final public function set_configuration_context($_ = null) {$this->_conf_context = $_;return $this;}
  final public function get_configuration_context(){return $this->_conf_context;}

  final public function get_ready(){
   $this->_defaults();
   $this->_get_ready();
   return $this;
  }


  /** Utilities **/
  public function report_debug($msg){return $this->_report("debug", $msg);}
  public function report_info($msg){return $this->_report("info", $msg);}
  public function report_warning($msg){return $this->_report("warning", $msg);}
  public function report_error($msg){return $this->_report("error", $msg);}

  public function inform($c){
   if(! $_c = $this->get_configurator()) throw new mysfw\exception("No configurator defined");
   return $_c->inform($c, $this->get_configuration_context());
  }

  public function define($c, $v){
   if(! $_c = $this->get_configurator()) return null;
   return $_c->define($c, $v, $this->get_configuration_context());
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
    $ens = '\mysfw\exception'; // XXX until the raise of mysfw exceptions
   }
   return new $ens($m, $t);
  }


  /**
   * Should be overriden to follown specific initialisation requirements
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
   if(! $this->_defaults) return;
   foreach($this->_defaults as $conf => $default_value){
    if(! $this->inform($conf)) $this->define($conf, $default_value);
   }
  }

 }

?>
