<table<?php echo HTML::attributes($table_markup); ?>>
	<tbody>
<?php foreach ($columns as $col): ?>
		<tr>
			<th<?php echo HTML::attributes($col->label_markup ?: array()); ?>><?php echo $col->label; ?></th>
<?php foreach ($rows as $row): ?>
			<td<?php echo HTML::attributes(call_user_func($col->cell_markup, $row)); ?>><?php echo call_user_func($col->value, $row); ?></td>
<?php endforeach; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo $pagination ?: ''; ?>