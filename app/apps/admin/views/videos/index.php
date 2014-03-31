<?php echo render('_header') ?>
<div id="page-crud-index">
    <h1>Videos Index</h1>
    <div class="add-new"><a href="<?php url('admin\videos.create') ?>">Add New Video</a></div>
    <?php echo render('_status') ?>

    <div class="page">
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="assetlist">
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Content</th>
                    <th>Departments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($videos as $video): ?>
                <tr>
                    <td><a href="<?php echo $video->url ?>" target="_blank"><?php echo $video->url ?></a></td>
                    <td><?php echo $video->title ?></td>
                    <td>
                        <a href="<?php echo $video->image ?>" target="_blank"><?php echo $video->image ?></a>
                        |
                        <a class="image" href="<?php url('admin\videos.image', array($video->id)) ?>">Reload</a>
                    </td>
                    <td><?php echo substr($video->content, 0, 20) . "..." ?></td>
                    <td>
                        <ul>
                            <?php foreach($video->departments as $dep): ?> 
                                <li><?php echo $dep->title; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td class="actions">
                        <a class="edit" href="<?php url('admin\videos.edit', array($video->id)) ?>">Edit</a>
                        <a class="delete" href="<?php url('admin\videos.delete', array($video->id)) ?>">&times;</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript" charset="utf-8">

        /* Define two custom functions (asc and desc) for string sorting */
        jQuery.fn.dataTableExt.oSort['string-case-asc']  = function(x,y) {
            return ((x < y) ? -1 : ((x > y) ?  1 : 0));
        };
        
        jQuery.fn.dataTableExt.oSort['string-case-desc'] = function(x,y) {
            return ((x < y) ?  1 : ((x > y) ? -1 : 0));
        };

        /* Build the DataTable with third column using our custom sort functions */
        jQuery('#assetlist').dataTable( {
            "sPaginationType": "full_numbers",
            "aaSorting": [ [0,'asc'] ],
            "aoColumnDefs": [
                    { "sType": 'string-case', "aTargets": [ 2 ] }
                ],
            "aoColumns": [
                    null, 
                    null,
                    null, 
                    null, 
                    null,
                    { "bSortable": false }
                ]
        } );    
        
    </script>
</div>

<?php echo render('_footer') ?>