<?php

function grrr($mdt_local){
	echo "*** Object configuration ***\n";
	echo $mdt_local->dump_all_conf_data();
	echo "****************************\n\n";
}

$popper = call_user_func(require __DIR__.'/../init.php', __DIR__ );
$c = $popper->indicate('configurator');

// Main and central configuration
$c->define('mysql:host', 'default.server.net');

// Alternate and central configuration
$c->define('mysql:host', 'alternate.server.net', 'alt');

// Central configuration
$mdt = $popper->pop('mysql_data_storage');

echo "****** Configurator after central configuration ******\n";
echo $c->dump();

// Local configuration
$mdt_local = $popper->pop('mysql_data_storage', 'haha', ['mysql:host' => 'prout.net', 'mysql:user' => 'jean-paul']);

echo "****** Configurator after local configuration ******\n";
echo $c->dump();


// Local configuration
$mdt_local_2 = $popper->pop('mysql_data_storage', 'haha', ['mysql:host' => 'bizou.net', 'mysql:user' => 'jean-marie']);

echo "****** Configurator after second local configuration ******\n";
echo $c->dump();

grrr($mdt);
grrr($mdt_local);
grrr($mdt_local_2);


