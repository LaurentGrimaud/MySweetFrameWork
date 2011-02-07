<?php
 require('popper.class.php');

 $popper = mysfw_popper::itself();

 $popper->swallow("view");

 $tmpl = $popper->pop('view');
 
 $tmpl->set('title', 'Oh yeah !');

 $tmpl->reveal('example'); 
?>
