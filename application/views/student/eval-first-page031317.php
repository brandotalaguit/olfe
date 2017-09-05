<?php $this->load->view("includes/head"); ?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
				<?php $this->load->view("php/article-professor-information"); ?>
				<?php $this->load->view("php/snippet-legend"); ?>
				<?php $this->load->view("php/article-first-page"); ?>
				<?php $this->load->view("php/footer"); ?>
		</div><!-- col-md-12 -->
	</div><!-- row -->
</div><!-- container -->

<?php $this->load->view("includes/footer"); ?>

<!-- Modal -->
<div class="modal fade validation_error"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Validation Error</h4>
      </div>
      <div class="modal-body">
        Please fill up all information to continue.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php if($this->session->userdata("sess_error") == true){ ?>
  <script type="text/javascript">
      $(window).load(function(){
          $('.validation_error').modal('show');
      });
  </script>
<?php } ?>

