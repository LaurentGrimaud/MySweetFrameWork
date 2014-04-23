<?php
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\reporter');

 class file_reporter extends frame\dna implements frame\contract\reporter, frame\contract\dna {
  private $_fd;
  private $_level_ceil = 3;

  protected $_defaults = [
   'root' => '',
   'report_dir' => '../reports/',
   'report_file_name' => 'default.report'
    ];

  protected function _get_ready() {
   $report = ( $this->inform('report_dir')==$this->_defaults['report_dir'])?$this->inform('root').$this->inform('report_dir').$this->inform('report_file_name'):$this->inform('report_dir') . $this->inform('report_file_name');
   if(! $this->_fd = \fopen($report, 'a')) throw $this->except("Failed to open report file `$report`");
  }

  /** Overrides of the generic behaviour implemented in mysfw_core **/
  public function report_debug($msg){return $this->_r($msg, 3);}
  public function report_info($msg){return $this->_r($msg, 2);}
  public function report_warning($msg){return $this->_r($msg, 1);}
  public function report_error($msg){return $this->_r($msg, 0);}

  private function _r($msg, $level) {
   if($level > $this->_level_ceil) return true;

   if(! $this->_fd) return false;

   return \fwrite($this->_fd, '['.date('r')."] [level $level] $msg\n");
  }
 }
