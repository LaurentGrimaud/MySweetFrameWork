<?php
 require('../popper.class.php');
 $popper = mysfw_popper::itself();

 $tmpl = $popper->pop('view');
 $fds = $popper->pop("mysql_data_storage");

 $tmpl->set('create', $fds->add('users', array('user_active' => 1)));
 
 $tmpl->set('title', 'Oh yeah !');
 $tmpl->set('users', $fds->retrieve('users', array('user_active' => 1)));

 $tmpl->reveal('example'); 
?>
