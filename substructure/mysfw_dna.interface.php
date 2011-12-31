<?php

 interface mysfw_dna {
  public function set_popper(mysfw_popper $_);
  public function get_popper();

  public function set_reporter(mysfw_reporter $_);
  public function get_reporter();

  public function set_configurator(mysfw_configurator $_);
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
