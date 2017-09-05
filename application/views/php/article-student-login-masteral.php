<article class="student-login">
	<div class="panel panel-default">
		<div class="panel-heading">
			<i class="fa fa-lock fa-5x pull-right"></i>
			<h1>Login</h1>
			<p>Enter your birthday to confirm.</p>
		</div>
		<div class="panel-body">

			<?php  if(validation_errors() != null){ ?>
				<div class="alert alert-danger" style="text-align:center;">
					<i class="fa fa-exclamation-triangle fa-lg"></i><?php echo validation_errors(); ?>
				</div>
			<?php } ?>

			<?php echo form_open('student/masteral') ?>
				<div class="form-group">
					<label>Student ID</label>
					<input type="text" class="form-control"  placeholder="Student ID" autofocus="autofocus" name="input_studid"> <br>
					<label>Birthday</label>
					<input type="date" class="form-control"  autofocus="autofocus" name="input_bday">
				</div>			
		</div><!-- panel-body -->
		<div class="panel-footer">
			<button type="submit" name="btnStudLogin" class="btn btn-primary btn-block">Confirm</button>
		</div><!-- panel-footer -->
		<?php echo form_close(); ?><!-- form close -->
	</div><!-- panel-default -->
</article><!-- student-login -->
