<?php echo render('_header') ?>
<h1>Add Department</h1>
<form action="<?php url('admin\Departments.add'); ?>" method="post">
    <?php echo render('admin/departments/_form', array('department' => $department)) ?>
</form>
<?php echo render('_footer') ?>