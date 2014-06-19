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
<div class="block">
	<div class="arrival descriptor"><?php echo $_arrivals_time;?></div>
	<div class="name descriptor"><?php echo $_arrivals_name;?></div>
	<div class="status descriptor"><?php echo $_arrivals_status;?></div>
</div>	
<?php $counter1=-1; if( isset($today_arrivals) && is_array($today_arrivals) && sizeof($today_arrivals) ) foreach( $today_arrivals as $key1 => $value1 ){ $counter1++; ?>
<div class="block">
	<div class="arrival"><?php echo $value1["time"];?></div>
	<div class="name"><?php echo $value1["name"];?></div>
	<div class="status"><?php echo $value1["status"];?></div>
</div>
<?php } ?>	

<div id="formContainer">
<h2><?php echo $_check_in;?></h2>
	<form id="announceForm" action="socket.php?mode=announce" method="post">
		<fieldset>
			<legend>Who are you?</legend>
			<input name="name" type="text" required autofocus>
			<input name="announce" type="submit" value="I'd love to come">
		</fieldset>
	</form>
</div>

<h2><?php echo $_former_arrivals;?></h2>
<div class="block">
	<div class="arrival descriptor"><?php echo $_arrivals_time;?></div>
	<div class="name descriptor"><?php echo $_arrivals_name;?></div>
	<div class="status descriptor"><?php echo $_arrivals_status;?></div>
</div>

<?php $counter1=-1; if( isset($former_arrivals) && is_array($former_arrivals) && sizeof($former_arrivals) ) foreach( $former_arrivals as $key1 => $value1 ){ $counter1++; ?>
<div class="block">
	<div class="arrival"><?php echo $value1["time"];?></div>
	<div class="name"><?php echo $value1["name"];?></div>
	<div class="status"><?php echo $value1["status"];?></div>
</div>
<?php } ?>

<script type="text/javascript">
	function update () {
		// body...
	}

	$( "#announceForm" ).submit(function( event ) {
 
	  // Stop form from submitting normally
	  event.preventDefault();
	 
	  // Get some values from elements on the page:
	  var $form = $( this ),
	    name = $form.find( "input[name='name']" ).val(),
	    url  = $form.attr( "action" );
	 
	  // Send the data using post
	  var posting = $.post( url, { name: name } );
	 
	  // Put the results in a div
	  posting.done(function( data ) {
	    var content = $( data ).find( "#formContainer" );
	    $( "#formContainer" ).empty().append( content );
	  });
	});	

	setInterval(update, 5000);
</script>
</body>
</html>
