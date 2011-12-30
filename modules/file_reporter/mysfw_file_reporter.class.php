<?php

 class mysfw_file_reporter extends mysfw_core implements mysfw_reporter {
  private $_fd;
  private $_level_ceil = 3;


  protected function _get_ready() {
   if(false === ($log = $this->inform('root').$this->inform('log_file_name'))){return false;}
   return ($this->_fd = fopen($log, 'a')) ? true : false;
  }

  public function report_debug($msg){return $this->_report($msg, 3);}
  public function report_info($msg){return $this->_report($msg, 2);}
  public function report_warning($msg){return $this->_report($msg, 1);}
  public function report_error($msg){return $this->_report($msg, 0);}

  private function _report($msg, $level) {
   if($level > $this->_level_ceil) return true;

   if(! $this->_fd) return false;

   return fwrite($this->_fd, '['.date('r')."] [level $level] $msg\n");
  }
 }

?>
