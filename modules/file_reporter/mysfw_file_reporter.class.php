<?php

 class mysfw_file_reporter extends mysfw_core {
  private $_fd;
  private $_level_ceil = 3;


  public function __construct() {
   $this->_fd = fopen('file_report.txt', 'a'); // XXX temp
  }

  public function report_debug($msg){return $this->_report($msg, 3);}
  public function report_info($msg){return $this->_report($msg, 2);}
  public function report_warning($msg){return $this->_report($msg, 1);}
  public function report_error($msg){return $this->_report($msg, 0);}

  private function _report($msg, $level) {
   if($level > $this->_level_ceil) return true;

   return fwrite($this->_fd, '['.date('r')."] [level $level] $msg\n");
  }
 }

?>
