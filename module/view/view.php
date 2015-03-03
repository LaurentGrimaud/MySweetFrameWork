<?php
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\view');

 class view extends frame\dna implements frame\contract\view, frame\contract\dna {
  private $_values;
  protected $_filter= false;
  protected $_defaults = array(
    'view:tmpl_dir' => '../include/tmpl/',
    'view:response' => 'mysfw_http_response'
    );

  public function get($k) {return isset($this->_values[$k]) ? $this->_values[$k] : $this->inform($k);}
  public function set($k, $v) {$this->_values[$k] = $v;}
  public function set_all($_) {$this->_values = (array)$_;}
  public function get_all() {return $this->_values;}
  public function output($k,array $callbacks=null) {
    if( ! $callbacks) $callbacks= array('htmlspecialchars');
    return $this->_filter->apply($k,$callbacks);
  }

  protected function _get_ready(){
   $this->_filter= $this->pop('filter');
  }

  /**
   * Process to the given template
   * @var $t string the template's name
   *
   * @throw frame\exception\dna on include error
   */
  public function reveal($t) {
   $tmpl_name = $this->inform('root').$this->inform('view:tmpl_dir').$t.'.tmpl.php';
   if(! @include $tmpl_name) throw $this->except("Failed to include template `$tmpl_name`");
  }
 }
