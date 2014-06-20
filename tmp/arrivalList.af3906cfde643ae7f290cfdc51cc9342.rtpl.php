<?php if(!class_exists('raintpl')){exit;}?><table class="arrivalsList">
	<thead>
		<tr>
			<td class="arrival descriptor"><?php echo $_arrivals_time;?></td>
			<td class="name descriptor"><?php echo $_arrivals_name;?></td>
			<td class="status descriptor"><?php echo $_arrivals_status;?></td>
		</tr>
	</thead>
	<tbody>
	<?php $counter1=-1; if( isset($$GLOBALS["tplMessage"]) && is_array($$GLOBALS["tplMessage"]) && sizeof($$GLOBALS["tplMessage"]) ) foreach( $$GLOBALS["tplMessage"] as $key1 => $value1 ){ $counter1++; ?>
		<tr>
			<td class="arrival"><?php echo ( stDate( $value1["time"], 'H:i' ) );?></td>
			<td class="name"><?php echo $value1["name"];?></td>
			<td class="status"><?php echo $value1["status"];?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>