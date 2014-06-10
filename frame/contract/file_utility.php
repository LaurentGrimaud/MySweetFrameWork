<?php
 namespace t0t1\mysfw\frame\contract;

 interface file_utility {
  public function full_path($report_dir, $root, $file);
  public function project_full_path($container_dir, $file);
 }
