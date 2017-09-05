<article class="student-login">
	<div class="panel panel-default">
		<div class="panel-heading">
			<i class="fa fa-lock fa-5x pull-right"></i>
			<h1>Login | Student</h1>

			<p>
					Evaluation Period will start on <?php echo $date_start ?> until <?php echo $date_end?> within <?php echo date("h:i A",strtotime($time_start))." to ".date("h:i A",strtotime($time_end)) ?>
			</p>

			<p>
					Active User : <?php echo $actived_user->cnt ?>
			</p>
		</div>

		<?php echo form_open('student') ?>

		<div class="panel-body">

			<div class="alert alert-info">
				<h4><i class="fa fa-info-circle"></i> REMINDERS</h4>

				<p>Please login first to activate your official University email address before you start the evaluation, if you don't have your email yet, Please proceed to Information Technology Center (ITC) to get your email account.</p>
			</div>


			<?php  if(validation_errors() != null): ?>
				<div class="alert alert-danger">
					<h4><i class="fa fa-exclamation-triangle"></i> NOTIFICATION</h4>
					<?php echo validation_errors('<li>','</li>'); ?>
				</div>
			<?php endif; ?>

			<?php  if($this->session->flashdata('error') != null): ?>
				<div class="alert alert-danger">
					<h4><i class="fa fa-exclamation-triangle"></i> NOTIFICATION</h4>
					<?php echo $this->session->flashdata('error'); ?>
				</div>
			<?php endif; ?>

			<?php  if($this->session->flashdata('message') != null): ?>
				<div class="alert alert-success">
					<h4><i class="fa fa-info-circle"></i> NOTIFICATION</h4>
					<?php echo $this->session->flashdata('message'); ?>
				</div>
			<?php endif; ?>

		<?php if (!$IsClosed): ?>

				<div class="form-group">
					<label>UMak Email</label>
					<input type="email" class="form-control" placeholder="Enter your UMak Email Address" name="Email" value="<?php echo set_value('Email'); ?>">
				</div>

				<div class="form-group">
					<label>Prove that you are a human</label> <br>
					<label for="captcha"><?php echo $captcha['image']; ?></label>
					<input type="text" class="form-control" placeholder="Type the character that appears above" name="captcha">
				</div>



		</div><!-- panel-body -->
		<div class="panel-footer">
			<button type="submit" name="btn_login" class="btn btn-primary btn-block">Send Email</button>
		</div><!-- panel-footer -->
		<?php else: ?>
			<p class="text-center lead">
				Evaluation Period will start on <?php echo $date_start ?> until <?php echo $date_end?> within <?php echo date("h:i A",strtotime($time_start))." to ".date("h:i A",strtotime($time_end)) ?>
			</p>
		</div>
		<?php endif ?>

		<?php echo form_close(); ?><!-- form close -->
	</div><!-- panel-default -->
</article><!-- student-login -->
