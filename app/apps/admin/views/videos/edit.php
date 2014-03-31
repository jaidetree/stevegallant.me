<?php echo render('_header') ?>
<h1>Editing Video &ldquo;<?php echo $video->title ?>&rdquo;</h1>
<form action="<?php url('admin\videos.edit',array($video->id)); ?>" method="post">
    <?php echo render('admin/videos/_form', array('video' => $video, 'departments' => $departments)) ?>
</form>
<?php echo render('_footer') ?>