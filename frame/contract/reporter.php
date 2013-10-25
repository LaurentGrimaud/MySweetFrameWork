<?php
/**
 * XXX what about log ceil ?
 */
 namespace t0t1\mysfw\frame\contract;

 interface reporter {

  public function report_debug($msg);
  public function report_info($msg);
  public function report_warning($msg);
  public function report_error($msg);

 }

?>
