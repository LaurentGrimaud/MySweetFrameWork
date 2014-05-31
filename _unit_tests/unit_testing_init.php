<?php

 class unit_testing_initializer {
  protected function _learn() {
  }

  public function load($file) {require_once $file;}

  public function __construct() {
   $this->load('frame/contract/dna.php');
   $this->load('frame/contract/popper.php');
   $this->load('frame/contract/configurator.php');
   $this->load('frame/contract/reporter.php');

   $this->load('frame/exception/dna.php');
   $this->load('frame/dna.php');
   $this->load('frame/popper.php');
  }
 }

//$ut_initializer = new unit_testing_initializer();
