<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

$this->_learn('aliens\lemos\http');
$this->_learn('aliens\lemos\oauth_client');

class twitter extends mysfw\frame\dna{
    protected $_client= null;
    protected $_user= false;
    protected $_defaults= array(
        'auth:twitter:secret'=>"CLIENT_SECRET",
        'auth:twitter:key'=>"CLIENT_KEY",
        'auth:twitter:oauth_callback'=> "",
    );

    protected function _get_ready(){
        $this->_client = new \oauth_client_class;
        $this->_client->debug = 1;
        $this->_client->debug_http = 1;
        $this->_client->server = 'Twitter';
        $this->_client->redirect_uri = $this->inform('auth:twitter:oauth_callback');
        $this->_client->client_id = $this->inform('auth:twitter:key');
        $this->_client->client_secret = $this->inform('auth:twitter:secret');
    }

    public function authenticate($code){
        if(($success = $this->_client->Initialize())){
            if(($success = $this->_client->Process())){
                if(strlen($this->_client->access_token)){
                    $success = $this->_client->CallAPI(
                        'https://api.twitter.com/1.1/account/verify_credentials.json', 
                        'GET', array(), array('FailOnAccessError'=>true), $user);
                    if($success = $this->_client->Finalize($success)){
                        $this->_user= $user;
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function get_auth_url(){
        return '/twitter-connect.php';
    }

    public function logout(){
        $this->_client->ResetAccessToken();
    }

    public function load_user(){
        return $this->_user;
    }
}
