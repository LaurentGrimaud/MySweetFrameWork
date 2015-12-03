<?php
 namespace t0t1\mysfw\frame\contract;

 interface controller {

  // Pure controller logics
  public function control_and_reveal($p);
  public function control($p);

  // Wrappers to view, to handle data to render
  public function set($k, $v);
  public function get($_);
  public function set_all($_);

  // A controller may be asked its view
  // XXX really ?
  public function get_view();

 }
