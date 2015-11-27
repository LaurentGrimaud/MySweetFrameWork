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
//  protected $_defaults = [
//   'versioned_configurator:configurator_to_pop' => 'configurator',
//   'versioned_configurator:role_to_replace'     => 'configurator']; 
  protected $_defaults = ['versioned_configurator'=> [
   'configurator_to_pop' => 'configurator',
   'role_to_replace'     => 'configurator']]; 

  private $_versions; // Array of availables versions except the base one
  private $_to_use;   // The version to use
  private $_base;     // The base version, used as fallback for all versions

  protected function _get_ready(){
   $role = $this->get_popper()->indicate('configurator')->inform('versioned_configurator:role_to_replace');// XXX TEMP
   $this->_to_use = $this->_base = $this->get_popper()->indicate($role);
   $this->get_popper()->register($role, $this);
  }

  public function add_version($name, $conf = null) {
   $this->_versions[$name] = $conf ? : $this->get_popper()->pop($this->inform('versioned_configurator:configurator_to_pop')); // XXX TEMP
   if(count($this->_versions) == 0) $this->_to_use = $this->_base = $this->get_version($name);
   return $this;
  }

  // XXX bad name
  public function use_version($name) {
   $this->_to_use = $this->get_version($name);
   return $this;
  }

  public function get_version($name) {
   if(! @$this->_versions[$name]) throw new exception\dna("No configurator defined for `$name` environment"); 
   return $this->_versions[$name];
  }


  /**
   * XXX draft
   */
  public function dump() {
   $r = get_class()." - Default configuration:\n".$this->_base->dump();
   foreach($this->_versions as $name => $conf) {
    $r .= "\n`$name` configuration version (".get_class($conf)."):\n".$conf->dump();
   }
   return $r;
  }

  /** Overrides the generic behaviour implemented in dna **/

  public function define($c, $v){ // XXX temp
   $this->_base->define($c, $v);
   return $this; // XXX optim ? Could return $this->get_version($this->_to_use)
  }

  public function inform($c){ // XXX temp
   if(null != ($_ = $this->_to_use->inform($c))) return $_; 
   return $this->_base->inform($c);
  }
 }
