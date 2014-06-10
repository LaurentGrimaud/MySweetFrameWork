<?php
$popper = call_user_func(require '/t0t1/mysfw/init.php', __DIR__);

$popper->register('data_storage', 'mysql_data_storage');
$c = $popper->indicate('configurator');
$c->define('mysql:host', 'localhost');
$c->define('mysql:user', 'root');
$c->define('mysql:pass', 't0t1');
$c->define('operators:generic_definitions', ['id' => null]);
$c->define('operators:custom_definitions', [
  'user' => ['id' => null]
]);

$popper->register('reporter', 'file_reporter')->report_info('Ready to work !'); // We want our reporter to be a file_reporter
$fr = $popper->indicate('reporter');

//echo $c->dump();


echo  "\n1. UID-based recall\n";
$user = $popper->pop('operator')->morph('user');
$user->set_reporter($fr);
$user->identify('id', 1)->recall();
echo "My name is ".$user->get('name')."\n";


echo  "\n2. Recall based on other criteria\n";
$user = $popper->pop('operator')->morph('user');
$user->identify('nick', "l00")->recall();
echo "My name is ".$user->get('name')."\n";


echo "\n3. Update after recall\n";
$user->set('name', $user->get('name')."#")->update();


echo "\n4. Create\n";
$user = $popper->pop('operator')->morph('user');
$user->set('name', 'John Prout-Prout The '.time())->set('nick', 'John P.P.');
$user->create();
echo "My id is {$user->get('id')}\n";


echo "\n5. Update after create\n";
$user->set('name', $user->get('name').'*')->update();


echo "\n6. Update from scratch\n";
$user = $popper->pop('operator')->morph('user');
$user->identify('nick', 'l00')->set('email', 'hahahah@hohohoho.net')->update();
var_dump($user->get_values());


echo "\n7. UID change\n";
echo "My id is {$user->get('id')}\n";
$user->set('id', $user->get('id')+100000)->update();
echo "My id is now {$user->get('id')}\n";
