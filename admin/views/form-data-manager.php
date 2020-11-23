<?php $this->form_selector() ?>
<div class="tablenav top">
    <div class="alignleft actions bulkactions">
	    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top">
        <option value="-1">Bulk actions</option>
            <option value="edit" class="hide-if-no-js">Edit</option>
            <option value="trash">Move to Trash</option>
        </select>
        <input type="submit" id="doaction" class="button action" value="Apply">
	</div>
    <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo count($this->current_form_data['data']) . ' ' . ('items'); ?></span>
        <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
        <span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label>
        <input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text"> of <span class="total-pages">1</span></span></span>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span>
    </div>
    <br class="clear">
</div>
<?php $this->data_content() ?>