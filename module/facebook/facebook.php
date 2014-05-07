<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;
require_once "/t0t1/mysfw/aliens/facebook-php-sdk/src/facebook.php";
//set_include_path("/t0t1/mysfw/aliens/facebook-php-sdk/src/" . PATH_SEPARATOR . get_include_path());
//$this->_learn("aliens\facebook-php-sdk\src\facebook");

class facebook extends mysfw\frame\dna{
    protected $_handler = null;
    protected $_defaults = array(
        'auth:facebook:appId'=> 'YOUR_APP_ID',
        'auth:facebook:secret'=> 'YOUR_APP_SECRET',
        'auth:facebook:redirect_uri'=> 'YOUR_REDIRECT_URI',
        'auth:facebook:scope'=> 'email,user_birthday,user_location,user_hometown',
    );

    protected function _get_ready(){
        $fb_config = array(
            'appId' => $this->inform('auth:facebook:appId'),
            'secret' => $this->inform('auth:facebook:secret'),
            'fileUpload' => false, // optional
            'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
        );

        $this->_handler = new \Facebook($fb_config);
    }

    public function load_user(){
        if($this->_handler->getUser()){
            try {
                $me = $this->_handler->api('/me');
                $this->report_info('authentified facebook profile: ' . json_encode($me));

            } catch (\FacebookApiException $e) {
                $this->report_error($e);
                $this->_handler->destroySession();
                throw $e;
            }
            return $me;
        }
        return null;
    }

    public function authenticate(){
        if($user = $this->_handler->getUser()){
            try {
                $me = $this->_handler->api('/me');
                $this->report_info('authenticated using facebook');
                return $me;
            } catch (\FacebookApiException $e) {
                $this->report_error($e);
                $this->_handler->destroySession();
                return false;
            }
        }
        return false;
    }

    public function get_auth_url(){
        return $this->_handler->getLoginUrl(array('redirect_uri'=>$this->inform('auth:facebook:redirect_uri'),'scope'=>$this->inform('auth:facebook:scope')));
    }

    public function logout(){
        return $this->_handler->destroySession();
    }
}
