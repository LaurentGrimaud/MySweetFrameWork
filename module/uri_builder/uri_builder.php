<?php

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 class uri_builder extends frame\dna implements frame\contract\dna {
  protected $_defaults = [
   'uri_builder:rules' => []
  ];


  public function build() {
   $args = func_get_args();
   if(! $name = array_shift($args)){ // XXX name === 0 ...?
    $this->report_error("No name given");
    throw $this->except();   // XXX
   }
   $rules = $this->inform('uri_builder:rules');
   if(! $rules[$name]) {
    $this->report_error("No rule with`{$name}` found");
    throw $this->except();   // XXX
   }
   $this->report_debug("Build URI using `{$rules[$name]}` with `".json_encode($args).'`');
   return vsprintf($rules[$name], $args); // XXX draft
  }
 }
