<?php

function imgExists($filename)
{
	if (file_exists($filename)) {
	    return base_url($filename);
	} else {
	    return base_url('components/images/misc/user-default.gif');
	}
}

?>

<div class="modal fade modal_logout">
	<div class="modal-dialog">
		<div class="modal-content">

		</div>
	</div>
</div>

<article class="professor-information">
	<a href="<?php echo base_url('student/logout_modal'); ?>" class="btn btn-danger pull-right" data-toggle="modal" data-target=".modal_logout"><i class="fa fa-power-off"></i> Logout</a>
	<h2 class="page-header">Professor Information</h2>
	<div class="panel panel-default">
		<div class="panel-body">

			<div class="col-md-5">
				<div class="media">
					<div class="media-left">
						<?php $filename = imgExists("prof_img/$profinfo->faculty_id.JPG");  ?>
						<img src="<?php echo $filename; ?>" alt="Professor Photo" class="media-object img-circle img-responsive">
					</div>
					<div class="media-body">
						<h3><?php  if (!empty($profinfo->faculty_name)) echo $profinfo->faculty_name; ?></h3>
					</div>
				</div>

			</div>
			<div class="col-md-7">
				<form class="form-horizontal">
					<div class="col-md-4">
						<div class="form-group">
							<label class="col-md-2 control-label">CFN:</label>
							<div class="col-md-10">
								<p class="form-control-static">
								<?php if (!empty($profinfo->cfn)) echo $profinfo->cfn; ?>
								</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Subject:</label>
							<div class="col-md-9">
								<p class="form-control-static">
								<?php if (!empty($profinfo->CourseCode)) echo $profinfo->CourseCode; ?>
								</p>
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<div class="form-group">
							<label class="col-md-5 control-label">Subject Code:</label>
							<div class="col-md-7">
								<p class="form-control-static">
								<?php if (!empty($profinfo->CourseDesc)) echo $profinfo->CourseDesc; ?>
								</p>
							</div>
						</div>
					</div>
				</form>
			</div>

		</div><!-- panel-body -->
	</div><!-- panel-default -->
</article><!-- professor-information -->
