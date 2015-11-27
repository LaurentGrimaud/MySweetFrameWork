<?php

$popper = call_user_func(require __DIR__.'/../init.php', __DIR__ );


$c = $popper->indicate('configurator');
//print $c->dump();

$multi_conf = $popper->pop('versioned_configurator')->add_version('DEV')->use_version('DEV');
$multi_conf->get_version('DEV')
 ->define('mysql:user', 'root')
 ->define('mysql:pass', 't0t1');
$multi_conf->add_version('PREPROD')->get_version('PREPROD')
 ->define('mysql:user', 'root-preprod')
 ->define('mysql:pass', 't0t1-preprod');

$multi_conf->define('XXX:versioned_configurator:configurator_to_pop', 'versioned_configurator');


$multi_conf2 = $popper->pop('versioned_configurator', 'XXX')->add_version('FR')->use_version('FR');
$multi_conf2->get_version('FR')
 ->define('mysql:user', 'root-fr')
 ->define('mysql:pass', 't0t1-fr');


echo $multi_conf2->dump();
