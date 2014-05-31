<?php
 use t0t1\mysfw\module;
 use t0t1\mysfw\frame;

 // XXX temp

 class test_init {
  protected function _learn() {
  }

  public function __construct() {
   // XXX liste des dépendances
   require_once 'frame/contract/dna.php';
   require_once 'frame/contract/popper.php';
   require_once 'frame/contract/configurator.php';
   require_once 'frame/contract/reporter.php';

   require_once 'frame/exception/dna.php';
   require_once 'frame/dna.php';
   require_once 'frame/popper.php';

   require_once 'module/configurator/configurator.php';

   require_once 'module/file_reporter/file_reporter.php';
  }
 }

$xxx = new test_init();


 class file_reporterTest extends PHPUnit_Framework_TestCase {
  protected $_x;

  public function init_configurator($configurator) {
   $map = [
    ['report_dir', null, 'relative dir/'],
    ['root', null, '/root/'],
    ['report_file_name', null, 'report.log']
    ];
   
   $configurator
    ->expects($this->any())
    ->method('inform')
    ->will($this->returnValueMap($map));

  }

  public function setUp() {
   $this->_x = new module\file_reporter;
   $mocked_popper = $this->getMock('t0t1\mysfw\frame\contract\popper');
   $mocked_configurator = $this->getMock('t0t1\mysfw\frame\contract\configurator');
   $this->init_configurator($mocked_configurator);
   $this->_x->set_popper($mocked_popper);
   $this->_x->set_configurator($mocked_configurator);
   //$this->_x->get_ready();
  }

  public function reports_data() {
   return [
    ['my dir/', '/my root/', 'my file.truc', '/my root/my dir/my file.truc'],
    ['/my absolute dir/', '/my root/', 'my beautiful file.truc', '/my absolute dir/my beautiful file.truc'],
    ['../my relative dir/', '/my root/', 'my incredible file.truc', '/my root/../my relative dir/my incredible file.truc'],
    ];
  }

  /** @dataProvider reports_data **/
  public function test_report_file($dir, $root, $file, $result) {
   $this->assertEquals($result, $this->_x->build_file($dir, $root, $file));
  }
}
