<form action="" method="post">
    <div id="forms2db-fields">
        <div class="forms2db-fields-row">
            <div class="forms2db-fields-col col-2">
                <div class="inner-col">
                    <label for="type" class="block"><?php _e('Type') ?></label>
                    <select name="type[]" id="type">
                        <optgroup label="Input">
                            <option value="text">text</option>
                            <option value="number">number</option>
                            <option value="email">email</option>
                            <option value="hidden">hidden</option>
                        <optgroup label="Choices">
                            <option value="select">select</option>
                            <option value="checkbox">checkbox</option>
                            <option value="radio">radio</option>
                        <optgroup label="Other">   
                            <option value="textarea">textarea</option>
                            <option value="file">file</option>
                    </select>
                    <div id="options">
                        <label for="type" class="block"><?php _e('Options') ?></label>
                        <textarea name="options[]" id="" cols="30" rows="10"></textarea>
                    </div>
                </div>
   
            </div>
            <div class="forms2db-fields-col col-10">
                <div class="forms2db-fields-row">
                    <div class="forms2db-fields-col col-4">
                        <div class="inner-col">
                            <label for="name" class="block"><?php _e('Name') ?></label>
                            <input type="text" name="name[]" value="">
                        </div>
                    </div>
                    <div class="forms2db-fields-col col-4">
                        <div class="inner-col">
                            <label for="type" class="block"><?php _e('Value') ?></label>
                            <input type="text" name="value[]" value="">
                        </div>
                    </div>
                    <div class="forms2db-fields-col col-4">
                        <div class="inner-col">
                            <label for="type" class="block"><?php _e('Class') ?></label>
                            <input type="text" name="class[]" value="">
                        </div>
                    </div>
                    <div class="forms2db-fields-col col-4">
                        <div class="inner-col">
                            <label for="type" class="block"><?php _e('Attributes') ?></label>
                            <input type="text" name="attributes[]" value="">
                        </div>
                    </div>
                    <div class="forms2db-fields-col col-4">
                        <div class="inner-col">
                        </div>
                    </div>
                    <div class="forms2db-fields-col col-4">
                        <div class="inner-col">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="forms2db-fields-row">
        <div class="forms2db-fields-col col-12 text-right">
            <div class="inner-col">
                <span class="forms2db-add-row button"><?php _e('Add field') ?></span>
            </div>
        </div>
    </div>
</form>