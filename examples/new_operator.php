<?php

// What I want is...

// 1. One line instanciation
$user = $p->pop('operator')->morph('user'); // XXX could be smaller...

// 2. multi-identification
// like:
$user->identify('id', 123);
// or:
$user->identify('name', 'prout')->identify('pass', 'gniii');

// 4. easy set
$user->set('level', 43);

// 5. one line create, with data_storage created uid return
$user->create();

// 6. one line update
$user->update();

// 7. one line delete
$user->erase();

// 8. one line retrieve
$user->recall();



// ////////////////////////////////////////////////////////////////
// Another way ? (data storages as operator factories)
// 1. retrieve
$user = $user_data_storage->retrieve(...);
$user->set(...)
$user->update()
// or
$user->erase()
// or
// ...

// 2. create
$user = $user_data_storage->create(...);
//...

// 3. Direct update (nonsense in ORM layer ?)

// 4. Direct erase (nonsense in ORM layer ?)
