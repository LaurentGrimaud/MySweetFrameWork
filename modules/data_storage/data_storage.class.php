<?php

 class mysfw_data_storage extends mysfw_core {

  public function retrieve($type, $crit) {
   for($i=0;$i<5;$i++){
    $gna = (object)array("id" => $i);
    foreach($crit as $k=>$v) $gna->$k = $v;
    $res[] = $gna;
   }
   return $res;
  }

  public function add($mysfw_data_object){

  }

  public function change($mysfw_data_object) {
  }

  public function delete($crit) {
  }
 }

?>
