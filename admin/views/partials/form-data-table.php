<table id="forms2db-data-table" class="wp-list-table widefat fixed striped table-view-list pages">
    <thead>
        <tr>
            <td id="cb" class="manage-column column-cb check-column">
                <label class="screen-reader-text" for="cb-select-all-1"><?php _e('Select All') ?></label>
                <input id="cb-select-all-1" type="checkbox">
            </td>
            <?php foreach( $this->current_form_data['structure'] as $label_info ) {
                $base_link_query_args['order-by'] = $label_info['name']; 
                ?>
                <th class="forms2db-data-table-label"><a href="<?php echo $this->form_data_table_link( array ('order-by' => $label_info['name'] ) ); ?>"><?php echo $label_info['label'] ?></a></th>
            <?php } ?>
            <th id="forms2db-data-table-actions-label" class="forms2db-data-table-actions"></th>
        </tr>
    </thead>    
    <tbody id="the-list">
        <?php foreach( $this->current_form_data['data'] as $data  )  { ?>
            <tr>
                <th scope="row" class="check-column forms2db-data-table-select"><input type="checkbox" name="form-data[]" id="cb-select-<?php echo $data['id'] ?>" value="<?php echo $data['id'] ?>"></th>
                <?php foreach( $data['data'] as $key => $value  )  { ?> 
                    <td><?php echo $value ?></td>
                <?php } ?>
                <td class="forms2db-data-table-actions">
                    <span class="forms2db-data-table-edit-item"><a href="<?php echo $this->form_data_table_link( array ('action' => 'edit', 'item' => $data['id']) ) ?>"><?php _e('Edit') ?></a></span>
                    <span class="forms2db-data-table-delete-item"><a href="<?php echo $this->form_data_table_link( array ('action' => 'delete', 'item' => $data['id']) ) ?>"><?php _e('Delete') ?></a></span>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<script>
    jQuery('.forms2db-data-table-delete-item').click(function() {
        return confirm('<?php _e('Are you sure?') ?>');
    });
</script>