<!DOCTYPE html>
<html>
<head>

	<title>Faculty Evaluation |</title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Planning Development Center Management Information System">
    <meta name="author" content="Michael Anthony C. Castro">

	<link rel="shortcut icon" type="image/png" href="<?php echo base_url()?>components/images/misc/umak-logo.png">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>bootstrap/font-awesome/css/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>bootstrap/css/sidebar.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>flipclock/flipclock.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>components/css/custom.css">
	
</head>
<body>

<?php if(isset($show_clock)): ?>
	<div class="timer_container">
		<div class="clock" style="margin:2em;"></div>
	</div>
<?php endif; ?>
