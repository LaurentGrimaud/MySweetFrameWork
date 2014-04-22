<?php namespace t0t1\mysfw\module;
use t0t1\mysfw;

class request extends mysfw\frame\dna{

    protected $_query= array();
    protected $_post= array();
    protected $_server= array();
    protected $_files= array();

    protected $_defaults= array(
        'INPUT_GET'=> array(),
        'INPUT_POST'=> array(),
        'INPUT_SERVER'=> array(),
        'INPUT_FILES'=> array()
    );

    protected function _get_ready(){
        $this->_query = $this->inform('request:INPUT_GET')?:$_GET;
        $this->_post = $this->inform('request:INPUT_POST')?:$_POST;
        $this->_server = $this->inform('request:INPUT_SERVER')?:$_SERVER;
        $this->_files = $this->inform('request:INPUT_FILES')?:$_FILES;
    }
    public function get_query($k=null) {
        if(empty($k)  and $k!==0) return $this->_query;
        if(isset($this->_query[$k])) return $this->_query[$k];
        return null;
    }

    public function get_post($k=null) {
        if(empty($k)  and $k!==0) return $this->_post;
        if(isset($this->_post[$k])) return $this->_post[$k];
        return null;
    }

    public function get_server($k=null) {
        if(empty($k)  and $k!==0) return $this->_server;
        if(isset($this->_server[$k])) return $this->_server[$k];
        return null;
    }

    public function get_files($k=null) {
        if(empty($k)  and $k!==0) return $this->_files;
        if(isset($this->_files[$k])) return $this->_files[$k];
        return null;
    }

    public function is_post(){
        return ($this->get_server('REQUEST_METHOD')=='POST');
    }
}
