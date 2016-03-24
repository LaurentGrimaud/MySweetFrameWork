<?php

 /* Generic REST controller
  * WIP
  * 
  * Action == HTTP verb
  * params syntax:
  *  criteria are prefixed by @ (ie: @id=11111)
  *  meta are prefixed by ^ (ie: ^desc=created_on) 
  */

 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\controller');
 $this->_learn('module\controller_base\controller_base');

 class rest_controller extends controller_base implements frame\contract\controller, frame\contract\dna {
   protected $_defaults = [
    'rest:response'              => 'http_response',
    'rest:mime_type_placeholder' => 'response:mime-type',
    'rest:mime_type'             => 'application/json',
    'rest:entity_placeholder'    => 'entity',
    'rest:entity_id_placeholder' => 'entity_id',
    'rest:data_storage'          => 'data_storage',
    'rest:post_data'             => 'data',
    'rest:tmpl'                  => 'rest.tmpl',
    'rest:entities_whitelist'    => [] // Default is _no_ entities allowed
   ];

  protected $_ds_actions = [
    'GET'    => 'retrieve',
    'PUT'    => 'change',
    'POST'   => 'add',
    'DELETE' => 'delete'
  ];

  protected function _get_ready() {
   $this->_set_tmpl($this->inform('rest:tmpl'));
   $this->_v = $this->pop($this->inform('rest:response'), 
     $this->get_configuration_context(),
     [ $this->inform('rest:mime_type_placeholder') => $this->inform('rest:mime_type') ]
     );
  }

  protected function _check_entity($request) {
   $entity = $request->get_query($this->inform('rest:entity_placeholder'));
   if(! $entity) 
    throw $this->except('No entity name found in request');
   if(! in_array($entity, $this->inform('rest:entities_whitelist'))){
    throw $this->except("Entity $entity is not REST allowed");
   }
   return $entity;
  }

  protected function _check_entity_id($request, $mandatory = true) {
   $entity_id = $request->get_query($this->inform('rest:entity_id_placeholder'));
   if(! $entity_id && $mandatory) 
    throw $this->except('No entity id found in request');
   return $entity_id;
  }

  protected function _check_definition($entity, $values) {
   if(count($values) != 1)
    throw $this->except('Entity uid needs to be of exactly _one_ part');
   $defs = $this->inform('rest:definitions');
   if(! isset($defs[$entity]))
    throw $this->except("No definitions found for entity $entity");
   if(count($defs[$entity]) != 1)
    throw $this->except("Entity definion must be of exactly _one_ part"); 
   return [current($defs[$entity]) => current($values)];
  }

  protected function _build_criteria($entity, $request, $mandatory = true) {
   $criteria = [];
   $ft_criteria = []; // XXX temp
   // "Primary key" part of criteria
   if($entity_id = $this->_check_entity_id($request, $mandatory))
    $criteria = $this->_check_definition($entity, [$entity_id]);
   // Potential extra criteria
   foreach($request->get_query() as $k => $v) {
    $this->report_debug("Found param $k = ".print_r($v, true));
    switch($k[0]){
     case '@':
      $crit = substr($k, 1);
      if(isset($criteria[$crit]) && $criteria[$crit] != $v) {
       throw $this->except(sprintf("Criteria collision for key %s: %s found but %s already defined", $crit, $v, $criteria[$crit])); 
      }
      $criteria[$crit] = $v;
      break;

     case '~':
      $crit = substr($k, 1);
      if(isset($ft_criteria[$crit]) && $ft_criteria[$crit] != $v) {
       throw $this->except(sprintf("Full-text criteria collision for key %s: %s found but %s already defined", $crit, $v, $ft_criteria[$crit])); 
      }
      $ft_criteria[$crit] = $v;
      break;
    }
   }
   return ['crit' => $criteria, 'ft_crit' => $ft_criteria]; 
  }

  protected function _build_meta($request) {
   $res = [];
   foreach($request->get_query() as $k => $v){
    if($k[0] == '^') {
     $meta = substr($k, 1);
     switch($meta) {

      case 'order':
       foreach($v as $field => $desc){
        $res['s'][$field] = $desc;
       }
       break;

      case 'limit':
       $res['l'] = $v;
       break;

      case 'result_hash':
       $res['h'] = $v;
       break;

      default:
       throw $this->except("Unrecognized meta $meta");
     }
    }
   }

   return $res;
  }

  protected function _build_response($action, $results) {
   $response = [];
   $response['meta']['method'] = $action;
   $response['doc'] = $results;
   return $response;
  }

  protected function _read($request, $entity) {
   $criteria = $this->_build_criteria($entity, $request, false);
   $meta = $this->_build_meta($request);
   $ds = $this->indicate($this->inform('rest:data_storage'));
   $res = $ds->retrieve($entity, $criteria['crit'], $meta, null, $criteria['ft_crit']);
   $response = $this->_build_response('READ', $res);
   return $this->_finalize($response);
  }

  protected function _finalize($response) {
   $this->set('response', $response);
  }

  protected function _update($request, $entity){
   $entity = $this->_check_entity($request);
   $criteria = $this->_build_criteria($entity, $request, false);
   $values = json_decode($request->get_raw_input(), true); // XXX temp - get_raw_input() 
   $ds = $this->indicate($this->inform('rest:data_storage'));
   $res = $ds->change($entity, $criteria['crit'], $values, null, $criteria['ft_crit']);
   $response = $this->_build_response('UPDATE', $res);
   return $this->_finalize($response);
  }

  public function control($request) {
   $method = $request->get_method();
   $entity = $this->_check_entity($request);
   switch($method){
    case 'GET':
     return $this->_read($request, $entity);
    case 'PUT':
     return $this->_update($request, $entity);
    case 'DELETE':
    case 'POST':
    default:
     throw $this->except("Unhandled HTTP method: $method");
   }
  }
 }
