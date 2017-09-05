<article class="student-verification">
	<?php echo form_open("administrator/student_verification"); ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1>Student Verification</h1>
				<p>Enter the student number to verify.</p>
			</div>				
			<div class="panel-body">

				<img src="<?php echo base_url(); ?>student_img/PICTURES/<?php if(isset($studno))echo $studno.".JPG"; ?>" alt="Student Photo" class="img-circle img-responsive media-object" onError="this.onerror=null;this.src='<?php echo base_url(); ?>components/images/misc/user-default.gif';">
					
					<?php if(validation_errors() != null){ ?>
						<div class="alert alert-danger" style="text-align:center;">
							<p><i class="fa fa-lg fa-exclamation-triangle"></i> <?php echo validation_errors(); ?></p>
						</div>
					<?php } ?>

					<?php if($this->session->flashdata('error') != null){ ?>
						<div class="alert alert-danger" style="text-align:center;">
							<p><i class="fa fa-lg fa-exclamation-triangle"></i> <?php echo $this->session->flashdata('error'); ?></p>
						</div>
					<?php } ?>

					<?php if(isset($success)){ ?>
						<div class="alert alert-success" style="text-align:center;">
							<p><i class="fa fa-lg fa-check-circle"></i><br> <?php echo $success; ?></p>
						</div>
					<?php } ?>

					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-user"></i></span>
							<input class="form-control" type="text" placeholder="This is.." disabled value="<?php if(isset($student))echo $student; ?>">
						</div>
					</div>	
					<div class="form-group">
						<label class="sr-only">Student Number</label>
						<input type="text" class="form-control" placeholder="Enter Student ID" autofocus="autofocus" style="text-transform:uppercase;" name="StudNo" 
						value="<?php if(isset($success)){} else {echo set_value('StudNo');} ?>">
					</div>											
				
			</div><!-- panel-body -->
			<div class="panel-footer">
				<button type="submit" class="btn btn-primary btn-block" name="btn_verify">Verify Student</button>
			</div><!-- panel-footer -->
		</div><!-- panel-default -->
	<?php echo form_close(); ?>
</article><!-- student-verification -->