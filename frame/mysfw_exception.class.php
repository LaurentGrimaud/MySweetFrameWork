<?php
 /**
  * Empty nutshell for mysfw exceptions
  * Just here to permit a potential special process of exceptions
  */
 namespace mysfw;

 class exception extends \Exception {
  private $_requested_type;

  public function __construct($m, $t = null) {
   parent::__construct($m);
   $this->_requested_type = $t;
  }
 }
