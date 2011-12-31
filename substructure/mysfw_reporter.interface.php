<?php
/**
 * XXX what about log ceil ?
 */

 interface mysfw_reporter {

  public function debug($msg);
  public function info($msg);
  public function warning($msg);
  public function error($msg);

 }

?>
