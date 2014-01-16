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
   $this->_repository = (object) null;
  }

  /**
   * XXX draft
   */
  public function dump() {
   return print_r($this->_repository, true);
  }

  /** Overrides the generic behaviour implemented in dna **/
  public function define($c, $v, $cc = null){$_c = $cc? "$cc.$c" : $c;$this->_repository->$_c = $v;return $this;}
  public function inform($c, $cc = null){$_c = $cc ? "$cc.$c" : $c; return @$this->_repository->$_c;}
 }
