<?php

 /*
  * XXX Draft
  * XXX Unit tests missing
  */

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 class uri_rewriter extends frame\dna implements frame\contract\dna {
  protected $_defaults = [
   'uri_rewriter:rules' => []
  ];

  // XXX Draft
  public function rewrite(request $request) {
   $server = $request->get_server();
   if(! $server['REQUEST_URI']){
    $this->report_debug('No REQUEST_URI found - Nothing to rewrite');
    return $this;
   }
   $this->report_debug("REQUEST_URI `{$server['REQUEST_URI']}` found");
   foreach($this->inform('uri_rewriter:rules') as $rule => $prm){
    if(1 === preg_match($rule, $server['REQUEST_URI'], $matches)) {
     $this->report_debug("Rule `$rule` matches");
     $i=1;
     foreach($prm as $name => $value) {
      if ($value !== null || isset($matches[$i])) {
       $request->set('query', $name, ($value !== null ? $value : $matches[$i++]));
      }
     }
     return $this;
    }
    $this->report_debug("Rule `$rule` does not match");
   }
   return $this;
  }
 }
