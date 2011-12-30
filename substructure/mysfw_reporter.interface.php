<?php
/**
 * XXX what about leg ceil ?
 */

 interface mysfw_reporter {

  public function report_debug($msg);
  public function report_info($msg);
  public function report_warning($msg);
  public function report_error($msg);

 }

?>
