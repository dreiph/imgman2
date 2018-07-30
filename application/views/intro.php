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
	
	<script src="public/jq/jquery-3.3.1.min.js"></script>
	<script src="public/uikit/js/uikit.min.js"></script>
	<script src="public/uikit/js/components/lightbox.min.js"></script>
	<script src="public/uikit/js/components/upload.min.js"></script>
</head>

<body>
	<div class="uk-container uk-container-center">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<div class="logo">
					<div class="logomenu uk-align-left">
						<img src="public/img/imgman.png" alt="Logo">
					</div>
					<div class="uk-text-right">
						<a href="" class="uk-button">
							<i class="uk-icon-user"></i> Logout
						</a>
					</div>
				</div>
			</div>
		</div>
		
		<div class="uk-grid">
			<div class="uk-width-1-1">
			<div class="filter">
				<div class="uk-align-left">
					<form class="uk-form uk-form-horizontal" action="site/search" method="post">
					Filter by:
					<a href="site/action/date_asc" class="uk-button uk-button-primary <?php if($_SESSION['order']=='datetime_uploaded' && $_SESSION['sort']=='asc'){ echo 'filter_marked'; } ?>">
						Date ASC <i class="uk-icon-sort-alpha-asc"></i>
					</a>
					<a href="site/action/date_desc" class="uk-button uk-button-primary <?php if($_SESSION['order']=='datetime_uploaded' && $_SESSION['sort']=='desc'){ echo 'filter_marked'; } ?>">
						Date DESC <i class="uk-icon-sort-alpha-desc"></i>
					</a>
					<input type="text" name="sq" value="<?php if(isset($_SESSION['sq'])){ echo $_SESSION['sq']; } ?>" placeholder="Enter search query" class="uk-input" />
					<button class="uk-button uk-button-primary">
						<i class="uk-icon-search"></i> Search
					</button>
					</form>
				</div>
				<div class="uk-text-right">
					<a class="uk-button uk-button-success">
						<i class="uk-icon-plus"></i> Upload
					</a>
				</div>
			</div>
			</div>
		</div>
		
		<div class="uk-grid">
			<div class="uk-width-1-1">
			<?php if(isset($error)): ?>
			<div class="uk-alert uk-alert-danger">
			<h6><?php echo $error; ?></h6>
			</div>
			<?php else: ?>
			<div class="uk-grid results">	
				<div class="uk-width-1-1">
				<table class="uk-table uk-table-striped">
				<tr>
					<th>Image</th>
					<th>Dimensions</th>
					<th>Original filename</th>
					<th>Path to CDN image</th>
					<th>Upload date</th>
				</tr>
				<?php foreach($results as $r): ?>
				<tr>
					<td>
						<a href="<?php echo $r->img_system_filename; ?>" data-uk-lightbox="{group:'my-group'}" title="<?php echo base_url(); ?><?php echo $r->img_system_filename; ?> ">
						<img  class="uk-thumbnail" src="<?php echo $r->img_system_filename; ?>" data-id="<?php echo $r->uid; ?>" />
						</a>
					</td>
					<td>
						<?php echo $r->img_dimensions; ?>
					</td>
					<td>
						<?php echo $r->img_upload_filename; ?>
					</td>
					<td>
						<?php echo base_url(); ?><?php echo $r->img_system_filename; ?> 
						<a href="<?php echo base_url(); ?><?php echo $r->img_system_filename; ?>" target="_blank" title="Open in new tab">
							<i class="uk-icon-arrow-right"></i>
						</a>
					</td>
					<td>
						<?php echo $r->datetime_uploaded; ?>
					</td>
				</tr>
				<?php endforeach; ?>
				</table>
				</div>
			</div>
			<?php endif; ?>
			</div>
		</div>
		
		<div class="uk-grid">
			<div class="uk-width-1-1">
			<div class="pagination">
			<?php if(isset($links)){ echo $links; } ?>
			</div>
			</div>
		</div>
		
		<div class="uk-grid">
			<div class="uk-width-1-1">
			<div class="footer">&copy 2018 dreiph.com</div>
			</div>
		</div>
	</div>
	


<div id="progressbar" class="uk-progress uk-hidden">
    <div class="uk-progress-bar" style="width: 0%;">...</div>
</div>

<div id="upload-drop" class="uk-placeholder uk-text-center">
<i class="uk-icon-cloud-upload uk-icon-medium uk-text-muted uk-margin-small-right"></i> Attach binaries by dropping them here or <a class="uk-form-file">selecting one<input id="upload-select" type="file"></a>.
</div>

<script>

    $(function(){

        var progressbar = $("#progressbar"),
            bar         = progressbar.find('.uk-progress-bar'),
            settings    = {

            action: '<?php echo base_url(); ?>site/upload', // upload url

            allow : '*.(jpg|jpeg|gif|png)', // allow only images

            loadstart: function() {
                bar.css("width", "0%").text("0%");
                progressbar.removeClass("uk-hidden");
            },

            progress: function(percent) {
                percent = Math.ceil(percent);
                bar.css("width", percent+"%").text(percent+"%");
            },

            allcomplete: function(response) {

                bar.css("width", "100%").text("100%");

                setTimeout(function(){
                    progressbar.addClass("uk-hidden");
                }, 250);

                alert("Upload Completed")
            }
        };

        var select = UIkit.uploadSelect($("#upload-select"), settings),
            drop   = UIkit.uploadDrop($("#upload-drop"), settings);
    });

</script>
							
</body>
</html>