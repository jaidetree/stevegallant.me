<?php echo render('_header') ?>
<h1>Add Video</h1>
<form action="<?php url('admin\videos.import'); ?>" method="post">
    <?php echo render('videos/_form', array('video' => $video, 'departments' => $departments )) ?>
</form>
<?php echo render('_footer') ?>