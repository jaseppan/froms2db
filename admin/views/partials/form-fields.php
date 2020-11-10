<div class="forms2db-field-container <?php echo (isset($active)) ? $active : ''; ?>">
    <div class="forms2db-field-header">
        <input type="text" name="label[]" value="<?php echo $field['label'] ?>" placeholder="<?php echo _e('label'); ?>">
        <div class="forms2db-field-actions">
            <span class="forms2db-field-delete"><span class="circle">x</span></span>
            <span class="forms2db-field-toggle"><span>></span></span>
        </div>
    </div>
    <div class="forms2db-fields-row">
        <div class="forms2db-fields-col col-2">
            <div class="inner-col">
                <label for="type" class="block"><?php _e('field-type') ?></label>
                <select name="field-type[]" class="type">
                    <option></option>
                    <optgroup label="Input">
                        <option value="text" <?php echo ($field['field-type'] == 'text') ? 'selected' : '' ?>>text</option>
                        <option value="number" <?php echo ($field['field-type'] == 'number') ? 'selected' : '' ?>>number</option>
                        <option value="email" <?php echo ($field['field-type'] == 'email') ? 'selected' : '' ?>>email</option>
                        <option value="hidden" <?php echo ($field['field-type'] == 'hidden') ? 'selected' : '' ?>>hidden</option>
                    <optgroup label="Choices">
                        <option value="select" <?php echo ($field['field-type'] == 'select') ? 'selected' : '' ?>>select</option>
                        <option value="checkbox" <?php echo ($field['field-type'] == 'checkbox') ? 'selected' : '' ?>>checkbox</option>
                        <option value="radio" <?php echo ($field['field-type'] == 'radio') ? 'selected' : '' ?>>radio</option>
                    <optgroup label="Other">   
                        <option value="textarea" <?php echo ($field['field-type'] == 'textarea') ? 'selected' : '' ?>>textarea</option>
                        <option value="file" <?php echo ($field['field-type'] == 'file') ? 'selected' : '' ?>>file</option>
                </select>
            </div>
        </div>
        <div class="forms2db-fields-col col-10">
            <div class="forms2db-fields-row">
                <div class="forms2db-fields-col col-4">
                    <div class="inner-col">
                        <label for="name" class="block"><?php _e('Name') ?></label>
                        <input type="text" name="name[]" value="<?php echo (isset($field['name'])) ? $field['name'] : '' ?>">
                    </div>
                </div>
                <div class="forms2db-fields-col col-4">
                    <div class="inner-col">
                        <label for="type" class="block"><?php _e('Value') ?></label>
                        <input type="text" name="value[]" value="<?php echo (isset($field['value'])) ? $field['value'] : '' ?>">
                    </div>
                </div>
                <div class="forms2db-fields-col col-4">
                    <div class="inner-col">
                        <label for="type" class="block"><?php _e('Attributes') ?></label>
                        <input type="text" name="attributes[]" value="<?php echo (isset($field['attributes'])) ? $field['attributes'] : '' ?>">
                    </div>
                </div>
                <div class="forms2db-fields-col col-4">
                    <div class="inner-col">
                        <label for="type" class="block"><?php _e('Max length') ?></label>
                        <input type="text" name="max_length[]" value="<?php echo (isset($field['max_length'])) ? $field['max_length'] : '' ?>">
                    </div>
                </div>
                <div class="forms2db-fields-col col-4">
                    <div class="inner-col">
                        <label for="type" class="block"><?php _e('Required') ?></label>
                        <input type="checkbox" name="required[]" value="<?php echo (isset($field['required'])) ? $field['required'] : '' ?>">
                    </div>
                </div>
                <div class="forms2db-fields-col col-4">
                    <div class="inner-col">
                        <label for="type" class="block"><?php _e('Field Classes') ?></label>
                        <input type="text" name="field-classes[]" value="<?php echo (isset($field['field-classes'])) ? $field['field-classes'] : '' ?>">
                    </div>
                </div>
                <div class="forms2db-fields-col col-4">
                    <div class="inner-col">
                        <label for="type" class="block"><?php _e('Container Classes') ?></label>
                        <input type="text" name="container-classes[]" value="<?php echo (isset($field['container-classes'])) ? $field['container-classes'] : '' ?>">
                    </div>
                </div>
                <div class="forms2db-fields-col col-12">
                    <div class="inner-col">
                        <div class="options <?php echo (!in_array( $field['field-type'], $checkboxes)) ? 'hidden' : '' ?>">
                            <label for="type" class="block"><?php _e('Options') ?></label>
                            <textarea name="options[]" id="" cols="30" rows="4"><?php echo (isset($field['options'])) ? $this->option_array_to_text($field['options']) : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>