<?php

 interface mysfw_data_storage {
  public function retrieve($type, $crit, $metacrit);
  public function add($type, $crit, $values);
  public function change($type, $crit, $values);
  public function delete($type, $crit);
 }

?>
