<article class="student-login">
	<div class="panel panel-default">
		<div class="panel-heading">
			<i class="fa fa-lock fa-5x pull-right"></i>
			<h1>Login | Admin</h1>
			<p>Enter your student number to log in.</p>
		</div>
		
		<?php echo form_open('administrator') ?>

		<div class="panel-body">

			<?php  if(validation_errors() != null): ?>
				<div class="alert alert-danger">
					<h4><i class="fa fa-exclamation-triangle"></i> Error</h4>
					<?php echo validation_errors('<li>','</li>'); ?>
				</div>
			<?php endif; ?>

			<?php  if($this->session->flashdata('error') != null): ?>
				<div class="alert alert-danger">
					<h4><i class="fa fa-exclamation-triangle"></i> Error</h4>
					<?php echo $this->session->flashdata('error'); ?>
				</div>
			<?php endif; ?>

			<?php  if($this->session->flashdata('message') != null): ?>
				<div class="alert alert-success">
					<h4><i class="fa fa-info-circle"></i> Error</h4>
					<?php echo $this->session->flashdata('message'); ?>
				</div>
			<?php endif; ?>

			<div class="form-group">
				<label>Username</label>
				<input type="text" class="form-control" placeholder="Enter Username." autofocus="autofocus" name="Username" value="<?php echo set_value('Username'); ?>">
			</div>		

			<div class="form-group">
				<label>Password</label>
				<input type="password" class="form-control" placeholder="Enter Password." name="Password">
			</div>	

		</div><!-- panel-body -->
		<div class="panel-footer">
			<button type="submit" name="btn_login" class="btn btn-primary btn-block">Login</button>
		</div><!-- panel-footer -->

		<?php echo form_close(); ?><!-- form close -->
	</div><!-- panel-default -->
</article><!-- student-login -->
