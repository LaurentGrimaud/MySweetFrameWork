<?php

 interface mysfw_popper {

  public static function itself($root);

  public function pop($classname);

  public function swallow($modulename);

  public function set_home($v);
  public function get_home();

  public function register($name, $stuff);
  public function indicate($name);

 }
?>
