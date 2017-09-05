<article class="first-page">


    <?php if($this->session->flashdata('error') != NULL):  ?>
    <div class="alert alert-danger">
        <?php echo "<h4> <i class='fa fa-warning'></i> NOTIFICATION </h4>" . $this->session->flashdata('error'); ?>
    </div>
    <?php endif; ?>

    <div class="message">
        
    </div>

    <div class="panel panel-default" style="border:0;">
        <div id="ajax-result">
            <div class="panel-heading" style="background-color:#333;color:#fff;">
            <?php 
                foreach($question as $r){
                    $catdesc = $r->category_desc;
                    $catpercent = $r->category_percentage;
                }
            ?>

            <?php echo $catdesc." - (".$catpercent."%)"; ?>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2">Characteristics</th>
                                    <th colspan="6">Rating</th>
                            </tr>
                            <tr>
                                <th>X</th>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 0; foreach($question as $r): ?>
                            <tr>
                                <input type="hidden" name="question_id[]" class="form-control" value="<?php echo $r->QuestionId; ?>">
                                <td><?php echo $r->Question; ?></td>
                                <td>
                                    <input type="radio" name="<?php echo 'rating_'. $r->QuestionId; ?>" id="<?php echo $r->QuestionId; ?>" value="0"  
                                    <?php 
                                        if(array_key_exists($r->QuestionId, $studanswer)) 
                                        {
                                            if($studanswer[$r->QuestionId] == '0'){echo 'checked';}
                                        }
                                    ?>>
                                </td>
                                <td>
                                    <input type="radio" name="<?php echo 'rating_'. $r->QuestionId; ?>" id="<?php echo $r->QuestionId; ?>" value="1"
                                    <?php 
                                        if(array_key_exists($r->QuestionId, $studanswer)) 
                                        {
                                            if($studanswer[$r->QuestionId] == '1'){echo 'checked';}
                                        }
                                    ?>>
                                </td>
                                <td>
                                    <input type="radio" name="<?php echo 'rating_'. $r->QuestionId; ?>" id="<?php echo $r->QuestionId; ?>" value="2"
                                    <?php 
                                        if(array_key_exists($r->QuestionId, $studanswer)) 
                                        {
                                            if($studanswer[$r->QuestionId] == '2'){echo 'checked';}
                                        }
                                    ?>>
                                </td>
                                <td>
                                    <input type="radio" name="<?php echo 'rating_'. $r->QuestionId; ?>" id="<?php echo $r->QuestionId; ?>" value="3"
                                    <?php 
                                        if(array_key_exists($r->QuestionId, $studanswer)) 
                                        {
                                            if($studanswer[$r->QuestionId] == '3'){echo 'checked';}
                                        }
                                    ?>>
                                </td>
                                <td>
                                    <input type="radio" name="<?php echo 'rating_'. $r->QuestionId; ?>" id="<?php echo $r->QuestionId; ?>" value="4"
                                    <?php 
                                        if(array_key_exists($r->QuestionId, $studanswer)) 
                                        {
                                            if($studanswer[$r->QuestionId] == '4'){echo 'checked';}
                                        }
                                    ?>>
                                </td>
                                <td>
                                    <input type="radio" name="<?php echo 'rating_'. $r->QuestionId; ?>" id="<?php echo $r->QuestionId; ?>" value="5"
                                    <?php 
                                        if(array_key_exists($r->QuestionId, $studanswer)) 
                                        {
                                            if($studanswer[$r->QuestionId] == '5'){echo 'checked';}
                                        }
                                    ?>>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <input type="hidden" name="current_category" class="current_category" value="<?php echo $current_category; ?>">
                        </tbody>
                    </table><!-- table -->
                </div><!-- table-responsive -->
            </div><!-- panel-body -->
        </div>

        <div class="panel-default">
            <div class="panel-body">
                <div class="col-sm-2 pull-right">
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-primary btn_previous" name="btn_previous">&laquo; Back</button>
                    </div>
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-primary btn_next" name="btn_next">Next &raquo;</button>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- panel-default -->
</article><!-- first-page -->


<div class="modal fade timer_reminder_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3>You are about to go back to your current load, please be reminded that you have <code class="remaining_time"></code> time to finish your evaluation.</h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn_confirm_remaining_time" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
