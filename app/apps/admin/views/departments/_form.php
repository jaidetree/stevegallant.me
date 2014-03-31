<?php
/**
 * The preceding underscore (_) in the file name indicates.
 * That this file is a partial template. A smaller template chunck
 * that belongs in something else.
 */
?>
<?php echo render('_status') ?>
<table class="edit">
    <tr>
        <td>Title</td>
        <td><input type="text" name="title" value="<?php echo $department->title ?>" /></td>
    </tr>
    <tr>
        <td>Slug</td>
        <td><input type="text" name="slug" value="<?php echo $department->slug ?>" /></td>
    </tr>
    <tr>
        <td>Content</td>
        <td><textarea name="content"><?php echo $department->content ?></textarea></td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit"><a href="<?php url('admin\Departments.index'); ?>">Return</a></td>
    </tr>
</table>
