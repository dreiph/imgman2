<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en-gb" dir="ltr">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Imgman</title>
	
	<base href="<?php echo base_url(); ?>" />
	
	<link rel="shortcut icon" href="public/img/favicon.ico" type="image/x-icon">

	<link rel="stylesheet" href="public/uikit/css/uikit.min.css">
	<link rel="stylesheet" href="public/uikit/css/style.css">
	<link rel="stylesheet" href="public/uikit/css/components/slidenav.min.css">
	<link rel="stylesheet" href="public/uikit/css/components/placeholder.min.css">
	<link rel="stylesheet" href="public/uikit/css/components/form-file.min.css">
	<link rel="stylesheet" href="public/uikit/css/components/progress.min.css">
	
	<script src="public/jq/jquery-3.3.1.min.js"></script>
	<script src="public/uikit/js/uikit.min.js"></script>
	<script src="public/uikit/js/components/lightbox.min.js"></script>
	<script src="public/uikit/js/components/upload.min.js"></script>
</head>

<body>
	<div class="uk-container uk-container-center lock">
		<div class="uk-grid">
			<div class="uk-width-1-1 uk-margin-top">
				
			</div>
			<div class="uk-width-6-10">
			</div>
			<div class="uk-width-3-10" style="margin-top:20vh;">
				<h2>Please login</h2>
				<?php if(isset($error)): ?>
				<div class="uk-alert uk-alert-danger">
					<h6><?php echo $error; ?></h6>
				</div>
				<?php endif; ?>
				<form action="" method="post" name="frm1" id="frm1" class="uk-form">
					<input type="text" name="username" id="username" placeholder="Username" class="uk-width-1-1" /><br /> 
					<input type="password" name="password" id="password" placeholder="Password" class="uk-width-1-1"  /><br /> 
					<input type="submit" name="send" id="send" value="Login" class="uk-button uk-button-primary uk-width-1-1" />
				</form>
			</div>
		</div>
	</div>
</body>
</html>