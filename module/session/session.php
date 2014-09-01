<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

class session extends mysfw\frame\dna{

    public function start(){
        if( ! $this->is_active()){
            if( ! @session_start()){
                $this->report_error('Session failed to start');
                throw new \Exception('Session failed to start');
            }
            $this->report_debug('Session has started');
        } else $this->report_debug('Session already started');
        return $this;
    }

    public function destroy(){
        $this->start();
        session_unset();
        $_SESSION= array();
        if( ! session_destroy()){
            $this->report_error('Failed to destroy session with id : ' . session_id());
            throw new \Exception('Failed to destroy session with id : ' . session_id());
        }
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            $params['name']= session_name();
            $params['time']= time()-42000;
            $params['value']= "";
            if( ! @setcookie($params['name'], $params['value'], $params['time'],@$params['path'], @$params['domain'], @$params['secure'], @$params['httponly'])){ //XXX should use some method belonging to a cookie object to destroy session's cookie
                $this->report_error('Failed to delete session cookie with values : ' . json_encode($params));
                throw new \Exception('Failed to delete session cookie with values : ' . json_encode($params));
            }
        }
        $this->report_debug('Session destroyed');
        return true;
    }

    public function is_active(){
        return ( PHP_SESSION_ACTIVE== session_status());
    }

    public function get($k=null) {
        $this->start();
        if($k===null) return $_SESSION;
        return @$_SESSION[$k];
    }
    public function set($k, $v) {
        $this->start();
        $_SESSION[$k]= $v; 
        return $this;
    }
    public function delete($k){
        $this->start();
        unset($_SESSION[$k]);
        return $this;
    }

 }
