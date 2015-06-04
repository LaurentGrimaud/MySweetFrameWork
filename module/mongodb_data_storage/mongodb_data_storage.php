<?php
 /**
  * Implementation of MySFW data storage agains MongoDB
  *
  * This is WORK IN PROGRESS
  *
  * XXX uid support is not yet correct
  * XXX everything here needs to be carefully checked and tested
  * XXX migration to exceptions
  *
  * Requires mongo pecl extension >= 1.3.0 usi MongoClient Class
  * pecl install mongo
  *
  */
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn("frame\contract\data_storage");
 $this->_learn('module\data_storage\exception\db_failure');
 $this->_learn('module\data_storage\exception\connection_failure');
 $this->_learn('module\data_storage\exception\data_storage_exception');
 $this->_learn('module\data_storage\exception\duplicate_key');
 $this->_learn('module\data_storage\exception\invalid_parameters');
 $this->_learn('module\data_storage\exception\wrong_key');
 $this->_learn('module\data_storage\exception\no_entry');
 $this->_learn('module\data_storage\exception\too_many_entries');

 class mongodb_data_storage extends frame\dna implements frame\contract\data_storage, frame\contract\dna {
  protected $_defaults = array(
    "mongo:db" => "mysfw_demo",
    "mongo:iterator"=> "mongodb_iterator"
   );
  protected $_collection_options = array(
    //"safe" => true => deprecated w=1 replace safe option
   );
   
   protected $_mns = '\t0t1\mysfw\module\data_storage'; //XXX used by dna:except()
 protected $_exceptions = [   // XXX TEMP exceptions definition
   'connection_failure' => 1,
   'db_failure' => 1,
   'duplicate_key' => 1,
   'data_storage_exception' => 1,
   'invalid_parameters' => 1,
   'wrong_key' => 1,
   'duplicate_key' => 1,
   'no_entry' => 1,
   'too_many_entries'=>1,
    ];

    protected $_iterator= false;

    protected function _get_ready(){
        $this->_iterator= $this->pop($this->inform('mongo:iterator')); //XXX not phpunit compatible
    }

    public function set_iterator(frame\contract\resource_iterator $iterator){
        $this->_iterator= $iterator;
    }
   
   protected function _build_connection_string(){ // XXX TEMP Should be private
    $connection_string = "";
    if( $this->inform('mongo:host') || $this->inform('mongo:user') || $this->inform('mongo:pass') || $this->inform('mongo:port')){
        $connection_string = "mongodb://";
    }
    if( $this->inform('mongo:user') && $this->inform('mongo:pass')){
        $connection_string .= $this->inform('mongo:user') . ':' . $this->inform('mongo:pass') . '@';
    }
    if( $this->inform('mongo:host')){
        $connection_string .= $this->inform('mongo:host');
    }
    else{
        if( $this->inform('mongo:port')){
            $connection_string .= 'localhost';
         }
    }
    if( $this->inform('mongo:port')){
        $connection_string .= ':' . $this->inform('mongo:port');
    }
    return $connection_string;
   }

  // XXX temp - draft
  private function _get_connection($type){
   try {
    $connection_string = $this->_build_connection_string();
    if (class_exists('\MongoClient')) {
        $m = new \MongoClient( $connection_string);
    }
    else{
        $m = new \Mongo( $connection_string);
    }
    return $m->selectCollection($this->inform('mongo:db'), $type); // XXX Conf should be taken from configurator object
   }
   catch( \MongoConnectionException $e){
    $this->report_error("Failed to connect to MongoDB, message is: ".$e->getMessage());
    throw $this->except($e->getMessage(), 'connection_failure');
   }
   catch( \Exception $e){
    $this->report_error("Failed to connect to database or collection, message is: ".$e->getMessage());
    throw $this->except($e->getMessage(), 'db_failure');
   } 
  }
  

  // XXX temp - draft - Needs to be in data storage interface
  public function get_connection( $type) {
   try {
    $connection_string = $this->_build_connection_string();
    if (class_exists('\MongoClient')) {
        $m = new \MongoClient( $connection_string);
    }
    else{
        $m = new \Mongo( $connection_string);
    }
    return $m->selectCollection($this->inform('mongo:db'), $type); // XXX Conf should be taken from configurator object
   }
   catch( \MongoConnectionException $e){
    $this->report_error("Failed to connect to MongoDB, message is: ".$e->getMessage());
    throw $this->except($e->getMessage(), 'connection_failure');
   }
   catch( \Exception $e){
    $this->report_error("Failed to connect to database or collection, message is: ".$e->getMessage());
    throw $this->except($e->getMessage(), 'db_failure');
   } 
  }


  /**
   * Build data set uid according to given criteria object
   * Based (!) on memcache_data_storage::_criteria_talk()
   *
   * @param $type string
   * @param $crit object of criteria key/value
   * @return uid as string, false on error
   *
   * XXX useful ?
   */
  private function _criteria_talk($type, $crit){
   if(! $crit){
    $this->report_error("Can't build uid without criteria");
    return false;
   }

   if(! $type){
    $this->report_error("Can't build uid without type");
    return false;
   }

   $crit = (array)$crit;

   if(count($crit) !== 1){
    $this->report_error("Only one part uid supported in this version. Sorry...");
    return false;
   }
   $uid = array_pop($crit);
   $this->report_info("data uid is $uid");
   return $uid;
  }


  // XXX WIP
  /**
   * metacrit[l] = 10 => limit
   * metacrit[o] = 10 => offset
   * metacrit[s][field_name] = 1|-1 (asc ou desc) => sort by field name
   *
   * crit[field_name] = value               // exact match
   * crit[field_name] = ["$gt"  => value]   // strictly greater than
   * crit[field_name] = ["$gte" => value]   // greater than or equal to
   * crit[field_name] = ["$lt"  => value]   // strictly lower than
   * crit[field_name] = ["$lte" => value]   // lower than or equal to
   */
  public function retrieve($type, $crit = null, $metacrit = null) {
    $this->report_info('`retrieve` action requested');
    $c = $this->_get_connection($type);
    if( empty( $this->_connection_options)){
        $this->_build_connection_options();
    }
    if(! $crit) {
     $crit = array();
    }
    if(false === $data = $c->find($crit)){
        $this->report_warning("Failed to retrieve data (type $type)");
        return null;
    }
    if($metacrit) {
        if( array_key_exists('o', $metacrit) ){
          $data->skip($metacrit['o']);
         }
         if(array_key_exists('l', $metacrit)){
          $data->limit($metacrit['l']);
         }

         if(array_key_exists('s', $metacrit)) {
          $data->sort($metacrit['s']);
         }
         $this->report_debug(print_r($metacrit, true));
   }
   $iterator = $this->_iterator->wrap($data, $type);
   $this->report_debug($iterator->count()." `$type` item(s) retrieved");
   return $iterator;
  }


  public function add($type, $crit, $values){
   $this->report_info('`add` action requested');
   $c = $this->_get_connection($type);
   if( empty( $this->_connection_options)){
    $this->_build_connection_options();
   }
   $data_to_insert = (array)$values;
   if( array_key_exists( '_id', $data_to_insert) && empty( $data_to_insert['_id'])){
    throw $this->except("We tried to save un document with empty _id property !!", "data_storage_exception");
   }
   try {
    $doc_o = $c->insert($data_to_insert);
    $uid = @$data_to_insert['_id'];
    if( empty( $uid)){
        $uid = $doc_o['_id'];
    }
    $this->report_debug("`$type` item set, with uid $uid");
    return $uid;

   }
   catch( \MongoCursorException $e){
    if( $e->getCode() == "11000"){
        //$this->report_error("duplicate key : " . $uid);
        throw $this->except($e->getMessage(), 'duplicate_key');
    }
    else{
        throw $this->except($e->getMessage(), 'data_storage_exception');
    }
   }
   catch( \MongoException $e){
    $this->_exception_management( $e);
   }
   catch( \Exception $e){
        throw $this->except($e->getMessage(), 'data_storage_exception');
    }   
  }
  

 // XXX duplicate of add 
  private function _save($type, $crit, $values) {
   $c = $this->_get_connection($type);
   if( empty( $this->_connection_options)){
    $this->_build_connection_options();
   }
   $values = (array)$values;
   if( array_key_exists( '_id', $values) && empty( $values['_id'])){
    throw $this->except("We tried to save un document with empty _id property !!", "data_storage_exception");
   }
   try {
    $doc_o = $c->save($values);
    $uid = (string)(@$values['_id']);
    if( empty( $uid)){
        $uid = $doc_o['_id'];
    }
    $this->report_debug("`$type` item set, with uid $uid");
    return $uid;

   }
   catch( \MongoException $e){
    $this->_exception_management( $e);
   }
   catch( \MongoCursorException $e){
    if( $e->getCode() == "11000"){
        $this->report_error("duplicate key : " . $uid);
        throw $this->except($e->getMessage(), 'duplicate_key');
    }
    else{
        throw $this->except($e->getMessage(), 'data_storage_exception');
    }
   }
   catch( \Exception $e){
        throw $this->except($e->getMessage(), 'data_storage_exception');
    }   
  }


  // XXX old implementation, using retrieve then change
  public function OLD_change($type, $crit, $values){
   $this->report_info('`change` action requested');

   if(! $entity_list = (array)$this->retrieve($type, $crit)){ // XXX temp cast
    return false;
   }

   $previous_data = array_pop($entity_list);

   foreach($values as $p => $v){
    $previous_data[$p] = $v;
   }

   return $this->_save($type, $crit, $previous_data);
  }

  // New implementation, using MongoCollection::update()
  public function change($type, $crit, $values){
   $this->report_info('`change` action requested');
   $c = $this->_get_connection($type);
   if( empty( $this->_connection_options)){
    $this->_build_connection_options();
   }
   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    throw $this->except('Failed to build uid', 'data_storage_exception');
   }
   if(isset( $values['_id'])) $values_id= $values['_id'];
   unset($values['_id']);
   $this->report_debug(print_r($values, true));
   try {
    if($res = $c->update(array("_id" => $uid), array('$set' => $values))) {
     if( $res['n'] == 0){
        if( $res['updatedExisting'] === true){
            $this->report_error("Database error during update for uid : " . $uid);
            throw $this->except('Database error during update : ' . $res['err'], 'data_storage_exception');
        }
        else{
            $this->report_error("No entry found for uid : " . $uid);
            throw $this->except('No entry found for uid : ' . $uid, 'no_entry');
        }
     }
     if(isset( $values_id)) $values['_id'] = $values_id;
     $this->report_debug(print_r($res, true));
     $this->report_debug("Item of type $type and uid $uid updated");
     return true;
    }

    $this->report_error("Failed to update item of type $type and uid $uid from MongoDB");
    return false;
    //should throw data_storage exception ?
   }catch( \MongoException $e){
    $this->_exception_management( $e);
   }
  }


  /**
   * @throw data_storage_exception if a mongodb error occurs
   * @throw too_many_entries exception if more than one documents are removed for the same uid
   * @throw no_entry exception if no document found for uid OK ?????
   *         1 if one item found and deleted
   *         0 if no item found (and deleted)
   **/
  public function delete($type, $crit){
   $this->report_info('`delete` action requested');
   $c = $this->_get_connection($type);
   if( empty( $this->_connection_options)){
    $this->_build_connection_options();
    }
   if(! $uid = $this->_criteria_talk($type, $crit)){
    $this->report_error("Failed to build uid - Aborting");
    throw $this->except('Failed to build uid', 'data_storage_exception');
   }
   try {
    $res = $c->remove(array("_id" => $uid), $this->_collection_options);

    if($res === false || @$res['err']) {
     $this->report_error("Failed to remove item of type $type and uid $uid from MongoDB");
     throw $this->except("Failed to remove item of type $type and uid $uid from MongoDB", "data_storage_exception");
     return false;
    }

    if($res === true || $res['n'] === 1) {
     $this->report_debug("Item of type $type and uid $uid removed from MongoDB - ".print_r($res, true));
     return 1;
    }

    if($res['n'] === 0) {
     $this->report_warning("No item of type $type and uid $uid to remove from MongoDB");
     throw $this->except("No item of type $type and uid $uid to remove from MongoDB", "no_entry");
    }

    $this->report_error("Something strange just happened: {$res['n']} items of $type and uid $uid removed from MongoDB");
    throw $this->except("More than one document removed for uid : " . $uid, "too_many_entries");

   }catch( \MongoException $e){
    $this->_exception_management( $e);
   }
  }
  
  private function _exception_management( \MongoException $e){
    switch ($e->getCode()){
        case '1':
            $type = 'wrong_key';
            $message = 'You are trying to save a document with empty key';
            break;
        case '2':
            $type = 'wrong_key';
            $message = 'You are trying to save a document with not allowed format key : ' . $e->getMessage();
            break;
        case '3':
            $type = 'invalid_parameters';
            $message = 'Insert or update parameters weight is too big : '. $e->getMessage();
            break;
        case '4':
            $type = 'invalid_parameters';
            $message = 'You are trying to save an empty document : ' . $e->getMessage();
            break;
        case '5':
            $type = 'wrong_key';
            $message = 'You are trying to save a document with non string keys : ' . $e->getMessage();
            break;
        default:
            $type = 'data_storage_exception';
            $message = $e->getMessage();
     }
     throw $this->except($message, $type); 
  }
  
  /* mongo collection options management for save, find, delete update
  ** parameter is an array with php mongodb driver options as keys
  ** w : write context MongoClient default value = 1, supported commeon value : 0, 1, N, majority (replace safe option)
  ** j : boolean, journal option if true The write will be acknowledged by primary and the journal flushed to disk
  ** justOne : boolean, for delete only, only one document with the good criteria will be deleted unused here because operator is used by mongodb_data_storage functions
  ** fsync : boolean defaukt value = false, see http://php.net/manual/fr/mongocollection.remove.php
  ** socketTimeoutMS : milliseconds limit duration for a socket communication, default value for MongoClient : 30000 (replace timeout option)
  ** wTimeoutMS : milliseconds write action limit duration, only if w > 1, default value for MongoClient : 10000  (replace wtimeout option)
  */
  private function _build_connection_options(){
    $this->_connection_options = [];
    if (class_exists('\MongoClient')) {
        $supported_options = ['w', 'j', 'justOne', 'fsync', 'socketTimeoutMS', 'wTimeoutMS'];
        $options =  $this->inform('mongo:options');
        if ( !empty( $options)){
            foreach( $options as $key=>$value){
                if( in_array( $key, $supported_options)){
                    $this->_connection_options[$key] = $value;
                 }
                 $this->report_debug('Skipping unsupported option : ' . $key);
            }
        }
    }
    else{
        // Mongo options
        $this->_connection_options['safe'] = true;
    }
  }
 }
