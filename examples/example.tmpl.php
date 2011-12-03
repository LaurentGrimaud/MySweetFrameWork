<?php
 if(false === $c = $this->get("create")) {
  $create = 'No!';
 }else{
  $create = "Yes, with id $c !";
 }
?>

<h3><?php echo $this->get('title'); ?></h3>

<b>New element created ? <?php echo $create ?></b></br />

<?php if(! $this->get('users')){ ?>
<b>No user to list, strange...</b>
<?php }else{foreach($this->get('users') as $u) { ?>
 <b>user #<?php echo $u->user_id ?> / active: <?php echo $u->user_active ?></b><br />
<?php }} ?>
