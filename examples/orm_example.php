<?php
 require('../popper.class.php');
 $popper = mysfw_popper::itself();

 $popper->register('reporter', 'file_reporter'); // Default reporter now set to file_reporter
// $popper->register('data_storage', 'mysql_data_storage'); // Default data storage now set to mySQL
// $popper->register('data_storage', 'memcache_data_storage'); // Default data storage now set to MemCache
// $popper->register('data_storage', 'redis_data_storage'); // Default data storage now set to Redis

 $popper->register('data_storage',       'cached_data_storage'); // Default data storage now set to composite cached data storage
 $popper->register('base_data_storage',  'mysql_data_storage'); 
 $popper->register('cache_data_storage', 'redis_data_storage');

 $popper->indicate("data_storage")->set_base_data_storage($popper->indicate("base_data_storage"));
 $popper->indicate("data_storage")->set_cache_data_storage($popper->indicate("cache_data_storage"));

 $popper->swallow("operator"); // XXX temp
 require('../customs_operators/user_operator.class.php'); // XXX no explicit require

 // What I want is:
 // 1. Easily create an object mapped to an entry in a data storage (one line)
 // 2. Manipulate data in a data storage using a dedicated object
 //$user = $popper->pop('operator.user');

 $user = new user_operator; // XXX no direct instanciation
 $user->set_data_storage($popper->indicate("data_storage")); // XXX needs an implicit configuration system

 // creation
 $user->set('pseudo', 'Harry Ketchup');
 $user->set('level', 45);
 if(false === $uid = $user->save()){
  echo "Creation failed !\n";
  exit();
 }

 echo "User created ! (uid: $uid)\n";
 // update
 $user->set('level', 53);
 if(false === $user->save()){
  echo "Update failed !\n";
  exit();
 }

 echo "User updated !\n";


 // update from scratch
 $user = new user_operator($uid); // XXX no direct instanciation
 $user->set_data_storage($popper->indicate("data_storage")); // XXX needs an implicit configuration system

 $user->set('level', 47);

 if(false === $user->save()){
  echo "Update from scratch failed !\n";
  exit();
 }

 echo "User updated from scratch!\n";

 // data retrieval
 $user = new user_operator($uid); // XXX no direct instanciation
 $user->set_data_storage($popper->indicate("data_storage")); // XXX needs an implicit configuration system

 if(! $user->recall()){
  echo "User retrieval failed !\n";
  exit();
 }

 var_dump($user->get_values());

 // data retrieval
 if(false === $user->erase()){
  echo "Erase failed\n";
 }else{
  echo "Erase done\n";
 }


?>
