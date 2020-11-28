<form action="<?php echo admin_url('edit.php'); ?>" method="get">
    <input type="hidden" name="post_type" value="forms2db-forms"/><br />
    <input type="hidden" name="page" value="forms2db-save-data"/>
    <label for="forms2db-selector"><?php _e('Form: ', 'forms2db') ?></label>
    <select name="form-id" id="forms2db-selector">
        <option>-- <?php _e('Select form', 'forms2db') ?> --</option>
        <?php foreach($this->available_forms as $form) { ?>
        <option value="<?php echo $form->ID ?>" <?php echo ($_GET['form-id'] == $form->ID) ? 'selected' : ''  ?>><?php echo $form->post_title ?></option>     
        <?php } ?>
    </select>
    <input type="submit" name="submit-forms2db-selector" value="<?php _e('Submit', 'forms2db') ?>" id="submit-forms2db-selector" class="button">
</form>