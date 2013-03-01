<table<?php echo HTML::attributes($table_attributes); ?>>
	<thead>
		<tr>
<?php foreach ($columns as $col): ?>
			<th<?php echo HTML::attributes($col->label_attributes ?: array()); ?>><?php echo $col->label; ?></th>
<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
<?php foreach ($rows as $row): ?>
		<tr<?php echo HTML::attributes(call_user_func($row_attributes, $row) ?: array()); ?>>
<?php foreach ($columns as $col): ?>
			<td<?php echo HTML::attributes(call_user_func($col->cell_attributes, $row)); ?>><?php echo call_user_func($col->value, $row); ?></td>
<?php endforeach; ?>
		</tr>
<?php endforeach; if ( ! count($rows) and $empty_message): ?>
		<tr class="norecords">
			<td colspan="<?php echo count($columns); ?>"><?php echo $empty_message->get(null, 'No records'); ?></td>
		</tr>
<?php endif; ?>
	</tbody>
</table>
<?php echo $pagination ?: ''; ?>