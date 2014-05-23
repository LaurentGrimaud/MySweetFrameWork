<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

$this->_learn('aliens\twitteroauth\twitteroauth\OAuth');
$this->_learn('aliens\twitteroauth\twitteroauth\twitteroauth');

class twitter extends mysfw\frame\dna{
    protected $_client= null;
    protected $_user= false;
    protected $_defaults= array(
        'auth:twitter:secret'=>"CLIENT_SECRET",
        'auth:twitter:key'=>"CLIENT_KEY",
        'auth:twitter:oauth_callback'=> "",
    );

    protected function _get_ready(){
        $this->_client = new \TwitterOAuth($this->inform('auth:twitter:key'), $this->inform('auth:twitter:secret')); // Use config.php client credentials
    }

    public function authenticate($code){
        $this->_client = new \TwitterOAuth($this->inform('auth:twitter:key'), $this->inform('auth:twitter:secret'), $_SESSION['twitter:oauth_token'],$_SESSION['twitter:oauth_token_secret']);
        $token_credentials = $this->_client->getAccessToken($code);
        $this->_client = new \TwitterOAuth($this->inform('auth:twitter:key'), $this->inform('auth:twitter:secret'), $token_credentials['oauth_token'],$token_credentials['oauth_token_secret']);
        return true;
    }

    public function get_auth_url(){
        $temporary_credentials = $this->_client->getRequestToken($this->inform('auth:twitter:oauth_callback'));
        $_SESSION['twitter:oauth_token'] = $temporary_credentials['oauth_token'];
        $_SESSION['twitter:oauth_token_secret'] = $temporary_credentials['oauth_token_secret'];
        return $this->_client->getAuthorizeURL($temporary_credentials);
    }

    public function logout(){
    }

    public function load_user(){
        return $this->_client->get('account/verify_credentials');
    }
}
