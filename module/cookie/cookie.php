<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

// XXX Unused and refactor needed

class cookie extends mysfw\frame\dna{

    protected $_defaults= array(
        'cookie:expire'=> 0,
        'cookie:path'=> null, 
        'cookie:domain'=> null,
        'cookie:secure'=> false,
        'cookie:http_only'=> false,
    );

    public function set($name, $value, $expire=null, $path=null, $domain=null, $secure=null, $http_only=null){
        $expire = ($expire===null)?$this->inform('cookie:expire'):$expire;
        $path = ($path===null)?$this->inform('cookie:path'):$path;
        $domain = ($domain===null)?$this->inform('cookie:domain'):$domain;
        $secure = ($secure===null)?$this->inform('cookie:secure'):$secure;
        $http_only = ($http_only===null)?$this->inform('cookie:http_only'):$http_only;
        if( ! @setcookie($name, $value, $expire, $path, $domain, $secure, $http_only)){
            $this->report_error('Failed to set cookie with values : ' . json_encode(array('name'=>$name, 'value'=>$value, 'expire'=>$expire, 'path'=>$path, 'domain'=>$domain, 'secure'=>$secure, 'http_only'=>$http_only)));
            throw new \Exception('Failed to set cookie with values : ' . json_encode(array('name'=>$name, 'value'=>$value, 'expire'=>$expire, 'path'=>$path, 'domain'=>$domain, 'secure'=>$secure, 'http_only'=>$http_only)));
        }
        return $this;
    }

    public function get($name=null){
        if($name!==null) return @$_COOKIE[$name];
        return $_COOKIE;
    }

    public function delete($name){
        if(!setcookie($name,"")){
            $this->report_error('Failed to unset cookie with values : ' . json_encode(array('name'=>$name)));
            throw new \Exception('Failed to unset cookie with values : ' . json_encode(array('name'=>$name)));
        }
        return $this;
    }

    public function are_accepted(){ //XXX use object session
        $use_cookie = ini_get('session.use_cookies');
        @ini_set('session.use_cookies', 1);
        $a = @session_id();
        $started = ( is_string( $a ) && strlen( $a ));
        if( !$started ){
            @session_start();
            $a = @session_id();
        }
        $a_data = (isset( $_SESSION ))?$_SESSION:array();
        @session_destroy();
        @session_start();
        $_SESSION = $a_data;
        $b = @session_id();
        if( !$started ) @session_write_close();
        if( !$use_cookie ) @ini_set('session.use_cookies', 0 );
        return ($a === $b);
    }
}
