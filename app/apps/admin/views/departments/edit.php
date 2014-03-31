<?php echo render('_header') ?>
<h1>Edit Department <?php echo $department->title ?></h1>
<form action="<?php url('admin\Departments.edit',array($department->id)); ?>" method="post">
    <?php echo render('admin/departments/_form', array('department' => $department)) ?>
</form>
<?php echo render('_footer') ?>