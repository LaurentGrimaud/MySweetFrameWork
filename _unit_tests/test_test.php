<?php


 interface Bidule {
  function truc();
 }

 namespace prout;
 class Machin {
  function truc(Bidule $chose) {return $chose->truc();}
 }


 namespace testouille;

 class machinTest extends \PHPUnit_Framework_TestCase {
  function testHAHAHA() {
   $x = new \prout\Machin;

   $y = $this->getMock('Bidule');
   $y->expects($this->once())
     ->method('truc');

   $x->truc($y);

  }
 }
