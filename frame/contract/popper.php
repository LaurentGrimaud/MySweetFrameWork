<?php
 namespace t0t1\mysfw\frame\contract;

 interface popper {

  public static function itself($root, $home);

  public function pop($classname);

  public function swallow($modulename);

  public function set_home($v);
  public function get_home();

  public function register($name, $stuff);
  public function indicate($name);

 }
?>
