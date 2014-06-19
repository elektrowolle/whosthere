<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html>
<head>
	<title><?php echo $title;?></title>
	<link href="tpl/style.css" type="text/css" rel="stylesheet" >
	<script src="tpl/sockjs-client/lib/sockjs.js"></script>
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

<h2><?php echo $_check_in;?></h2>
</body>
</html>
