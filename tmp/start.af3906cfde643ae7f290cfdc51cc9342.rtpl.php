<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html>
<head>
	<title><?php echo $title;?></title>
	
	<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.2/jquery.mobile.min.js"></script>
	<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.2/jquery.mobile.min.css" />
	<link href="tpl/style.css" type="text/css" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>

<div data-role="page" id="page">
	
	<h1><?php echo $title;?></h1>
	<p><?php echo $_welcome;?></p>
	<?php if( isKioskMode() ){ ?>
	<div id="kioskInfo"></div>
	<?php } ?>
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

	<div id="statusContainer" data-role="footer">
	<?php if( !isCheckedIn() ){ ?>
	<?php $tpl = new RainTPL;$tpl->assign( $this->var );$tpl->draw( "checkInForm" );?>
	<?php } ?>

	<?php if( isCheckedIn() ){ ?>

	<?php } ?>

	<?php if( isArrived() ){ ?>

	<?php } ?>
	</div>
</div>

<script type="text/javascript">

	var user_position = "<?php echo $default_position;?>";
	var last_Update   = "<?php echo $initial_time;?>";
	<?php if( isKioskMode() ){ ?>
	var ipShown = false;
	<?php } ?>

<?php if( locationIsNeeded() ){ ?>
	if(navigator.geolocation){
		navigator.geolocation.getCurrentPosition(successCallback,errorCallback,{timeout:10000});

		function successCallback (position) {
			user_position = 
				position.coords.latitude 
				+ "," 
				+ position.coords.longitude ;
		}

		function errorCallback (events) {
		}
	}
<?php } ?>
	var posting;
	function update () {
		var $todaysList = $("#todays_arrivals_list");
		var $formerList = $("#former_arrivals_list");
		var args = "";

		$.getJSON("api.php?get=arrivals").done(function(data) {
		    $todaysList.html(data);
		 })
		args = JSON.stringify({"filter": "former"});
		$.ajax("api.php?get=arrivals&args=" + args).done(function(data) {
		    $formerList.html(data);
		    
		 })

		<?php if( isKioskMode() ){ ?>
		$.getJSON("api.php?get=kiosk&args=showIP&output=json", function(data){
			ipShown = displayIP(data.enabled == "true", ipShown);
		});
		
		<?php } ?>

	}
<?php if( isKioskMode() ){ ?>
	
	function displayIP(showIP, shown){
		if(showIP && !shown) {
			getGlobalIP(function(globalIP, args){
				getLocalIP(function(localIP, args){
					$('#kioskInfo').html('global: ' + globalIP + 'local: ' + localIP );
				});
			});

			shown = true;

		}else if (showIP == false && shown) {
			shown = false;
			$('#kioskInfo').html("");
		}
		return shown;
	}

	function getGlobalIP (successF, args) {
		$.getJSON("http://jsonip.appspot.com?callback=?",
			function(data){
				if(successF != null){
		        	successF(data.ip, args);
		        }
			});
	}

	function getLocalIP (successF, args) {
		var RTCPeerConnection = /*window.RTCPeerConnection ||*/ window.webkitRTCPeerConnection || window.mozRTCPeerConnection;
		var localIP = '';
		// NOTE: window.RTCPeerConnection is "not a constructor" in FF22/23
		

		if (RTCPeerConnection) (function () {
		    var rtc = new RTCPeerConnection({iceServers:[]});
		    if (window.mozRTCPeerConnection) {      // FF needs a channel/stream to proceed
		        rtc.createDataChannel('', {reliable:false});
		    };
		    
		    rtc.onicecandidate = function (evt) {
		        if (evt.candidate) grepSDP(evt.candidate.candidate);
		    };
		    rtc.createOffer(function (offerDesc) {
		        grepSDP(offerDesc.sdp);
		        rtc.setLocalDescription(offerDesc);
		    }, function (e) { console.warn("offer failed", e); });
		    
		    
		    var addrs = Object.create(null);
		    addrs["0.0.0.0"] = false;
		    function updateDisplay(newAddr) {
		        if (newAddr in addrs) return;
		        else addrs[newAddr] = true;
		        var displayAddrs = Object.keys(addrs).filter(function (k) { return addrs[k]; });
		        localIP = localIP + displayAddrs.join(" or perhaps ") || "n/a";
		        if(successF != null){
		        	successF(localIP, args);
		        }
		    }
		    
		    function grepSDP(sdp) {
		        var hosts = [];
		        sdp.split('\r\n').forEach(function (line) { // c.f. http://tools.ietf.org/html/rfc4566#page-39
		            if (~line.indexOf("a=candidate")) {     // http://tools.ietf.org/html/rfc4566#section-5.13
		                var parts = line.split(' '),        // http://tools.ietf.org/html/rfc5245#section-15.1
		                    addr = parts[4],
		                    type = parts[7];
		                if (type === 'host') updateDisplay(addr);
		            } else if (~line.indexOf("c=")) {       // http://tools.ietf.org/html/rfc4566#section-5.7
		                var parts = line.split(' '),
		                    addr = parts[2];
		                updateDisplay(addr);
		            }
		        });
		    }
		})(); else {
		   localIP = localIP + "<code>ifconfig | grep inet | grep -v inet6 | cut -d\" \" -f2 | tail -n1</code>";
		   localIP = localIP + "In Chrome and Firefox your IP should display automatically, by the power of WebRTCskull.";
		}

	}
<?php } ?>
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

	function gup( name ){
		name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
		var regexS = "[\\?&]"+name+"=([^&#]*)";
		var regex = new RegExp( regexS );
		var results = regex.exec( window.location.href ); 
		if( results == null )    return "";
		else    return results[1];
	}

	setInterval(update, 5000);
</script>
</body>
</html>
