<?php
 namespace t0t1\mysfw;

 return function($root) {
  // Minimal set of dependencies
  require_once 'frame/contract/popper.php';
  require_once 'frame/contract/dna.php';
  require_once 'frame/exception/dna.php';
  require_once 'frame/popper.php';    

  return frame\popper::itself($root, __DIR__);
 };
