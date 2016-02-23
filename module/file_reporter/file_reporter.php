<?php
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\reporter');

 class file_reporter extends frame\dna implements frame\contract\reporter, frame\contract\dna {
  private $_fd;
  private $_file;
  private $_eid;   // Execution Identifier

  protected $_defaults = [
   'reporter:directory'  => '../reports/',
   'reporter:filename'   => 'default.report',
   'reporter:ceil_level' => 3
    ];

  public function get_file(){return $this->_file;}
  public function set_file($file){$this->_file = $file;}

  protected function _get_ready() {
   $this->_file = $this->pop('file_utility')->project_full_path($this->inform('reporter:directory'), $this->inform('reporter:filename'));
   if(! $this->_fd = @\fopen($this->_file, 'a')) throw $this->except("Failed to open report file `{$this->_file}`");
   $this->_eid = $this->build_eid();
  }

  public function get_eid() {return $this->_eid;}

  public function build_eid() {
   return uniqid().'-'.getmypid();
  }

  /** Overrides of the generic behaviour implemented in mysfw_core **/
  public function report_debug($msg){return $this->_r($msg, 3);}
  public function report_info($msg){return $this->_r($msg, 2);}
  public function report_warning($msg){return $this->_r($msg, 1);}
  public function report_error($msg){return $this->_r($msg, 0);}

  private function _r($msg, $level) {
   if($level > $this->inform('reporter:ceil_level')) return true;

   if(! $this->_fd) return false;

   $msg = preg_replace('/[\s\n]+/', ' ', $msg); // one line log
   return \fwrite($this->_fd, '['.date('r')."] [".$this->get_eid()."] [level $level] $msg\n");
  }
 }
