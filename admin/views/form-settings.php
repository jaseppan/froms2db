<p>
    <span><?php _e('Front end user can edit form content') ?></span><br/>
    <input type="radio" name="modifyable" id="modifyable-true" value=1 <?php echo ($settings['modifyable'] == true ) ? 'checked' : '' ?> ><label for="modifyable-true"><?php _e('Yes') ?></label>
    <input type="radio" name="modifyable" id="modifyable-false" value=0 <?php echo ($settings['modifyable'] == false ) ? 'checked' : '' ?> ><label for="modifyable-false"><?php _e('No') ?></label>
</p>
<p>
    <span><?php _e('Confirmation required') ?></span><br/>
    <input type="radio" name="confirmation-required" id="confirmation-required-true" value=1 <?php echo ($settings['confirmation-required'] == true ) ? 'checked' : '' ?> ><label for="confirmation-required-true"><?php _e('Yes') ?></label>
    <input type="radio" name="confirmation-required" id="confirmation-required-false" value=0 <?php echo ($settings['confirmation-required'] == false ) ? 'checked' : '' ?> ><label for="confirmation-required-false"><?php _e('No') ?></label>
</p>