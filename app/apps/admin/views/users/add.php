<?php echo render('_header') ?>
<h1>Add User</h1>
<form action="<?php url('admin\Users.add'); ?>" method="post">
    <?php echo render('admin/users/_form', array('user' => $user)) ?>
</form>
<?php echo render('_footer') ?>