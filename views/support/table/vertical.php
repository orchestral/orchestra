<?php $table_attributes = Orchestra\HTML::decorate($table_attributes, array('class' => 'table table-bordered table-striped')); ?>
<table<?php echo HTML::attributes($table_attributes); ?>>
	<tbody>
<?php foreach ($columns as $col): ?>
		<tr>
			<th<?php echo HTML::attributes($col->label_attributes ?: array()); ?>><?php echo $col->label; ?></th>
<?php foreach ($rows as $row): ?>
			<td<?php echo HTML::attributes(call_user_func($col->cell_attributes, $row)); ?>><?php 

				$col_value = call_user_func($col->value, $row);
				echo ( !! $col->escape ? e($col_value) : $col_value); ?></td>
<?php endforeach; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo $pagination ?: ''; ?>