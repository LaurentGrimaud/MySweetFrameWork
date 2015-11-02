<?php

 /**
  * XXX Draft
  *
  * @missing configuration checks during get_ready()
  */

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 class uri_builder extends frame\dna implements frame\contract\dna {
  // rules => [ 'name' => RULE ]
  // or
  // rules => [ 'name' => ['rule' => RULE, 'ssst' => [ [...,...], ...] ] ]
  
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
   switch(gettype($rules[$name])) {
    case 'string':
     $this->report_debug("Found simple rule named `{$name}`");
     return $this->_build($rules[$name], $args);

    case 'array':
    default:
     $this->report_debug("Found complex rule named `{$name}`");
     $rule = $rules[$name]['rule'];
     $args = $this->_prepare_args($rules[$name]['ssst'], $args);
     return $this->_build($rule, $args);
   }
  }

  protected function _prepare_args($assistants, $args) {
   $res = [];
   for($i=0;$i < count($args); $i++) {
    $res[$i] = $args[$i];
    if(! @$assistants[$i]){
     $this->report_debug("No assistant found for #`$i` argument `{$args[$i]}`");
     continue;
    }
    foreach($assistants[$i] as $sst) {
     $new = $sst($res[$i]);
     $this->report_debug("Processed {$res[$i]} to $new");
     $res[$i] = $new;
    }
   }
   return $res;
  }

  protected function _build($rule, $args) {
   $this->report_debug("Build URI using `{$rule}` with `".json_encode($args).'`');
   return vsprintf($rule, $args); // XXX draft
  }
 }
