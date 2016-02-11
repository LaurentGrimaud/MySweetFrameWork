<?php
 /**
  * Multi-versioned configurator
  * Handles several versions of a default (base) configuration
  * Handles a current version to use
  * Fallback to the base configuration when no entries found on current configuration
  *
  * XXX WIP, beta stage
  */
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;
 use t0t1\mysfw\frame\exception;

 $this->_learn('frame\dna');
 $this->_learn('frame\contract\configurator');

 class versioned_configurator extends frame\dna implements frame\contract\configurator, frame\contract\dna {
  protected $_defaults = [
   'versioned_configurator:configurator_to_pop' => 'configurator',
   'versioned_configurator:role_to_replace'     => 'configurator'
  ]; 
/*  protected $_defaults = ['versioned_configurator'=> [
   'configurator_to_pop' => 'configurator',
   'role_to_replace'     => 'configurator'
  ]]; 
*/

  private $_versions; // Array of availables versions except the base one
  private $_to_use;   // The version to use
  private $_base;     // The base version, used as fallback for all versions

  protected function _get_ready(){
   $role = $this->indicate('configurator')->inform('versioned_configurator:role_to_replace');// XXX TEMP
   $this->_to_use = $this->_base = $this->indicate($role);
   $this->get_popper()->register($role, $this);
  }

  /*
   * Adds an alternative configurator ("version")
   * Uses the given name as entry name
   * Uses the given configurator, or creates a new one
   *
   * @param string $name, the name of the alternative
   * @param frame $name, the name of the alternative
   */
  public function add_version($name, $configurator = null) {
   $this->_versions[$name] = $configurator ? : $this->pop($this->inform('versioned_configurator:configurator_to_pop')); // XXX TEMP
   if(count($this->_versions) == 0) $this->_to_use = $this->_base = $this->get_version($name);
   return $this;
  }

  // XXX bad name
  public function use_version($name) {
   $this->_to_use = $this->get_version($name);
   return $this;
  }

  public function use_default(){$this->_to_use = $this->_base; return $this;}

  public function get_version($name) {
   if(! @$this->_versions[$name]) throw new exception\dna("No configurator defined for `$name` environment"); 
   return $this->_versions[$name];
  }

  public function configure($module) {
   if(! $defaults = $module->get_defaults()) return;
   $context       = $module->get_configuration_context();
   $custom_conf   = $module->get_custom_conf();
   $conf          = [];
   foreach($defaults as $entry => $value){
    if(isset($custom_conf[$entry])){
     self::define($entry, $custom_conf[$entry], $context);
    }else{
     if(! self::inform($entry, $context)){
      self::define($entry, $value, $context);
     }
    }
    $conf[$entry] = self::inform($entry, $context); // XXX TEMP DRAFT
   }
   $module->set_conf($conf);
  }

  /**
   * XXX draft
   */
  public function dump() {
   $r = get_class()." - Base configuration:\n".$this->_base->dump();
   foreach($this->_versions as $name => $conf) {
    $r .= "\n`$name` configuration version (".get_class($conf)."):\n".$conf->dump();
   }
   return $r;
  }

  /** Overrides the generic behaviour implemented in dna **/

  public function define($c, $v, $cc = '_default_'){ // XXX temp
   $this->_base->define($c, $v, $cc);
   return $this; // XXX optim ? Could return $this->get_version($this->_to_use)
  }

  public function inform($c, $cc = '_default_'){ // XXX temp
   if(null != ($_ = $this->_to_use->inform($c, $cc))) return $_; 
   $this->report_debug(sprintf('No value found on current configuration for (%s, %s), trying base one', $c, $cc));
   if(null != ($_ = $this->_base->inform($c, $cc))){
    $this->report_debug(sprintf('Value found on base configuration for (%s, %s): %s', $c, $cc, $_));
    return $_;
   }
   $this->report_debug(sprintf('No value found on base configuration for (%s, %s) - returning null', $c, $cc));
   return null;
  }
 }
