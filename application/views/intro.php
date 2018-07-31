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
	<div class="uk-container uk-container-center">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<div class="logo">
					<div class="logomenu uk-align-left">
						<img src="public/img/imgman.png" alt="Logo">
					</div>
					<div class="uk-text-right">
						There <?php echo($stats_count > 1 ? "are" : "is"); ?> <strong style="font-size:1.5rem;"><?php echo $stats_count; ?></strong> image<?php echo($stats_count > 1 ? "s" : ""); ?> on the system.
						<a href="site/logout" class="uk-button">
							<i class="uk-icon-user"></i> Logout<strong><?php if(isset($_SESSION['username'])) { echo ", ", $_SESSION['username']; } ?></strong><?php if(isset($_SESSION['role'])){ echo " (".$_SESSION['role'].")"; } ?>
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
					<?php if(isset($image_dimensions)): ?>
					<select id="image_dimensions" name="image_dimensions">
					<option value="ALL" <?php if(!isset($_SESSION['imde'])) { echo "selected"; } ?>>ALL</option>
					<?php foreach($image_dimensions as $imde): ?>
					<option value="<?php echo $imde->imde; ?>" <?php if(isset($_SESSION['imde'])) { if($_SESSION['imde']==$imde->imde) { echo "selected"; } } ?>><?php echo $imde->imde; ?></option>
					<?php endforeach ?>
					</select>
					<?php endif; ?>
					
					<input type="text" name="sq" value="<?php if(isset($_SESSION['sq'])){ echo $_SESSION['sq']; } ?>" placeholder="Enter search query" class="uk-input" />
					<button class="uk-button uk-button-primary">
						<i class="uk-icon-search"></i> Search
					</button>
					</form>
				</div>
				<div class="uk-text-right">
					<a class="uk-button uk-button-success" href="#modal_upload" data-uk-modal="{center:true}">
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
					<?php if($_SESSION['role']=='admin'): ?><th>Action</th><?php endif; ?>
					<th>Image</th>
					<th>Dimensions</th>
					<th title="In KB">Filesize</th>
					<th>Original filename</th>
					<th>Path to CDN image</th>
					<th>Upload date</th>
				</tr>
				<?php foreach($results as $r): ?>
				<tr>
					<?php if($_SESSION['role']=='admin'): ?>
					<td>
					<form action="site/delete" method="post" name="frm">
					<input type="hidden" name="uid" value="<?php echo $r->uid; ?>" />
					<input type="submit" name="delete" value="X" class="uk-button uk-button-danger" />
					</form>
					</td>
					<?php endif; ?>
					<td>
						<a href="<?php echo $this->config->item('cdn_url').$r->img_system_filename; ?>" data-uk-lightbox="{group:'my-group'}" title="<?php echo base_url(); ?><?php echo $r->img_system_filename; ?> ">
						<img  class="uk-thumbnail" src="<?php echo $this->config->item('cdn_url').$r->img_system_filename; ?>" data-id="<?php echo $r->uid; ?>" />
						</a>
					</td>
					<td>
						<?php echo $r->img_dimensions; ?>
					</td>
					<td>
						<?php echo $r->img_filesize; ?> KB
					</td>
					<td>
						<?php echo $r->img_upload_filename; ?>
					</td>
					<td>
						<span><?php echo base_url(),str_ireplace("\\", "/", $this->config->item('cdn_url')); ?><?php echo $r->img_system_filename; ?></span>
						<a href="<?php echo base_url(),str_ireplace("\\", "/", $this->config->item('cdn_url')); ?><?php echo $r->img_system_filename; ?>" target="_blank" title="Open in new tab">
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
	

<!-- This is the modal for uploading images -->
<div id="modal_upload" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
		<div id="progressbar" class="uk-progress uk-hidden">
			<div class="uk-progress-bar" style="width: 0%;">...</div>
		</div>

		<div id="upload-drop" class="uk-placeholder uk-text-center">
		<i class="uk-icon-cloud-upload uk-icon-medium uk-text-muted uk-margin-small-right"></i> Attach binaries by dropping them here or <a class="uk-form-file">selecting one<input id="upload-select" name="userfile" type="file"></a>.
		</div>
	</div>
</div>	

<!-- This is the modal for messages -->
<div id="modal_msg" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
		<div class="uk-alert uk-alert-success">
			<h4>
			<?php if(isset($_GET['msg'])) { echo $_GET['msg']; } ?>
			</h4>
		</div>
	</div>
</div>
	
<!--Upload script-->
<script>
    $(function(){

        var progressbar = $("#progressbar"),
            bar         = progressbar.find('.uk-progress-bar'),
            settings    = {

            action: '<?php echo base_url(); ?>site/upload', // upload url

            allow : '*.(jpg|jpeg|gif|png)', // allow only images
			
			param : 'userfile',

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

                var modal = UIkit.modal("#modal_upload");
				if ( modal.isActive() ) {
					modal.hide();
					location.reload();
				} else {
					modal.show();
				}
            }
        };

        var select = UIkit.uploadSelect($("#upload-select"), settings),
            drop   = UIkit.uploadDrop($("#upload-drop"), settings);
    });
	(function($){
		$('#image_dimensions').on('change', function(){
			window.location='site/action/imde/?image_dimensions='+$(this).val();
		});
		
		<?php if(isset($_GET['msg'])): ?>
		var modal_msg = UIkit.modal("#modal_msg");
		modal_msg.show();
		<?php endif; ?>
		
		//copying
		const span = document.querySelector("span");

		span.onclick = function() {
			document.execCommand("copy");
		}

		span.addEventListener("copy", function(event) {
			event.preventDefault();
			if (event.clipboardData) {
				event.clipboardData.setData("text/plain", span.textContent);
				console.log(event.clipboardData.getData("text"))
			}
		});
	})(jQuery);
</script>
							
</body>
</html>