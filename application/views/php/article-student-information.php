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

<article class="student-information">
	<a href="<?php echo base_url('student/logout_modal'); ?>" class="btn btn-danger pull-right" data-toggle="modal" data-target=".modal_logout"><i class="fa fa-power-off"></i> Logout</a>
	<h2 class="page-header">Student Information</h2>
	<div class="panel panel-default">
		<div class="panel-body">
			<form class="form-horizontal">
				<div class="col-md-5">
					<div class="form-group">
						<div class="media">
						  	<div class="media-left">
							  	<?php $filename = imgExists("student_img/PICTURES/{$this->uri->segment(3)}.JPG");  ?>
						      	<img src="<?php echo $filename; ?>" alt="Student Photo" class="img-circle img-responsive media-object">
						  	</div>
						  	<div class="media-body">
								<h3 style="margin-top:5px;"><?php echo $student->Lname .','. $student->Fname .' '. $student->Mname; ?></h3>
								<p class="lead"><?php echo $this->uri->segment(3); ?></p>
						  	</div>
						</div>							
					</div>
				</div>
						
				<div class="col-md-4">
					<div class="form-group">
						<label class="col-md-2 control-label">College:</label>
						<div class="col-md-10">
							<p class="form-control-static"><?php echo $student->CollegeDesc; ?></p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 control-label">Program:</label>
						<div class="col-md-10">
							<p class="form-control-static"><?php echo $student->ProgramDesc; ?></p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 control-label">Major:</label>
						<div class="col-md-10">
							<p class="form-control-static"><?php echo $student->MajorDesc; ?></p>	
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">						
						<label class="col-md-6 control-label">Academic Year:</label>
						<div class="col-md-6">
							<p class="form-control-static"><?php echo $this->session->userdata('SyDesc'); ?></p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-6 control-label">Term:</label>
						<div class="col-md-6">
							<p class="form-control-static"><?php echo $this->session->userdata('SemCode'); ?></p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-6 control-label">Year Level:</label>
						<div class="col-md-6">
							<p class="form-control-static"><?php echo $student->YrLevel; ?></p>
						</div>
					</div>
				</div>	

			</form>	
		</div>
	</div>
</article><!-- student-information -->

