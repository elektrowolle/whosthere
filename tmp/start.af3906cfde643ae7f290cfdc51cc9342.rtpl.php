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
<h2><?php echo $_todays_arrivals;?></h2>
<div id="todays_arrivals_list">
<?php echo setTplMessage( "today_arrivals" );?>
<?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "arrivalList" );?>
</div>

<h2><?php echo $_former_arrivals;?></h2>
<div id="former_arrivals_list">
<?php echo setTplMessage( "former_arrivals" );?>
<?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "arrivalList" );?>
</div>

<div id="statusContainer">
<?php if( !isCheckedIn() ){ ?>
<?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "checkInForm" );?>
<?php } ?>

<?php if( isCheckedIn() ){ ?>

<?php } ?>

<?php if( isArrived() ){ ?>

<?php } ?>
</div>


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
		var $todaysList = $("#todays_arrivals_list");
		$.ajax("arrivalsListUpdate.php").done(function(data) {
		    $todaysList.html(data);
		 })

		var $formerList = $("#former_arrivals_list");
		$.ajax("arrivalsListUpdate.php?filter=former").done(function(data) {
		    $formerList.html(data);
		 })
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
	    $( "#formContainer" ).html(data);
	    update();
	  });
	});	

	setInterval(update, 5000);
</script>
</body>
</html>
