<?php
 //XXX bad name ?

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\controller');

 abstract class controller_base extends frame\dna implements frame\contract\controller, frame\contract\dna {
  protected $_v;    // Object implementing mysfw_view
  protected $_tmpl; // Template to be used by the view

  // XXX temp - needs a generic way to pass parameters to sub-object
  protected function _set_tmpl($_){$this->_tmpl = $_;return $this;}
  protected function _get_tmpl(){return $this->_tmpl;}

  //Temporary protection against deprecated _set() use 
  protected function _set($k, $v){
   $this->report_warning("_set is deprecated, please use set() instead");
   return $this->set($k, $v);
  }
  // Wrappers to view object, to handle data to render
  public function set($k, $v){$this->get_view()->set($k, $v);return $this;}
  public function get($_){return $this->get_view()->get($_);}
  public function set_all($_){$this->get_view()->set_all($_);return $this;}

  // XXX to be checked
  public function get_view(){if(! $this->_v) throw $this->except("Underlaying view does not exist"); return $this->_v;}

  public function control_and_reveal($p) {
   if(!$this->_v) $this->_prepare_view();
   $this->control($p);
   $this->get_view()->reveal($this->_get_tmpl() ? : $this->_default_tmpl());
  }

  // XXX external dependency to dispatcher conf
  protected function _default_tmpl(){
   $classname = get_class($this);
   $_classname = ( false === ($pos = strrpos($classname, $this->inform('dispatcher:controller_suffix'))))?$classname:substr_replace($classname, '', $pos, strlen($this->inform('dispatcher:controller_suffix'))); // if trailing dispatcher:controller_suffix found, strip it
   return join('', array_slice(explode('\\', $_classname), -1)); // strips namespace
  }

 }
