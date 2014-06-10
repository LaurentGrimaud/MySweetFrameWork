<?php
$popper = call_user_func(require '/t0t1/mysfw/init.php', __DIR__);

$popper->register('data_storage', 'redis_data_storage');
$c = $popper->indicate('configurator');
$c->define('mysql:host', 'localhost');
$c->define('mysql:user', 'root');
$c->define('mysql:pass', 't0t1');
$c->define('operators:generic_definitions', ['id' => null]);
$c->define('operators:custom_definitions', [
  'user' => ['name' => null, 'email' => null]
]);

$popper->register('reporter', 'file_reporter')->report_info('Ready to work !'); // We want our reporter to be a file_reporter
$fr = $popper->indicate('reporter');

$user = $popper->pop('operator')->morph('user');
var_dump($user->get_uid());
$user->set('name', 'John Prout-Prout')->set('email', 'John@'.time().'.com')->create();

