<?php echo render('_header.php'); ?>
<div id="page-dashboard" class="admin">
    <h1>Welcome</h1>
    <p>Welcome to the administrative dashboard.</p>
    <section class="logo-upload">
        <form method="post" enctype="multipart/form-data" action="<?php url('jframe\admin\Dashboard.update_kendall_logo'); ?>">
            <div class="current">
                <span>Current Logo:</span>
                <img src="<?php static_url(); ?>uploads/logos/kendall_college_logo.png" alt="Kendall Logo" />
            </div>
            <label for="id_kendall_logo">Official Kendall Logo</label>
            <input type="file" name="kendall_logo" id="id_kendall_logo" />
            <br />
            <input type="submit" value="Upload" />
        </form>
    </section>
</div>
<?php echo render('_footer.php'); ?>