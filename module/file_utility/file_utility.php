<?php
 namespace t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 $this->_learn('frame\contract\file_utility');

 class file_utility extends frame\dna implements frame\contract\file_utility, frame\contract\dna {

  public function is_path_absolute($path) {
   return @$path[0] === DIRECTORY_SEPARATOR;
  }

  public function full_path($report_dir, $root, $file) {
   return ($this->is_path_absolute($report_dir) ? "" : $root).$report_dir.$file;
  }

  public function project_full_path($container_dir, $file){
   return $this->full_path($container_dir, $this->inform('root'), $file);
  }

 }
