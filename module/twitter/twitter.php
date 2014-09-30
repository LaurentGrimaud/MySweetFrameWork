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
        'auth:twitter:acces_token'=> "ACCESS_TOKEN",
        'auth:twitter:acces_token_secret'=> "ACCESS_TOKEN_SECRET",
        'auth:twitter:oauth_callback'=> "",
        'twitter:search:url'=>"https://api.twitter.com/1.1/search/tweets.json",
        'twitter:post:url'=>"https://api.twitter.com/1.1/statuses/update.json"
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
    
    
    public function get_tweets_by_hashtag( $hashtag, $since_id = null, $until_date = null, $nb = null){
        return $this->_get_tweets( $hashtag, $since_id, $until_date, $nb);
    }
    
     public function post_tweet( $tweet_text, $origin_tweet_id = null, $twitter_user_name = null){
        if( $origin_tweet_id && ! $twitter_user_name){
            return false;
        }
        // start connection
        $client = new \TwitterOAuth($this->inform('auth:twitter:key'), $this->inform('auth:twitter:secret'), $this->inform('auth:twitter:acces_token'), $this->inform('auth:twitter:acces_token_secret'));
        
        //send message
        $paramx = array();
        $paramx['status'] = '';
        if( $twitter_user_name){
            $paramx['status'] .= ' @' . $twitter_user_name;
        }
        $paramx['status'] .= $tweet_text;
        if( $origin_tweet_id){
            $paramx['in_reply_to_status_id'] = $origin_tweet_id;
        }    
        return $client->post($this->this->inform('twitter:post:url', $paramx);    
    }
    
    protected function _get_tweets( $param, $since_id = null, $until_date = null, $nb = null){
       if( $until_date){
           // verification date format
           $format ="Y-m-d"; 
           if( false === \DateTime::createFromFormat($format,$until_date)){
            return false;
           }
       }
       // twitter api authentication read_only method
       $client = new \TwitterOAuth($this->inform('auth:twitter:key'), $this->inform('auth:twitter:secret'), $this->inform('auth:twitter:acces_token'), $this->inform('auth:twitter:acces_token_secret'));
       $url = $this->inform('twitter:search:url') . '?q=' . urlencode($param);
       if( $since_id){
        $url .= '&since_id=' . $since_id;
       }
       if( $until_date){
        $url .= '&until=' . $until_date;
       }
       if( $nb){
        $url .= '&count=' . $nb;
       }
       return $client->get($url);
   
    }
}
