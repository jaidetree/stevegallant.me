<?php echo render('_header') ?>
<h1>User Edit <?php echo $user->username ?></h1>
<form action="<?php url('admin\users.edit',array($user->id)); ?>" method="post">
    <?php echo render('admin/users/_form', array('user' => $user)) ?>
</form>
<?php echo render('_footer') ?>