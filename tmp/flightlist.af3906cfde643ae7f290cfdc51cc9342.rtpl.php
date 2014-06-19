<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html>
<head>
	<title><?php echo $title;?></title>
	<link href="tpl/style.css" type="text/css" rel="stylesheet" >
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.2/jquery.mobile.min.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.2/jquery.mobile.min.js"></script>
</head>

<body>
<h1><?php echo $title;?></h1>
<p><?php echo $_welcome;?></p>
<h2><?php echo $_arrivals;?></h2>
<table>
<tr>
	<td class="arrival descriptor"><?php echo $_arrivals_time;?></td>
	<td class="name descriptor"><?php echo $_arrivals_name;?></td>
	<td class="status descriptor"><?php echo $_arrivals_status;?></td>
</tr>
<?php $counter1=-1; if( isset($today_arrivals) && is_array($today_arrivals) && sizeof($today_arrivals) ) foreach( $today_arrivals as $key1 => $value1 ){ $counter1++; ?>
<tr>
	<td class="arrival"><?php echo ( stDate( $value1["time"], 'H:i' ) );?></td>
	<td class="name"><?php echo $value1["name"];?></td>
	<td class="status"><?php echo $value1["status"];?></td>
</tr>
<?php } ?>
</table>

<h2><?php echo $_former_arrivals;?></h2>
<table>
<tr>
	<td class="arrival descriptor"><?php echo $_arrivals_time;?></td>
	<td class="name descriptor"><?php echo $_arrivals_name;?></td>
	<td class="status descriptor"><?php echo $_arrivals_status;?></td>
</tr>

<?php $counter1=-1; if( isset($former_arrivals) && is_array($former_arrivals) && sizeof($former_arrivals) ) foreach( $former_arrivals as $key1 => $value1 ){ $counter1++; ?>
<tr>
	<td class="arrival"><?php echo ( stDate( $value1["time"], 'H:i' ) );?></td>
	<td class="name"><?php echo $value1["name"];?></td>
	<td class="status"><?php echo $value1["status"];?></td>
</tr>
<?php } ?>
</table>

<?php if( !isCheckedIn() ){ ?>
<div id="formContainer">
<h2><?php echo $_check_in;?></h2>
	<form id="announceForm" action="socket.php?mode=announce" method="post">
		<fieldset>
			<input name="name" type="text" value="<?php echo $user_name;?>" required autofocus>
			<input name="announce" type="submit" value="I'd love to come">
		</fieldset>
	</form>
</div>
<?php } ?>

<?php if( isCheckedIn() ){ ?>
<div id="formContainer">
<h2><?php echo $_check_in;?></h2>
	<form id="announceForm" action="socket.php?mode=announce" method="post">
		<fieldset>
			<input name="name" type="text" value="<?php echo $user_name;?>" required autofocus>
			<input name="announce" type="submit" value="I'd love to come">
		</fieldset>
	</form>
</div>
<?php } ?>

<script type="text/javascript">

	var user_position = "<?php echo $default_position;?>";

<?php if( locationIsNeeded() ){ ?>
	if(navigator.geolocation){
		navigator.geolocation.getCurrentPosition(successCallback,errorCallback,{timeout:10000});

		function successCallback (position) {
			user_position = 
				position.coords.latitude 
				+ "," 
				+ position.coords.longitude ;
		}

		function errorCallback () {
		}
	}
<?php } ?>
	var posting;
	function update () {
		// body...
	}

	var $nameFied = $( "#announceForm" ).find( "input[name='name']" );

	$nameFied.focusin(function( event ) {
		if ($nameFied.val() == "<?php echo $_name_request;?>") {
			$nameFied.val("");
		}

	});

	$nameFied.focusout(function( event ) {
		if ($nameFied.val() == "") {
			$nameFied.val("<?php echo $_name_request;?>");
		}
	});

	$( "#announceForm" ).submit(function( event ) {
 
	  // Stop form from submitting normally
	  event.preventDefault();
	 
	  // Get some values from elements on the page:
	  var $form = $( this ),
	    name = $form.find( "input[name='name']" ).val(),
	    url  = $form.attr( "action" );
	 
	  // Send the data using post
	  posting = $.post( url, { name: name, location: user_position } );
	 
	  // Put the results in a div
	  posting.done(function( data ) {
	    $( "#formContainer" ).empty().append( 'we are waiting for you!' );
	  });
	});	

	setInterval(update, 5000);
</script>
</body>
</html>
