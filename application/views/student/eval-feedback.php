<?php $this->load->view("includes/head"); ?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php $this->load->view("php/article-professor-information"); ?>

			<article class="feedback">
				<div class="panel panel-default">
					<div class="panel-body">
						<h1><i class="fa fa-pencil"></i> Feedback:</h1>
						<p>The University and your professor want to hear some of your comments or suggestions. </p>
						<?php echo form_open("student/feedback/{$this->uri->segment(3)}"); ?>
							<div class="form-group">
								<textarea id="field" rows="8" class="form-control" maxlength="500" onkeyup="countChar(this)" placeholder="Type here.." name="studfeedback"></textarea>
								<span class="help-block"><p id="charNum" class="help-block ">500 characters left</p></span>
							</div>
							<div class="form-group">
								<a href="<?php echo base_url("student/evaluate/{$this->uri->segment(3)}"); ?>" class="btn btn-primary">&laquo; Back</a>
								<button type="submit" class="btn btn-primary" name="btnsubmit"><i class="fa fa-save"></i> Save and Finalize</button>
							</div>
						<?php echo form_close(); ?>
					</div>
				</div>
			</article><!-- feedback -->
			<?php $this->load->view("php/footer"); ?>
		</div><!-- col-md-12 -->		
	</div><!-- row -->
</div><!-- container -->

<script>
  	function countChar(val) {
	    var len = val.value.length;
	    if (len > 500) {
	      	val.value = val.value.substring(0, 500);
	    } else {
	      	$('#charNum').text(500 - len + " characters left");

	      	var string = $('textarea').val();
	      	var regex = /fucking|fuck|fuck you|pakyu|baliw|inamo|tangina|gago|siraulo|shit|bitch|asshole|/ig;
 
			var updatedString = string.replace( regex, function(s) {
		  	var i = 0;
		  	var asterisks = "";
			  	while (i < s.length) {
				    asterisks += "*";
				    i++;
			  	}
			  	return asterisks;
			});

			$('textarea').empty().val(updatedString);
	    }
  	};
</script>
<?php $this->load->view("includes/footer"); ?>


<div class="modal fade summary_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title">Rating Summary</h3>
			</div>
			<div class="modal-body">
				<?php if(count($evaluation_summary)): ?>
				<table class="table table-condensed table-hover">
					<thead>
						<tr>
							<th>Question</th>
							<th>Rating</th>
						</tr>
					</thead>
					<tbody>
						<?php $current_category = ''; ?>
						<?php foreach ($evaluation_summary as $eval_summary): ?>

							<?php if($current_category == '' || $current_category != $eval_summary->category_desc): ?>
							<?php $current_category = $eval_summary->category_desc; ?>
							<tr>
								<th colspan="2"><?php echo $current_category; ?></th>
							</tr>
							<?php endif; ?>

							<tr>
								<td><?php echo $eval_summary->Question; ?></td>
								<td><?php echo $eval_summary->stud_rating; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php endif; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

