<?php
 /**
  * Defines common base for ALL mysfw objects, except popper
  * Currently, four things:
  * 1. Gives knowledge and access to the popper object
  * 2. Provides logging capabilities, by interfacing to a reporter object 
  * 3. Define an auto_initialise method, called by the popper
  * 4. Provides configuration access, by interfacing a configurator object
  **/

 abstract class mysfw_core {
  private $_p; // mysfw popper
  private $_r; // mysfw reporter
  private $_c; // mysfw configurator
  protected $_defaults;

  public function set_popper($_) {$this->_p = $_;}
  public function get_popper() {return $this->_p;}

  public function set_reporter($_) {$this->_r = $_;}
  public function get_reporter() {return $this->_r;}

  public function report_debug($msg){return $this->_report("debug", $msg);}
  public function report_info($msg){return $this->_report("info", $msg);}
  public function report_warning($msg){return $this->_report("warning", $msg);}
  public function report_error($msg){return $this->_report("error", $msg);}

  public function set_configurator($_) {$this->_c = $_;}
  public function get_configurator() {return $this->_c;}

  public function inform($c){
   if(! $_c = $this->get_configurator()) return false;
   return $_c->inform($c);
  }

  public function define($c, $v){
   if(! $_c = $this->get_configurator()) return false;
   return $_c->define($c, $v);
  }

  final public function get_ready(){
   $this->_defaults();
   $this->_get_ready();
   return $this;
  }

  protected function _get_ready() {
   $this->report_warning("This is the default (empty) implementation of _get_ready() method, seems that this object lacks specific implementation");
  }

  private function _report($method, $msg){
   if(! $r = $this->get_reporter()) return false;
   $method_name = "report_$method";
   return $r->$method_name($this->_emsg($msg));
  }

  /**
   * Build encapsulated message
   */
  private function _emsg($msg){
   return '['.get_class($this)."] $msg";
  }

  protected function _defaults(){
   if(! $this->_defaults) return;
   foreach($this->_defaults as $conf => $default_value){
    if(! $this->inform($conf)) $this->define($conf, $default_value);
   }
  }
 }

?>
