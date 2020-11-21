<table id="forms2db-data-table">
    <tbody>
        <tr>
            <th id="forms2db-data-table-select-label"></th>
            <th id="forms2db-data-table-actions-label"></th>
            <?php foreach( $this->current_form_data['structure'] as $label_info ) {
                $base_link_query_args['order-by'] = $label_info['name']; 
                ?>
                <th class="forms2db-data-table-label"><a href="<?php echo $this->form_data_table_link( array ('order-by' => $label_info['name'] ) ); ?>"><?php echo $label_info['label'] ?></a></th>
            <?php } ?>
        </tr>
        <?php foreach( $this->current_form_data['data'] as $data  )  { ?>
            <tr>
                <td class="forms2db-data-table-select"><input type="checkbox" name="" id=""></td>
                <td>
                    <span class="forms2db-data-table-edit-item"><a href="<?php echo $this->form_data_table_link( array ('action' => 'edit', 'item' => $data['id']) ) ?>"><?php _e('Edit') ?></a></span>
                    <span class="forms2db-data-table-delete-item"><a href="<?php echo $this->form_data_table_link( array ('action' => 'delete', 'item' => $data['id']) ) ?>"><?php _e('Delete') ?></a></span>
                </td>
                <?php foreach( $data['data'] as $key => $value  )  { ?> 
                    <td><?php echo $value ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
<script>
    jQuery('.forms2db-data-table-delete-item').click(function() {
        return confirm('<?php _e('Are you sure') ?>');
    });
</script>