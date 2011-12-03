<?php
 /**
  * Defines common base for ALL mysfw objects
  * Currently, only three things:
  * 1. Gives knowledge and access to the popper object
  * 2. Provides logging capabilities, by interfacing to a reporter object 
  * 3. Define an auto_initialise method, called by the popper
  **/

 abstract class mysfw_core {
  protected $_p; // mysfw popper
  protected $_r; // mysfw reporter

  public function set_popper($_) {$this->_p = $_;}
  public function get_popper() {return $this->_p;}

  public function set_reporter($_) {$this->_r = $_;}
  public function get_reporter() {return $this->_r;}

  public function report_debug($msg){return $this->_report("debug", $msg);}
  public function report_info($msg){return $this->_report("info", $msg);}
  public function report_warning($msg){return $this->_report("warning", $msg);}
  public function report_error($msg){return $this->_report("error", $msg);}

  public function get_ready(){
   $this->report_warning("This is the default implementation of get_ready() method, seems that this object lacks specific implementation");
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
 }

?>
