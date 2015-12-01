<?php

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 class debug_manager extends frame\dna implements frame\contract\dna {
  protected $_errors = [];

  public function error($errno, $errstr, $errfile, $errline, array $errcontext) {
   $this->_errors[] = ['errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline, 'errcontext' => $errcontext ];
  }

  public function get_errors(){return $this->_errors;}
 }
