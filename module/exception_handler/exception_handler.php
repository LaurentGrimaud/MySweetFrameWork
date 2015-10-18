<?php
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 /**
  * Top-level handler for exception
  * This is WORK IN PROGRESS
  * @XXX Should handle HTML/CLI output, with developpement/production different behaviors
  */

 class exception_handler extends frame\dna implements frame\contract\dna {

  /** Handle the given exception according to contextual information
   *
   *@XXX Currently behave only like in developpement and HTML context
   */
  public function handle($e) {
   echo "<div>";
   echo "<b>Uncaught exception: </b><pre>\n" . $e->getMessage(). "\n<pre>";
   echo "<b>Trace</b><pre>\n".$e->getTraceAsString()."\n</pre>";
   echo "<div>";
  }

 }
