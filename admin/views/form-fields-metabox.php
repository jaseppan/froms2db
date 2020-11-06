<div id="forms2db-fields-container">
    <h1><?php _e('Fields', 'forms2db') ?></h1>
    <div id="forms2db-fields">
        <?php $this->field_row($fields); ?>
    </div>
    <div class="forms2db-fields-row">
        <div class="forms2db-fields-col col-12 text-right">
            <div class="inner-col forms2db-add-row">
                <span class="button"><?php _e('Add field') ?></span>
            </div>
        </div>
    </div>
    <div class="forms2db-fields-row">
        <div class="forms2db-fields-col col-6">
            <div class="inner-col">
                <label for="submit-text"><?php _e('Text in submit button') ?></label>
                <input type="text" name="submit-text" id="submit-text" value="<?php echo $submit_text; ?>">
            </div>
        </div>
    </div>
</div>
