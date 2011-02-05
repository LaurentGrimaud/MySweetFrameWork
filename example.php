<?php
 require('popper.class.php');

 $popper = mysfw_popper::itself();

 $tmpl = $popper->pop('view');
 
 $tmpl->set('title', 'Oh yeah !');

 $tmpl->reveal('example'); 
?>
