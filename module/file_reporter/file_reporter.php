<?php
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\reporter');

 class file_reporter extends frame\dna implements frame\contract\reporter, frame\contract\dna {
  private $_fd;
  private $_file;
  private $_level_ceil = 3;

  protected $_defaults = [
   'root' => '',
   'reporter:dir' => '../reports/',
   'reporter:filename' => 'default.report'
    ];

  protected function _is_path_absolute($path) {
   return @$path[0] === DIRECTORY_SEPARATOR;
  }

  public function build_file($report_dir, $root, $file) {
   return ($this->_is_path_absolute($report_dir) ? "" : $root).$report_dir.$file;
  }

  public function get_file(){return $this->_file;}
  public function set_file($file){$this->_file = $file;}

  protected function _get_ready() {
   $this->_file = $this->build_file($this->inform('reporter:dir'), $this->inform('root'), $this->inform('reporter:filename'));
   if(! $this->_fd = \fopen($this->_file, 'a')) throw $this->except("Failed to open report file `$report`");
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
