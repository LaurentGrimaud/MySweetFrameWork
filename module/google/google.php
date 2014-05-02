<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

set_include_path("/t0t1/mysfw/aliens/google-api-php-client/src/" . PATH_SEPARATOR . get_include_path());
$this->_learn("aliens\google-api-php-client\src\Google\Client");
$this->_learn("aliens\google-api-php-client\src\Google\Service\Oauth2");

class google extends mysfw\frame\dna{
    protected $_client= null;
    protected $_defaults= array(
        'auth:goauth:client_secret'=>"CLIENT_SECRET",
        'auth:goauth:client_id'=>"CLIENT_ID",
        'auth:goauth:scopes'=>array('https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/userinfo.profile'),
        'auth:goauth:simple_api_key'=> 'insert_your_simple_api_key',
        'auth:goauth:application_name'=>'Google+ PHP Starter Application',
        'auth:goauth:redirect_uri'=> "",
        'auth:goauth:token_key'=> "auth_goauth_token_key",
        'auth:goauth:approval_prompt' => 'auto',
    );

    protected function _get_ready(){
        $this->_client = new \Google_Client();
        $this->_client->setApplicationName($this->inform('auth:goauth:application_name'));
        $this->_client->setClientId($this->inform('auth:goauth:client_id'));
        $this->_client->setClientSecret($this->inform('auth:goauth:client_secret'));
        $this->_client->setRedirectUri($this->inform('auth:goauth:redirect_uri'));
        $this->_client->setApprovalPrompt($this->inform('auth:goauth:approval_prompt'));
        $this->_client->setScopes($this->inform('auth:goauth:scopes'));
        $this->_client->setDeveloperKey($this->inform('auth:goauth:simple_api_key'));
        if( isset($_SESSION['goauth']) ) $this->_client->setAccessToken($_SESSION['goauth']);
    }

    public function authenticate(){
        $request = $this->get_popper()->pop('request');
        $filter = $this->get_popper()->pop('filter');
        $code = $filter->apply($request->get_query('code'),array('trim',array($filter,'filter_string')));
        try{
            $this->_client->authenticate($code);
            $_SESSION['goauth']= $this->_client->getAccessToken();
            $this->report_info('authenticated using google');
        } catch(\Google_Auth_Exception $e){
            unset($_SESSION['goauth']);
            $this->report_error($e);
            $this->_client->revokeToken();
            return false;
        }catch(\Google_IO_Exception $e){
            $this->report_error($e);
            unset($_SESSION['goauth']);
            return false;
        }
        return true;
    }

    public function get_auth_url(){
        return $this->_client->createAuthUrl();
    }

    public function logout(){
        unset($_SESSION['goauth']);
        $this->_client->revokeToken();
    }

    public function load_user(){
        if ($this->_client->getAccessToken()) {
            try{
                $google_user = (new \Google_Service_OAuth2($this->_client))->userinfo->get();
                $user['email']= $google_user->getEmail();
                $user['given_name']= $google_user->getGivenName();
                $user['family_name']= $google_user->getFamilyName();
            }catch(\Google_ServiceException $e){
                $this->report_error($e);
                unset($_SESSION['goauth']);
                $this->_client->revokeToken();
                throw $e;
            }
            return $user;
        }
    }
}
