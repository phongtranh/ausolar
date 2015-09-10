<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */
?>
<form method="post" action="admin.php?page=adrotate-ads&view=import" enctype="multipart/form-data">
<?php wp_nonce_field('adrotate_import','adrotate_nonce'); ?>
<input type="hidden" name="MAX_FILE_SIZE" value="4096000" />


<h3><?php _e('Select a file to import', 'adrotate'); ?></h3>
<label for="adrotate_file"><input type="file" name="adrotate_file" id="file" size="100" /></label>
<br /><em><strong>Accepted files:</strong> CSV and XML.<br />Make sure the file is smaller than 4096Kb (up to approximately 1000 ads in 1 file).</em>

<p class="submit">
	<label for="adrotate_import"><input tabindex="2" type="submit" name="adrotate_import" class="button-primary" value="Import" /> <em>Click only once!</em></label>
</p>

</form>