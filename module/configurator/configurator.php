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
  public function define($c, $v, $cc = '_default_', $p = 'project'){
   if(isset($this->_repository[$p][$cc][$c])){
    throw $this->except("Configuration entry ($p, $cc, $c) already exists");
   }
   $this->_repository[$p][$cc][$c] = $v;
   return $this;
  }

  public function inform($c, $cc = '_default_', $p = 'project'){
   if($cc == '_default_') return @$this->_repository[$p][$cc][$c];
   return @$this->_repository[$p][$cc] ?
     @$this->_repository[$p][$cc][$c] :
     @$this->_repository[$p]['_default_'][$c];
  }
 }
