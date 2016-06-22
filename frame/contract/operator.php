<?php
 namespace t0t1\mysfw\frame\contract;

 /** Interface for operator (data entry operator) role
  * @XXX Draft
  */
 interface operator {
  public function get($property);
  public function set($property, $value);

  public function create();
  public function update($uptodate_is_error = true);
  public function recall($rank = null);
  public function erase();
 }
