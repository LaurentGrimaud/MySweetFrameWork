<?php
$popper = call_user_func(require '/t0t1/mysfw/init.php', __DIR__);

$c = $popper->indicate('configurator');
$c->define('mysql:host', 'localhost');
$c->define('mysql:user', 'root');
$c->define('mysql:pass', 't0t1');
$c->define('operators:generic_definitions', ['id' => null]);  // XXX draft for generic operator behavior
$c->define('operators:custom_definitions', [                  // Custom definitions for specific operators
  'user' => ['id' => null]
]);

$popper->register('reporter', 'file_reporter')->report_info('Ready to work !'); // We want our reporter to be a file_reporter
$popper->register('data_storage', 'mysql_data_storage');                        // Default data storage now set to MySQL

$ds = $popper->indicate('data_storage');

$entries = $ds->retrieve('user');
foreach($entries as $entry){
 $operators[] = $popper->pop('operator')->morph('user')->identify('id', $entry->id)->set_values($entry);
}

foreach($operators as $operator) {
 echo $operator->get('name')." ---\n";
}

$operator->set('gender', 'Mr')->update();
