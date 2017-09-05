<input type="hidden" name="sched_id" class="sched_id" value="<?php echo $this->uri->segment(3); ?>">

<!-- Script -->
<script type="text/javascript" src="<?php echo base_url()?>bootstrap/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>bootstrap/js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>flipclock/flipclock.js"></script>


<?php if(isset($evaluation_summary)): ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('.summary_modal').modal('show');
	});
</script>
<?php endif; ?>

<?php if(isset($show_clock)): ?>
<script type="text/javascript">
	var clock;
	
	$(document).ready(function() {

        

		clock = $('.clock').FlipClock("<?php echo $remaining_time; ?>", {
	        clockFace: 'MinuteCounter',
	        countdown: true,
	        callbacks: {
	        	stop: function() {
	    			update_remaining_time(clock.getTime().time);
	        	},
	        },
	    });
	});

</script>
<?php endif; ?>

<?php if($this->session->flashdata('valid_email_code') == TRUE): ?>
<script>
$(document).ready(function() {
	$('.reminder_modal').modal('show');
});
$('.reminder_modal').on('hide.bs.modal', function (e) {
	$('.reminder_modal2').modal('show')
})
</script>
<?php endif; ?>

<script>
function update_remaining_time(remaining_time)
{
	var url = "<?php echo base_url('student/update_remaining_time'); ?>";
    var dataString = 'remaining_time='+remaining_time;

	$.ajax({
        type: "POST",
        url: url,
        data: dataString,
        cache: false,
        dataType: 'HTML',
        success: function(result){
        	if(result == 'RELOAD')
			{
    			window.location = "<?php echo base_url('student/current_load'); ?>"
			}
        },
    });
}
</script>

<script>
$(document).ready(function() {

	// $('#btn_proceed_feedback').click(function() {
	// 	this.submit();
	//     $(this).attr('disabled', 'disabled');
	// });

	$(".btn_proceed_feedback").click(function(e) {
	    update_remaining_time(clock.getTime().time);
	});

	$(".btn_previous").click(function(e) {
	    e.preventDefault();

	    update_remaining_time(clock.getTime().time);
	    $('.timer_reminder_modal').modal('show');
		var remaining_time = clock.getTime().time;
		var minutes = parseInt(remaining_time/60);
		var seconds = parseInt(remaining_time % 60);
		seconds += (seconds < 10) ? '0': '';

		$('.timer_reminder_modal .remaining_time').empty().append(minutes +' minute(s) and '+ seconds +' second(s)');
	});

	$('.timer_reminder_modal').on('hide.bs.modal', function (e) {
  		window.location = '<?php echo base_url("student/current_load"); ?>';
	})

});
</script>

<!-- Menu Toggle Script -->
<script>
$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});
</script>


</body>
</html>