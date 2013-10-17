<?php
 namespace t0t1\mysfw\module\view;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\view');

 class view extends frame\dna implements frame\contract\view, frame\contract\dna {
  private $_values;
  protected $_defaults = array(
    'tmpl_dir' =>      '../includes/tmpl/', // XXX temp ?
    'view.response' => 'mysfw_http_response'
    );

  public function get($k) {return isset($this->_values[$k]) ? $this->_values[$k] : $this->inform($k);}
  public function set($k, $v) {$this->_values[$k] = $v;}
  public function set_all($_) {$this->_values = (array)$_;}
  public function get_all() {return $this->_values;}

  public function reveal($t) {
   // XXX loo temp: should use another mechanism but configurator to bring http status code to response object
   /*
   if($c = $this->get('status_code')){
    $this->define('response.http_status_code', $c);
   }
   $this->get_popper()->pop($this->inform("view.response"))->reveal();
   */
   include $this->inform('root').$this->inform('tmpl_dir').$t.'.tmpl.php'; // XXX 
  }
 }
?>
