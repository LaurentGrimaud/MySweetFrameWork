<?php
 /**
  * Default implementation of myswf configurator contract
  */
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\dna');
 $this->_learn('frame\contract\configurator');

 class configurator extends frame\dna implements frame\contract\configurator, frame\contract\dna {
  private $_repository; 

  protected function _get_ready(){
   $this->_repository = [];
  }

  /**
   * XXX draft
   */
  public function dump() {
   return print_r($this->_repository, true);
  }

  /** Overrides the generic behaviour implemented in dna **/
  public function define($c, $v, $cc = '_default_', $module = '_top_'){
   if(isset($this->_repository[$module][$cc][$c])){
    throw $this->except("Configuration entry ($cc, $c) already exists"); // XXX incorrect msg 
   }
   $this->_repository[$module][$cc][$c] = $v;
   return $this;
  }

  // XXX _default_ ?
  public function inform($c, $cc = '_default_', $module = '_top_'){
   if(isset($this->_repository[$module][$cc][$c]))
    return $this->_repository[$module][$cc][$c];
   return null;
  }

  public function configure($module, $module_name) {
   $module->set_name($module_name);
   if(! $defaults = $module->get_defaults()) return;
   $context       = $module->get_configuration_context(); // XXX
   $custom_conf   = $module->get_custom_conf();
   $conf          = [];
   foreach($defaults as $entry => $value){
    if(isset($custom_conf[$entry])){
     self::define($entry, $custom_conf[$entry], $context, $module_name);
    }else{
     if(! self::inform($entry, $context, $module_name)){
      self::define($entry, $value, $context, $module_name);
     }
    }
    $conf[$entry] = self::inform($entry, $context, $module_name); // XXX TEMP DRAFT
   }
   $module->set_conf($conf);
  }
 }
