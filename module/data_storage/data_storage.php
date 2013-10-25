<?php
 /**
  * XXX Obsolete or WIP ?
  * XXX Intended to become an abstract base for all *_data_storage modules ?
  */
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 class data_storage extends frame\dna implements frame\contract\data_storage, frame\contract\dna {

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
