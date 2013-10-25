<?php
 namespace t0t1\mysfw\frame\contract;
 use t0t1\mysfw\frame;

 interface dna {
  public function set_popper(frame\contract\popper $_);
  public function get_popper();

  public function set_reporter(frame\contract\reporter $_);
  public function get_reporter();

  public function set_configurator(frame\contract\configurator $_);
  public function get_configurator();

  public function report_debug($msg);
  public function report_info($msg);
  public function report_warning($msg);
  public function report_error($msg);

  public function inform($c);
  public function define($c, $v);

  public function get_ready();
 }

?>
