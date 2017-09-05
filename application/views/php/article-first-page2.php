<?php echo form_open(NULL); ?>
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
                            <?php $current_category = 0; ?>
                            <?php $count = 0; foreach($student_questionnaire as $r): ?>
                            <?php if($current_category == 0 || $current_category != $r->category_id): ?>
                            <?php $current_category = $r->category_id; ?>
                            <tr><th colspan="10"><?php echo $r->category_desc ." - (" .$r->category_percentage. ")"; ?></th></tr>
                            <?php endif; ?>
                            <tr>
                                <input type="hidden" name="stud_questionnaire[<?php echo $count ?>][studans_id]" value="<?php echo $r->studans_id; ?>">
                                <input type="hidden" name="stud_questionnaire[<?php echo $count ?>][date_fill]" value="<?php echo date('Y-m-d H:i:s'); ?>">

                                <!-- <?php /* ?> do not include question and category id to batch update <?php */ ?> -->
                                <?php if ($isnewdata === FALSE): ?>
                                <input type="hidden" name="stud_questionnaire[<?php echo $count ?>][studno]" value="<?php echo $r->studno; ?>">
                                <input type="hidden" name="stud_questionnaire[<?php echo $count ?>][cfn]" value="<?php echo $r->cfn; ?>">
                                <input type="hidden" name="stud_questionnaire[<?php echo $count ?>][SyId]" value="<?php echo $r->SyId; ?>">
                                <input type="hidden" name="stud_questionnaire[<?php echo $count ?>][SemId]" value="<?php echo $r->SemId; ?>">
                                <input type="hidden" name="stud_questionnaire[<?php echo $count ?>][prof_id]" value="<?php echo $r->prof_id; ?>">
                                <input type="hidden" name="stud_questionnaire[<?php echo $count ?>][question_id]" value="<?php echo $r->question_id; ?>">
                                <input type="hidden" name="stud_questionnaire[<?php echo $count ?>][category_id]" class="current_category" value="<?php echo $r->category_id; ?>">
                                <?php endif ?>

                                <td><?php echo $r->Question; ?></td>
                                <td>
                                    <input type="radio" name="stud_questionnaire[<?php echo $count ?>][stud_rating]" value="0"  
                                    <?php $r->stud_rating === '0' ? $check = TRUE : $check = FALSE; echo set_radio("stud_questionnaire[$count][stud_rating]", 0, $check);?> >
                                </td>
                                <td>
                                    <input type="radio" name="stud_questionnaire[<?php echo $count ?>][stud_rating]" value="1"
                                    <?php $r->stud_rating === '1' ? $check = TRUE : $check = FALSE; echo set_radio("stud_questionnaire[$count][stud_rating]", 1, $check);?> >
                                </td>
                                <td>
                                    <input type="radio" name="stud_questionnaire[<?php echo $count ?>][stud_rating]" value="2"
                                    <?php $r->stud_rating === '2' ? $check = TRUE : $check = FALSE; echo set_radio("stud_questionnaire[$count][stud_rating]", 2, $check);?> >
                                </td>
                                <td>
                                    <input type="radio" name="stud_questionnaire[<?php echo $count ?>][stud_rating]" value="3"
                                    <?php $r->stud_rating === '3' ? $check = TRUE : $check = FALSE; echo set_radio("stud_questionnaire[$count][stud_rating]", 3, $check);?> >
                                </td>
                                <td>
                                    <input type="radio" name="stud_questionnaire[<?php echo $count ?>][stud_rating]" value="4"
                                    <?php $r->stud_rating === '4' ? $check = TRUE : $check = FALSE; echo set_radio("stud_questionnaire[$count][stud_rating]", 4, $check);?> >
                                </td>
                                <td>
                                    <input type="radio" name="stud_questionnaire[<?php echo $count ?>][stud_rating]" value="5"
                                    <?php $r->stud_rating === '5' ? $check = TRUE : $check = FALSE; echo set_radio("stud_questionnaire[$count][stud_rating]", 5, $check);?> >
                                </td>
                            </tr>
                            <?php $count++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table><!-- table -->
                </div><!-- table-responsive -->
            </div><!-- panel-body -->
        </div>

        <div class="panel-default">
            <div class="panel-body">
                <div class="col-sm-2 pull-right">
                    <div class="col-sm-6">
                        <!-- <button type="button" class="btn btn-primary btn_previous" name="btn_previous">&laquo; Back</button> -->
                        <a href="<?php echo base_url('student/current_load'); ?>" class="btn btn-primary btn_previous">&laquo; Back</a>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-primary btn_proceed_feedback" id="btn_proceed_feedback" name="btn_proceed_feedback">Next &raquo;</button>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- panel-default -->
</article><!-- first-page -->
<?php echo form_close(); ?>


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


