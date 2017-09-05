<article class="student-current-load">

	<?php if($this->session->flashdata('error') != NULL):  ?>
	<div class="alert alert-danger">
        <?php echo "<h4> <i class='fa fa-warning'></i> NOTIFICATION </h4>" . $this->session->flashdata('error'); ?>
    </div>
	<?php endif; ?>

	<?php  if($this->session->flashdata('message') != null): ?>
		<div class="alert alert-success">
			<h4><i class="fa fa-info-circle"></i> NOTIFICATION</h4>
			<?php echo $this->session->flashdata('message'); ?>
		</div>
	<?php endif; ?>

	<p class="lead page-header">Student Current Load</p>

	<div class="panel panel-default">


		<div class="panel-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>CFN</th>
						<th>SUBJECT CODE</th>
						<th>SUBJECT DESCRIPTION</th>
						<th>SECTION</th>
						<th>PROFESSOR</th>
						<th>STATUS</th>
						<th>ACTION</th>
					</tr>
				</thead>
				<tbody>
				<?php $studload_count = count($student_load); ?>
				<?php foreach($student_load as $studload):  ?>
					<?php $is_evaluated = FALSE; ?>
					<tr>
						<td><?php echo $studload->cfn; ?></td>
						<td><?php echo $studload->CourseCode; 	?></td>
						<td><?php echo $studload->CourseDesc; ?></td>
						<td><?php echo $studload->year_section; ?></td>
						<td><?php echo $studload->faculty_name; ?></td>
						<td>
							<?php if( in_array($studload->cfn, $cmat)):  ?>
								<?php $studload_count -= 1; ?>
								<?php echo "<span class='label label-default'>Cannot be evaluated,Please print your official Change of Matriculation.</span>"; $is_evaluated = TRUE; ?>
							<?php else: ?>
								<?php if( $studload->is_excluded):  ?>
									<?php $studload_count -= 1; ?>
									<?php echo "<span class='label label-default'>Cannot be evaluated.</span>"; $is_evaluated = TRUE; ?>
								<?php elseif ( ! empty($studload->Status) ):  ?>
									<?php $studload_count -= 1; ?>
									<?php echo "<span class='label label-default'>{$studload->Status}</span>"; $is_evaluated = TRUE; ?>
								<?php elseif ( $studload->remark == "DISSOLVED" ):  ?>
									<?php $studload_count -= 1; ?>
									<?php echo "<span class='label label-default'>{$studload->remark}</span>"; $is_evaluated = TRUE; ?>
								<?php else: ?>

									<?php if(count($incomplete_evaluation)): ?>
										<?php if(in_array($studload->cfn, $incomplete_evaluation)): ?>
											<?php echo "<span class='label label-warning'>INCOMPLETE EVALUATION</span>"; ?>
										<?php endif; ?>
									<?php endif; ?>

									<?php if(count($course_evaluated)): ?>
										<?php if(in_array($studload->cfn, $course_evaluated)): ?>
											<?php echo "<span class='label label-info'>ALREADY EVALUATED</span>"; $is_evaluated = TRUE; ?>
										<?php endif; ?>
									<?php endif; ?>

									<?php if( (count($incomplete_evaluation) || count($course_evaluated)) || (empty($incomplete_evaluation) && empty($course_evaluated)) ): ?>
										<?php if( ! in_array($studload->cfn, $course_evaluated) && ! in_array($studload->cfn, $incomplete_evaluation)): ?>
											<?php echo "<span class='label label-danger'>NOT YET EVALUATED</span>"; ?>
										<?php endif; ?>
									<?php endif; ?>
								<?php endif; ?>
							<?php endif; ?>
						</td>
						<td>
							<?php if($is_evaluated): ?>
								<span class="btn btn-primary btn-sm disabled">
									<i class="fa fa-edit"></i> Evaluate
								</span>
							<?php else: ?>
								<a href="<?php echo base_url("student/evaluate/{$studload->sched_id}"); ?>" class="btn btn-primary btn-sm btn_evaluate">
									<i class="fa fa-edit"></i> Evaluate
								</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php $this->session->set_userdata('studload_count',$studload_count); ?>
				</tbody>
			</table><!-- table -->
		</div><!-- panel-body -->
		<div class="panel-footer text-center">
			xxx NOTHING FOLLOWS xxx
		</div>
	</div><!-- panel-defaul -->
</article><!-- student-current-load -->

<div class="modal fade reminder_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title text-center">
					Welcome to the Online Faculty Evaluation <br>
					<?php echo $this->session->userdata('SemCode'); ?>
					A.Y. <?php echo $this->session->userdata('SyDesc'); ?> <br>
					Please read and follow all reminders before you start your evaluation.
				</h2>
			</div>
			<div class="modal-body">
			  	<ol>
			  		<h3>
			  			<li><code>EVALUATE OBJECTIVELY</code> in accordance to your instructors/professor's performance</li>
			  		</h3>
			  		<h3>
			  			<li><code>NEVER USE PROFANE OR FOUL WORDS</code> in the comment/suggestion boxes</li>
			  		</h3>
			  		<h3>
			  			<li><code>NEVER TAKE PHOTOS</code> of the content of the Faculty Evaluation for whatever purpose it may serve</li>
			  		</h3>
			  		<h3>
			  			<li><code>NEVER SHARE YOUR PASSWORD TO ANYONE</code></li>
			  		</h3>
			  		<h3>
			  			<li>You are given <code>30 MINUTES TO EVALUATE</code> your professors</li>
			  		</h3>
			  		<h3>
			  			<li><code>NO-EVALUATION MEANS NO ACCESS TO GRADES</code></li>
			  		</h3>
			  	</ol>
			</div>
		</div>
	</div>
</div>

<div class="modal fade reminder_modal2">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title text-center">
					Welcome to the Online Faculty Evaluation <br>
					<?php echo $this->session->userdata('SemCode'); ?>
					A.Y. <?php echo $this->session->userdata('SyDesc'); ?> <br>
					Please read and follow all reminders before you start your evaluation.
				</h2>
			</div>
			<div class="modal-body">
			  	<h3>Please be reminded that the Online Faculty Evaluation has <code>30 minutes</code> time limit, Once you start the evaluation the time limit will begin and it will accumulate the used time, The application will automatically log-out if you accumulate your 30 minutes time limit.</h3>
			</div>
		</div>
	</div>
</div>


