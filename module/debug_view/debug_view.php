<?php

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 

 class debug_view extends frame\dna implements frame\contract\view, frame\contract\dna {

  protected $_defaults = [
   'debug_view:view' => 'view'
  ];

  public function get($k) {return $this->_v->get($k);}
  public function set($k, $v) {$this->_v->set($k, $v);return $this;}
  public function push($k, array $v) {$this->_v->push($k, $v);return $this;}
  public function set_all($_) {$this->_v->set_all($_);}
  public function get_all() {return $this->_v->get_all();}

  protected function _get_ready() {
   $this->report_debug("Will create underlaying view object of type ".$this->inform('response:view'));
   $this->_v = $this->pop($this->inform('debug_view:view'));
  }

  public function reveal($t, $buffer = false) {
   $main_content = $this->_v->reveal($t, true);
   $debug_content = $this->_v->reveal('debug', true);
   $content = preg_replace('/<\/body/i', $debug_content.'</body', $main_content);
   if($buffer) return $content;
   echo $content;
  }
}
