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
  public function set($k, $v) {$this->_values[$k] = $v;return $this;}
  public function push($k, array $v) {
    if( ! isset($this->_values[$k])) $this->_values[$k]= array();
    if( ! is_array($this->_values[$k])) $this->_values[$k]= (array)$this->_values[$k];
    $this->_values[$k]= array_merge($this->_values[$k],$v);
    return $this;
  }
  public function set_all($_) {$this->_values = (array)$_;return $this;}
  public function get_all() {return $this->_values;}
  public function output($k,array $callbacks=null) {
    if( ! $callbacks) $callbacks= array('htmlspecialchars');
    return $this->_filter->apply($k,$callbacks);
  }

  protected function _get_ready(){
   $this->_filter= $this->pop('filter');
  }

  // XXX Draft
  public function e($k){echo $this->get($k);}

  /**
   * Process to the given template
   * XXX Draft using closures
   * @var $t string the template's name
   *
   * @throw frame\exception\dna on include error
   */
  public function reveal($t, $buffer = false, $vars = null) {
   if(is_null($vars)) $vars = $this->get_all();
   $tmpl_name = $this->inform('root').$this->inform('view:tmpl_dir').$t.'.tmpl.php';
   $e = function($x) use ($vars) {echo $vars[$x];};
   $g = function($x) use ($vars) {return $vars[$x];};
   $i = function($t, $buffer = false, $vars = null) {$this->reveal($t, $buffer, $vars);};
   if($buffer) {
    ob_start();
   }
   $exception = $this->except("Failed to include template `$tmpl_name`");
   $f = function($e, $g, $i, $tmpl_name, $exception) {
    if(! include $tmpl_name) throw $exception;
   };
   $f = $f->bindTo((object)[]);
   $f($e, $g, $i, $tmpl_name, $exception);
   if($buffer) {
    return ob_get_clean();
   }
  }
 }
