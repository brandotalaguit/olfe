<article class="legend">
	<p class="page-header lead">Rating Scale</p>
	<div class="panel panel-default">

		<div class="panel-body" style="font-size:16px;">
			
			<?php foreach($rating_scale as $r): ?>
			<div class="col-md-6"><p>
				<?php if ($r->r_choices== 0): ?>
					<?php echo '<b>X'." - ".$r->r_value." : ".'</b>'.$r->r_valuedesc; ?>
				<?php else: ?>
					<?php echo '<b>'.$r->r_choices." - ".$r->r_value." : ".'</b>'.$r->r_valuedesc; ?>
				<?php endif ?>
				
			</p></div>
			<?php endforeach; ?>

		</div><!-- panel-body -->
	</div><!-- panel-default -->	
</article><!-- legend -->

