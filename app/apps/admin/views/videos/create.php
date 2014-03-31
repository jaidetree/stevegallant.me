<?php echo render('_header') ?>
<h1>Add Video</h1>
<form action="<?php url('admin\videos.import'); ?>" method="post">
    <ul>
        <li>
            <label for="vimeo_url">Paste Vimeo URL</label>
            <input type="text" id="vimeo_url" name="vimeo_url" />
        </li>
        <li>
            <input type="submit" name="submit" value="Load Video" />
        </li>
    </ul>
</form>
<?php echo render('_footer') ?>