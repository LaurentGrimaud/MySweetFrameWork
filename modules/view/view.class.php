<?php
 // XXX WIP

 class mysfw_view extends mysfw_core implements mysfw_view_interface, mysfw_dna {
  private $_values;
  protected $_defaults = array(
    'tmpl_dir' =>      'tmpl/',
    'view.response' => 'http_response'
    );

  public function get($k) {return @$this->_values[$k];}
  public function set($k, $v) {$this->_values[$k] = $v;}
  public function set_all($_) {$this->_values = (array)$_;}
  public function get_all() {return $this->_values;}

  public function reveal($t) {
  
   $this->get_popper()->pop($this->inform("view.response"))->reveal();
   include $this->inform('main_root').$this->inform('tmpl_dir').$t.'.tmpl.php'; // XXX 
  }
 }
?>
