<?php echo render('_status') ?>
<table class="edit">
    <tr>
        <td>Title</td>
        <td><input type="text" name="title" value="<?php echo $video->title ?>" /></td>
    </tr>
    <tr>
        <td>Slug</td>
        <td><input type="text" name="slug" value="<?php echo $video->slug ?>" /></td>
    </tr>
    <tr>
        <td>URL</td>
        <td>
            <input type="text" name="url" value="<?php echo $video->url ?>" />
            <?php if($video->url): ?>
            <a href="<?php echo $video->url ?>" target="_blank"><?php echo $video->url; ?></a>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>Image</td>
        <td>
            <input type="text" name="image" value="<?php echo $video->image ?>" />
            <?php if($video->image): ?>
            <a href="<?php echo $video->image ?>" target="_blank"><?php echo basename($video->image); ?></a>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>Content</td>
        <td><textarea name="content"><?php echo $video->content ?></textarea></td>
    </tr>
    <tr>
        <td>Department(s)</td>
        <td>
            <ul class="cat-options">
                <?php if( $video->id && $video->departments ): ?>
                    <?php $departments = $video->departments; ?>
                <?php endif; ?>
                <?php foreach($departments as $dep): ?>
                    <li>
                        <?php if( ! $video->id || count($video->departments) == 0  ): ?>
                        <input type="checkbox" name="departments[]" value="<?php echo $dep->id; ?>" />
                        <?php endif; ?>
                        <?php echo $dep->title; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" value="Import Video" /> <a href="<?php url('admin\videos.index'); ?>">Return</a></td>
    </tr>
</table>