<?php
 //use t0t1\mysfw\frame as frame; // XXX useless

// require_once 'vfsStream/vfsStream.php';
  require_once 'frame/contract/popper.php';
  require_once 'frame/contract/dna.php';
  require_once 'frame/exception/dna.php';
  require_once 'frame/popper.php';    

 class popper_Test extends PHPUnit_Framework_TestCase {

  public function test_no_direct_intanciation(){
   $rc = new ReflectionClass('\t0t1\mysfw\frame\popper');
   $rm = $rc->getConstructor();
   $this->assertEquals($rm->getName(), '__construct');
   $this->assertFalse($rm->isPublic());
  }

  public function setUp(){
   $this->x = t0t1\mysfw\frame\popper::itself(__DIR__.DIRECTORY_SEPARATOR."../fake_project_root/www", '/t0t1/mysfw/');
  }

  public function test_singletonisation(){
   $this->assertEquals(get_class($this->x), 't0t1\mysfw\frame\popper');

   $popper2 = t0t1\mysfw\frame\popper::itself(__DIR__, 'some random path');
   $this->assertEquals(get_class($popper2), 't0t1\mysfw\frame\popper');

   $this->assertSame($this->x, $popper2);
  }

  public function test_pop_returns_correct_object() {
   $v = $this->x->pop('view');
   $this->assertTrue(in_array('t0t1\mysfw\frame\contract\view', class_implements($v)));
   $v = $this->x->pop('redis_data_storage');
   $this->assertTrue(in_array('t0t1\mysfw\frame\contract\data_storage', class_implements($v)));
  }

  /**
   * @dataProvider home_values
  **/
  public function test_set_and_get_home($h) {
   $this->x->set_home($h);
   $this->assertEquals($this->x->get_home(), $h);
  }

  public function home_values() {
   return array(
     array('gni'),
     array('gno'),
     array('gna')
     );
  }

  public function test_register() {
   $a = (object)array('hhhhh' => 'ffgfg', 'gbghbghbg' => 0);
   $this->x->register('a', $a);
   $this->assertSame($a, $this->x->indicate('a'));
  }

  /** XXX must use vfsStream **/
  public function test_external_swallowing(){
   $this->assertEquals(get_class($this->x->pop('fake_extension')), 't0t1\mysfw\module\fake_extension');
  }

  /**
   * @expectedException t0t1\mysfw\frame\exception\dna
   */
  public function test_indicate_exceptions() {
   $this->x->indicate('non_existent_register_entry');
  }

 }

?>
