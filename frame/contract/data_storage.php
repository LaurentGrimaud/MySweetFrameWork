<?php
 namespace t0t1\mysfw\frame\contract;

 interface data_storage {
  public function retrieve($type, $crit = null, $metacrit = null);
  public function add($type, $crit, $values);
  public function change($type, $crit, $values);
  public function delete($type, $crit);
 }
