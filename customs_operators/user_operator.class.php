<?php
 
 class user_operator extends mysfw_operator {

  public function __construct($user_id = null) {
   parent::__construct("users");
   parent::_identify('user_id', $user_id);
   if($user_id){
    parent::_set_identified();
   }
  }

  protected function _set_uid($uid){
   parent::_identify('user_id', $uid);
   parent::_set_identified();
   $this->set('user_id', $uid);
  }
 }

?>
