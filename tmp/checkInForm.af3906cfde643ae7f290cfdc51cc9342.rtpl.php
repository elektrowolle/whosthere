<?php if(!class_exists('raintpl')){exit;}?><h2><?php echo $_check_in;?></h2>
	<form id="announceForm" action="announce.php" method="post">
		<fieldset>
			<input name="name" type="text" value="<?php echo $user_name;?>" required autofocus>
			<input name="announce" type="submit" value="I'd love to come">
		</fieldset>
	</form>