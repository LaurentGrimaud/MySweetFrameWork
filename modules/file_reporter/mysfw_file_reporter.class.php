<?php

 class mysfw_file_reporter extends mysfw_core implements mysfw_reporter, mysfw_dna {
  private $_fd;
  private $_level_ceil = 3;


  protected function _get_ready() {
   if(false === ($log = $this->inform('root').$this->inform('log_file_name'))){return false;}
   return ($this->_fd = fopen($log, 'a')) ? true : false;
  }

  public function debug($msg){return $this->_r($msg, 3);}
  public function info($msg){return $this->_r($msg, 2);}
  public function warning($msg){return $this->_r($msg, 1);}
  public function error($msg){return $this->_r($msg, 0);}

  private function _r($msg, $level) {
   if($level > $this->_level_ceil) return true;

   if(! $this->_fd) return false;

   return fwrite($this->_fd, '['.date('r')."] [level $level] $msg\n");
  }
 }

?>
