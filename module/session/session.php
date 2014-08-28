<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

class session extends mysfw\frame\dna{

    public function start(){
        if( ! $this->is_active()){
            if( ! session_start()){
                $this->report_error('Session failed to start');
                return false;
            }
        }
        return $this;
    }

    public function destroy(){
        if ( ! $this->is_active() ){ // start session if not started yet
            if( ! session_start()){
                $this->report_error('Session failed to start');
                return false;
            }
        }
        session_unset();
        $_SESSION= array();
        if( ! session_destroy()){
            $this->report_error('Failed to destroy session with id : ' . session_id());
            return false;
        }
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            $params['name']= session_name();
            $params['time']= time()-42000;
            $params['value']= "";
            if( ! setcookie($params['name'], $params['value'], $params['time'],@$params['path'], @$params['domain'], @$params['secure'], @$params['httponly'])){ //XXX should use some method belonging to a cookie object to destroy session's cookie
                $this->report_error('Failed to delete session cookie with values : ' . json_encode($params));
                return false;
            }
        }
        return true;
    }

    public function is_active(){
        return ( PHP_SESSION_ACTIVE== session_status());
    }

    public function get($k=null) {
        if($k===null) return $_SESSION;
        return @$_SESSION[$k];
    }
    public function set($k, $v) {$_SESSION[$k]= $v; return $this;}
    public function delete($k) {unset($_SESSION[$k]); return $this;}

 }
