<?php echo render('_header'); ?>
<h1><?php echo $name; ?></h1>
<div class="page">
    <table class="display">
        <thead>
            <?php foreach( $fields as $field ): ?>
                <td><?php echo $field['label'] ?></td>
            <?php endforeach; ?>
        </thead>
        <?php foreach( $data as $row ): ?>
            <tr>
                <?php foreach( $fields as $i=>$field ): ?>
                    <?php if( $i == 0 ): ?>
                    <td><a href="<?php url($urls['show'], array( $row->$urls['pk'] )) ?>"><?php echo $row->$field['name']; ?></a></td>
                    <?php else:  ?>
                    <td><?php echo $row->$field['name']; ?></td>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php echo render('_footer'); ?>