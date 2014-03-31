<?php echo render('_header') ?>
<h1>Users Index</h1>
<div class="add-new"><a href="<?php url('admin\Users.add') ?>">Add New User</a></div>
<?php echo render('_status') ?>

<div class="page">
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="assetlist">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($users as $user): ?>
            <tr>
                <td><?php echo $user->username ?></td>
                <td><?php echo $user->email ?></td>
                <td><?php echo $user->type_name() ?></td>
                <td><?php echo $user->status_name() ?></td>
                <td class="actions">
                    <a class="edit" href="<?php url('admin\Users.edit', array($user->id)) ?>">Edit</a>
                    <a class="reset-password" href="<?php url('admin\Users.reset', array($user->id)) ?>">Reset</a>
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
                null, null,
                null, null, { "bSortable": false }
            ]
    } );    
    
</script>

<?php echo render('_footer') ?>