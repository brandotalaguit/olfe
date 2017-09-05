<?php echo form_open('student/logout'); ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title">Logout Student?</h4>
</div>
<div class="modal-body">
	<?php if($studload_count != $studcourseeval_count): ?>
		You are about to logout, You still have subject(s) to be evaluated, Please type your reason why you did not complete your evaluation?
		<textarea name="reason" class="form-control"></textarea>
	<?php else: ?>
		Are you sure do you want to logout?
	<?php endif; ?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button type="submit" class="btn btn-danger">Yes</button>
</div>
<?php echo form_close(); ?>