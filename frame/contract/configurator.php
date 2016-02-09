<?php
 namespace t0t1\mysfw\frame\contract;

 interface configurator {
  public function define($c, $v, $cc = '_default_'); // XXX temp
  public function inform($c, $cc = '_default_'); // XXX temp
  public function configure($module);
 }
