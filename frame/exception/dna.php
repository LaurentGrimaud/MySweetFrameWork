<?php
 /**
  * Empty nutshell for mysfw exceptions
  * Just here to permit a potential special process of exceptions
  * So every other mysfw exception should extends this one
  */
 namespace t0t1\mysfw\frame\exception;

 class dna extends \Exception {
  private $_requested_type;

  public function __construct($m, $t = null) {
   parent::__construct($m);
   $this->_requested_type = $t;
  }
 }
