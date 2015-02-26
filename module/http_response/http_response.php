<?php
 /**
  * @example
  * $r= $popper->pop("http_response");
  * $r->define("response.http_status_code", mysfw_http_response::http_status_code_created);
  */
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\response');
 $this->_learn('frame\contract\view');

 class http_response extends frame\dna implements frame\contract\view, frame\contract\response, frame\contract\dna {
  private $_v;                               // Object implementing mysfw_view interface
  protected $_defaults= array(
    'response.http_status_code' => 200,
    'response.http_version'     => 'HTTP/1.1',
    'response.view'             => 'view', // Name of the underlaying MySFW view object to use
    );

  /** 
   * Status lines
   * @todo add all http status line
   */

  //Informational 1xx

  //Succesful 2xx
  const http_status_code_successful=                      200;
  const http_status_code_created=                         201;
  const http_status_code_accepted=                        202;

  //Redirection 3xx
  const http_status_code_moved_permanently=               301;
  const http_status_code_found=                           302;
  const http_status_code_not_modified=                    304;
  const http_status_code_use_proxy=                       305;
  const http_status_code_temporary_redirect=              307;

  //Client Error 4xx
  const http_status_code_bad_request=                     400;
  const http_status_code_unauthorized=                    401;
  const http_status_code_forbidden=                       403;
  const http_status_code_not_found=                       404;
  const http_status_code_method_not_allowed=              405;
  const http_status_code_not_acceptable=                  406;
  const http_status_code_proxy_authentication_required=   407;
  const http_status_code_request_timeout=                 408;
  const http_status_code_conflict=                        409;
  const http_status_code_gone=                            410;
  const http_status_code_length_required=                 411;
  const http_status_code_precondition_failed=             412;
  const http_status_code_request_entity_too_large=        413;
  const http_status_code_request_uri_too_long=            414;
  const http_status_code_unsupported_media_type=          415;
  const http_status_code_requested_range_not_satisfiable= 416;
  const http_status_code_expectation_failed=              417;

  //Server Error 5xx
  const http_status_code_internal_server_error=           500;
  const http_status_code_not_implemented=                 501;
  const http_status_code_bad_gateway=                     502;
  const http_status_code_service_unavailable=             503;
  const http_status_code_gateway_timeout=                 504;

  protected static $_http_reason_phrase= array(
    self::http_status_code_successful=>                      "OK",
    self::http_status_code_created=>                         "Created",
    self::http_status_code_accepted=>                        "Accepted",

    //Redirection 3xx
    self::http_status_code_moved_permanently=>               "Moved Permanently",
    self::http_status_code_found=>                           "Found",
    self::http_status_code_not_modified=>                    "Not Modified",
    self::http_status_code_use_proxy=>                       "Use Proxy",
    self::http_status_code_temporary_redirect=>              "Temporary Redirect",

    //Client Error 4xx
    self::http_status_code_bad_request=>                     "Bad request",
    self::http_status_code_unauthorized=>                    "Unauthorized",
    self::http_status_code_forbidden=>                       "Forbidden",
    self::http_status_code_not_found=>                       "Not Found",
    self::http_status_code_method_not_allowed=>              "Method Not Allowed",
    self::http_status_code_not_acceptable=>                  "Not Acceptable",
    self::http_status_code_proxy_authentication_required=>   "Proxy Authentication Required",
    self::http_status_code_request_timeout=>                 "Request Timeout",
    self::http_status_code_conflict=>                        "Conflict",
    self::http_status_code_gone=>                            "Gone",
    self::http_status_code_length_required=>                 "Length Required",
    self::http_status_code_precondition_failed=>             "Precondition Failed",
    self::http_status_code_request_entity_too_large=>        "Request Entity Too Large",
    self::http_status_code_request_uri_too_long=>            "Request-URI Too Long",
    self::http_status_code_unsupported_media_type=>          "Unsupported Media Type",
    self::http_status_code_requested_range_not_satisfiable=> "Requested Range Not Satisfiable",
    self::http_status_code_expectation_failed=>              "Expectation Failed",

    //Server Error 5xx
    self::http_status_code_internal_server_error=>           "Internal Server Error",
    self::http_status_code_not_implemented=>                 "Not Implemented",
    self::http_status_code_bad_gateway=>                     "Bad Gateway",
    self::http_status_code_service_unavailable=>             "Service Unavailable",
    self::http_status_code_gateway_timeout=>                 "Gateway Timeout",
    );

  protected static $_extra_headers = array(
    self::http_status_code_unauthorized => array(
     'WWW-Authenticate' => 'Basic realm="My Realm"'
     )
    );

  // Response= Status-Line *(( general-header | response-header | entity-header ) CRLF) CRLF
  protected $_http_response_headers= array();

  public function get($k) {return $this->_v->get($k);}
  public function set($k, $v) {$this->_v->set($k, $v);return $this;}
  public function set_all($_) {$this->_v->set_all($_);}
  public function get_all() {return $this->_v->get_all();}


  public function set_http_response_header($_http_response_header_field, $_http_response_header_value){
   $this->_http_response_headers[$_http_response_header_field]= $_http_response_header_value;
   return $this;
  }

  public function get_http_response_header($_http_response_header_field){return $this->_http_response_headers[$_http_response_header_field];}

  public function set_http_response_headers(array $_http_response_headers){
   $ret= true;
   foreach($_http_response_headers as $http_response_header_field => $http_response_header_value){
    $ret= $this->set_http_response_header($http_response_header_field, $http_response_header_value) and $ret;
   }
   return $ret;
  }

  public function get_http_response_headers(){return $this->_http_response_headers;}

  public function reveal($t= null) {
   $this->_send_headers();
   if($this->_v and $t) $this->_v->reveal($t);
  }

  private function _send_headers(){
   // Headers has already been sent, do nothing but log information about headers sent and sender
   if(headers_sent($file, $line)){
    $this->report_warning("The following headers has already been sent by {$file}, l.{$line}: " . print_r(headers_list(),true));
    return;
   }

   $status_code = $this->get('status_code') ? $this->get('status_code') : $this->inform('response.http_status_code');
   if($status_code and self::$_http_reason_phrase[$status_code]){
    // Output status line
    $status_line= $this->inform('response.http_version') . " $status_code " . self::$_http_reason_phrase[$status_code];
    \header($status_line, true, $status_code);
    $this->report_debug("Sent status line: `{$status_line}` for status code: `" . $status_code . "`.");
    // Build extra headers
    if(isset(self::$_extra_headers[$status_code])) {
     foreach(self::$_extra_headers[$status_code] as $header => $value) {
      $this->set_http_response_header($header, $value);
     }
    }
    foreach($this->_http_response_headers as $http_response_header_field => $value){
     // Output response headers
     \header("{$http_response_header_field}: {$value}",true);
     $this->report_debug("Sent header: `{$http_response_header_field}: {$value}` for status code: `" . $status_code . "`.");
    }
   }else{
    $this->report_error("No headers were sent for status code: `" . $status_code . "`.");
    return false;
   }
   return true;
  }

  protected function _get_ready() {
   $this->report_debug("Will create underlaying view object of type ".$this->inform('response.view'));
   $this->_v = $this->get_popper()->pop($this->inform('response.view')); // Wich mysfw view object to use ?
  }
 }
