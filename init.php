<?php
 namespace t0t1\mysfw;

 /*
  * @param string root, the web-root of the project
  *  ie the most-upper directory of items available
  *  via HTTP
  */
 return function($root) {
  // Minimal set of dependencies
  require_once 'frame/contract/popper.php';
  require_once 'frame/contract/dna.php';
  require_once 'frame/exception/dna.php';
  require_once 'frame/popper.php';    

  return frame\popper::itself($root, __DIR__);
 };
