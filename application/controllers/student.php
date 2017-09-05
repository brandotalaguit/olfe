<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student extends CI_Controller {

    protected $time_start = "06:00:00";
    protected $time_end = "22:00:00";
    private $now;

    function __construct()
    {
        parent::__construct();
        $this->now = date('Y-m-d');
        // load model
        $this->load->model('main_model');
        $this->main_model->update_logout_r();


        $uri = $this->uri->segment(2,0);
        if (($uri != 0 || $uri != 'index') && ($uri != 'validate_verification_code') )
        {

            //force logout
            $loginStatus = $this->db->get_where("tblstudfevallogin",array("StudNo"=>$this->session->userdata('StudNo')))->row();
            if ($loginStatus->IsLogin == 0)
            $this->logout();
        }

        // $this->output->enable_profiler(TRUE);
    }

    public function index()
    {
        if( (bool) $this->session->userdata('loggedin'))
        {
            redirect(base_url('student/current_load'));
        }

        if(isset($_POST['btn_login']))
        {
            // setup validation rules
            $rules = array(
                array('field' => 'Email','label' => 'Email Account','rules' => 'trim|xss_clean|required|callback__validate_student')
            );
            if (ENVIRONMENT == 'production')
            {
                $rules[] = array('field' => 'captcha','label' => 'Captcha','rules' => 'trim|xss_clean|required|callback__validate_captcha');
            }
            // add validation rules
            $this->form_validation->set_rules($rules);

            if ($this->form_validation->run())
            {
                $email_account = $this->input->post('Email');

                $this->db->where(array('Email'=>$email_account));
                // $this->db->where(array('StudNo'=>"K1124762"));
                $valid_email = $this->db->get('tblstudemailaccount')->row();

                $current_sysem = $this->main_model->get_current_sysem();
                $studevaluation = $this->db->where(array(
                                                    'SyId' => $current_sysem->SyId,
                                                    'SemId' => $current_sysem->SemId,
                                                    'StudNo' => $valid_email->StudNo,
                                                    'IsEvaluated' => 1
                                                ))->get('tblstudevaluation')->row();

                if(count($studevaluation))
                {
                    $this->session->set_flashdata('error', 'Unable to process your request, You have already finished the faculty evaluation.');
                    redirect('student','refresh');
                }

                $StudNo = $valid_email->StudNo;
                $Email = $valid_email->Email;

                $StudentName = $this->session->userdata('StudentName');
                $random_verification_code =  random_string('alnum', 8);

                $this->db->insert('tblstudverificationcode', array(
                                                        'StudNo' => $valid_email->StudNo,
                                                        'verificationcode' => $random_verification_code,
                                                        'SyId' => $current_sysem->SyId,
                                                        'SemId' => $current_sysem->SemId,
                                                        'created_at' => date('Y-m-d H:i:s'),
                                                    ));

                $this->main_model->add_studentlogs($StudNo, 'Send verification code to : '.$Email);

                $date = date('F j, Y H:i a');

                if(ENVIRONMENT == 'production')
                {
                    $message = "{$date}
                                <br><br>
                                Hi! {$StudentName},
                                <br><br>
                                Please click <a href='https://umak.edu.ph/olfe/student/validate_verification_code/{$random_verification_code}'>here</a> to start the Umak Online Faculty Evaluation
                                <br><br>
                                This is a computer generated email messages. Do no reply to this email.
                                <br><br>
                                Thank you.
                                <br><br>
                                <i>Data as of {$date}</i>";
                }
                else
                {
                    $message = "{$date}
                                <br><br>
                                Hi! {$StudentName},
                                <br><br>
                                Please click <a href='http://test.umak.edu.ph/olfe/student/validate_verification_code/{$random_verification_code}'>here</a> to start the Umak Online Faculty Evaluation
                                <br><br>
                                This is a computer generated email messages. Do no reply to this email.
                                <br><br>
                                Thank you.
                                <br><br>
                                <i>Data as of {$date}</i>";
                }


                $this->send_verification($Email, $message);
                $this->session->set_flashdata('message', 'We have already sent a verfication code to this email '. '<i>'.$Email.', please login your gmail account to start the faculty evaluation.</i>');
                $this->session->set_userdata('VerificationCode',$random_verification_code);

                redirect('student','refresh');
            }

        }

        $this->load->helper('captcha');

        $random_captcha =  strtoupper(random_string('alnum', 8));
        $actived_user = $this->main_model->count_login();
        $this->db->where('evaluationdate_id',5);
        $evaluation_period = $this->db->get('tblevaluationdate')->row();
        $IsClosed = FALSE;

        $date_start = date('F j, Y',strtotime($evaluation_period->date_start));
        $date_end = date('F j, Y',strtotime($evaluation_period->date_end));
        $date_now = $this->now;

        $time_start = $this->time_start;
        $time_end = $this->time_end;
        $time_now = date('G:i:s');

        $is_ongoing = $this->db->query("select * from tblevaluationdate where '{$date_now}' between date_start and date_end")->row();


        if( (! count($is_ongoing)) || (!(strtotime($time_now) > strtotime($time_start) && strtotime($time_now) < strtotime($time_end))))
            $IsClosed = TRUE;

        // setting up captcha config
        $vals = array(
                'word' => $random_captcha,
                'img_path' => './captcha/',
                'img_url' => base_url().'captcha/',
                'img_width' => 300,
                'img_height' => 50,
            );


        $data['captcha'] = create_captcha($vals);
        $data['time_start'] = $this->time_start;
        $data['time_end'] = $this->time_end;
        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        $data['IsClosed'] = $IsClosed;
        $data['actived_user'] = $actived_user;
        $this->session->set_userdata('captcha_word',$random_captcha);

        $this->load->view('student/student-login',$data);
    }

    public function _validate_captcha($str)
    {
        $word = $this->session->userdata('captcha_word');
        if($str == $word)
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('_validate_captcha', 'You have enter an incorrect captcha, please try again');
            return false;
        }
      }

    public function send_verification($email,$msg)
    {
        $this->db->where('sentcount < 490');
        $umak_email = $this->db->get('tblumakemailaccount')->row();
        $current_email = $umak_email->email;
        $current_pass = $umak_email->password;

        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => $current_email,
            'smtp_pass' => $current_pass,
            'mailtype'  => 'html',
            'charset'   => 'iso-8859-1'
        );

        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");

        // Set to, from, message, etc.
        $this->email->from('itc_support@umak.edu.ph', 'UMAK-ITC SUPPORT');
        $this->email->to($email);
        $this->email->subject('Activation Code');
        $this->email->message($msg);


        $result = $this->email->send();

        $this->db->query('update tblumakemailaccount set sentcount = sentcount+1 where umakemailaccount_id = '.$umak_email->umakemailaccount_id);

        // echo $this->email->print_debugger();
    }

    public function _validate_student()
    {
        $loggedin = FALSE;
        $Email = $this->input->post('Email', TRUE);

        $this->db->where('evaluationdate_id',5);
        $evaluation_period = $this->db->get('tblevaluationdate')->row();

        $current_date = date('Y-m-d');
        $date_start = date('F j, Y',strtotime($evaluation_period->date_start));
        $date_end = date('F j, Y',strtotime($evaluation_period->date_end));

        $time_start = $this->time_start;
        $time_end = $this->time_end;
        $time_now = date('G:i:s');

        $is_ongoing = $this->db->query("select * from tblevaluationdate where '{$current_date}' between date_start and date_end")->row();


        if(! count($is_ongoing))
        {
            $this->session->set_flashdata('error',"Evaluation Period will start on {$date_start} until {$date_end} only.");
            redirect('student','refresh');
        }

        if (!(strtotime($time_now) > strtotime($time_start) && strtotime($time_now) < strtotime($time_end)))
            redirect('student','refresh');


        // $this->db->where(array('Email'=>$Email));
        $this->db->where(array('Email'=>$Email));
        $valid_email = $this->db->get('tblstudemailaccount')->row();

        if( ! count($valid_email) )
        {
            $this->form_validation->set_message('_validate_student', 'Please enter a valid email address.');
            return FALSE;
        }

        $StudNo = $valid_email->StudNo;
        $sysem = $this->main_model->get_current_sysem();
        $result['type'] = $this->main_model->get_student_type($StudNo, $sysem->SyId, $sysem->SemId);

        //validate college if Cor is Printed and Hsu if had student enrollment
        if($result['type'] == FALSE)
        {
            $this->main_model->add_studentlogs($StudNo, 'Access is denied. No enrollment record found.');
            $this->form_validation->set_message('_validate_student', 'Access is denied. No enrollment record found.');
            return FALSE;
        }

        $is_barred = $this->main_model->is_barred_stud($StudNo, $result['type']);

        if(count($is_barred))
        {
            $this->main_model->add_studentlogs($StudNo, 'Access is denied, '.$is_barred->Remarks);
            $this->form_validation->set_message('_validate_student', $is_barred->Remarks);
            return FALSE;
        }



        /*NOTE FOR IMPROVEMENT :
            --> Validate if CMAT is printed
        */

        $result['SyId'] = $sysem->SyId;
        $result['SemId'] = $sysem->SemId;
        $result['SemCode'] = $sysem->SemCode;
        $result['SyDesc'] = $sysem->SyDesc;
        $result['StudNo'] = $valid_email->StudNo;
        $result['Email'] = $valid_email->Email;
        $result['StudentName'] = $valid_email->Lastname .', '. $valid_email->Firstname;
        $this->session->set_userdata($result);

        return TRUE;
    }


    public function validate_verification_code($verification_code)
    {
        $evaluation_period = $this->db->get('tblevaluationdate')->row();

        $current_date = date('Y-m-d');
        $date_start = date('F j, Y',strtotime($evaluation_period->date_start));
        $date_end = date('F j, Y',strtotime($evaluation_period->date_end));

        $Email = $this->session->userdata('Email');
        $StudNo = $this->session->userdata('StudNo');
        $VerificationCode = $this->session->userdata('VerificationCode');
        $current_sysem = $this->main_model->get_current_sysem();

        $no_login_student = $this->main_model->count_login();

        $time_start = $this->time_start;
        $time_end = $this->time_end;
        $time_now = date('G:i:s');

        if($no_login_student->cnt >= $evaluation_period->max_users)
        {
            $this->main_model->add_studentlogs($StudNo, "The Server is currently accomodating {$no_login_student->cnt} students Please try again later.");
            $this->session->set_flashdata('error', 'Database is updating, Please try again after 5 minutes.');
            redirect('student','refresh');
        }
        // var_dump($time_now);
        // var_dump($time_start);
        // var_dump($time_end);
        // die();
        if (!(strtotime($time_now) > strtotime($time_start) && strtotime($time_now) < strtotime($time_end)))
        {
            // var_dump($time_now);
            // die();
            $this->session->set_flashdata('error',"Evaluation Period will start on {$date_start} until {$date_end}  within ". date('h:i: A',strtotime($time_start))." to ".date('h:i: A',strtotime($time_end))."  only.");
            $this->session->unset_userdata('loggedin');
            redirect('student','refresh');
        }

        if($verification_code == $VerificationCode)
        {
            $this->db->where(array(
                        'SyId' => $current_sysem->SyId,
                        'SemId' => $current_sysem->SemId,
                        'StudNo' => $StudNo,
                    ));
            $has_started_evaluation = $this->db->get('tblstudevaluation')->row();

            if( ! count($has_started_evaluation))
            {
                $studeval = array(
                    'SyId' => $current_sysem->SyId,
                    'SemId' => $current_sysem->SemId,
                    'StudNo' => $StudNo,
                    'is_actived' => 1,
                    'Hold' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                );
                $this->db->insert('tblstudevaluation',$studeval);
            }
            $this->main_model->update_login($StudNo);
            $this->session->set_userdata('loggedin', TRUE);
            $this->session->set_flashdata('valid_email_code', TRUE);
            // $this->db->query('update tblevaluationdate set max_users = max_users+1');
            $this->main_model->add_studentlogs($StudNo, 'Student login success.');

            $data = array(
                        'StudNo' => $StudNo,
                        'timelogin' => date('Y-m-d H:i:s'),
                        'ipaddress' => $this->get_ipaddress(),
                        'created_at' => date('Y-m-d H:i:s'),
                    );

            $this->main_model->update_login($StudNo);

            // $studentlogin_id = $this->loginout('LOGIN', $data);

            // $this->session->set_userdata('studentlogin_id', $studentlogin_id);

            // $this->force_logout();

            redirect('student/current_load','refresh');
        }

        $result = $this->db->select('verificationcode')
                           ->where(array(
                            'SyId' => $current_sysem->SyId,
                            'SemId' => $current_sysem->SemId,
                            'StudNo' => $StudNo,
                         ))->get('tblstudverificationcode')->result();

        $verification_code_arr = array();

        foreach ($result as $row)
        {
            $verification_code_arr[] = $row->verificationcode;
        }

        if(in_array($verification_code, $verification_code_arr))
        {
            $this->main_model->add_studentlogs($StudNo, 'You have enter an old verification code, please request a new verification code by typing your email below.');
            $this->session->unset_userdata('loggedin');
            $this->session->set_flashdata('error', 'You have enter an old verification code, please request a new verification code by typing your email below.');
            redirect('student','refresh');
        }

        $this->session->set_flashdata('error', 'Invalid verification code, please request a new verification code by typing your email below.');
        $this->main_model->add_studentlogs($StudNo, 'Invalid verification code, please request a new verification code by typing your email below.');
        redirect('student','refresh');
    }

    public function force_logout()
    {
        $result = $this->db->where('isloggedin', 1)
                           ->get('tblstudentlogin')
                           ->result();

        foreach ($result as $row)
        {
            if(strtotime(date('Y-m-d H:i:s')) >= strtotime('+60 minutes', strtotime($row->timelogin)))
            {
                $this->db->where('studentlogin_id', $row->studentlogin_id);
                $this->db->update('tblstudentlogin',array('isloggedin'=>0,'timelogout'=>date('Y-m-d H:i:s')));
            }
        }
    }

    public function loginout($action, $data, $studentlogin_id = NULL)
    {
        $method = ($action == 'LOGIN') ? 'insert': 'update';

        if($method == 'update')
        {
            $this->db->where('studentlogin_id', $studentlogin_id);
        }

        $this->db->$method('tblstudentlogin', $data);

        return $studentlogin_id == NULL ? $this->db->insert_id(): $studentlogin_id;
    }

    public function get_ipaddress()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    public function current_load()
    {

        $studentlogin_id = $this->session->userdata('studentlogin_id');

        if( !(bool) $this->session->userdata('loggedin'))
        {
            $this->session->set_flashdata('error', 'User credential not found, Please login.');
            redirect(base_url('student'));
        }

        // $data['show_clock'] = TRUE;
        $current_sysem = $this->main_model->get_current_sysem();
        $StudNo = $this->session->userdata('StudNo');

        $student_type = $this->session->userdata('sess_student_type');


        $StudNo = $this->session->userdata('StudNo');
        $student_type = $this->session->userdata('type');

        $data['student'] = $this->main_model->get_student_information($StudNo, $student_type);
        $data['student_load'] = $this->main_model->get_student_currload($StudNo, $student_type);
        $data['incomplete_evaluation'] = $this->main_model->get_incomplete_evaluation($StudNo);
        $data['course_evaluated'] = $this->main_model->get_course_evaluated($StudNo);
        $data['cmat'] = $this->main_model->with_pending_cmat($StudNo);

        // load the view
        $this->load->view('student/student-load',$data);
    }

    public function evaluate($sched_id = NULL)
    {
        $studentlogin_id = $this->session->userdata('studentlogin_id');

        if( !(bool) $this->session->userdata('loggedin'))
        {
            $this->session->set_flashdata('error', 'User credential not found, Please login.');
            redirect(base_url('student'));
        }

        $data['show_clock'] = TRUE;
        $current_sysem = $this->main_model->get_current_sysem();
        $StudNo = $this->session->userdata("StudNo");
        $sched_id = intval($sched_id);
        $sched = $this->main_model->get_schedule($sched_id, TRUE);

        if (empty($sched))
        {
            $this->session->set_flashdata('error', "Unable to process request, Subject does not exists.");
            redirect(base_url('student/current_load'));
        }

        if (!empty($sched->remark))
        {
            if ($sched->remark == 'DISSOLVED')
            {
                $this->session->set_flashdata('error', "You cannot evaluate this subject due to <b>dissolved status</b>.");
                redirect(base_url('student/current_load'));
            }
        }


        $cfn = $sched->cfn;
        $faculty_id = $sched->faculty_id;

        $stud_timer = $this->db->where(array(
                                    'StudNo' => $StudNo,
                                    'SyId' => $current_sysem->SyId,
                                    'SemId' => $current_sysem->SemId,
                                ))->get('tblstudevaltimer')->row();

        if(count($stud_timer))
        {
            if( strtotime($stud_timer->remaining_time) <= strtotime('00:00:00') )
            {
                $this->main_model->add_studentlogs($StudNo, 'Unable to process your request, You have reach the maximum time of evaluation please proceed to TASC office for instruction.');
                $this->session->set_flashdata('error', 'Unable to process your request, You have reach the maximum time of evaluation please proceed to TASC office for instruction.');
                redirect('student/current_load','refresh');
            }
            else
            {
                $remaining = $stud_timer->remaining_time;
                $data['remaining_time'] = (intval(date('i',strtotime($remaining))) * 60) + intval(date('s',strtotime($remaining)));
            }
        }
        else
        {
            $data = array(
                'remaining_time' => '00:45:00',
                'StudNo' => $StudNo,
                'SyId' => $current_sysem->SyId,
                'SemId' => $current_sysem->SemId,
                'created_at'=>date('Y-m-d H:i:s'),
            );
            $this->db->insert('tblstudevaltimer',$data);

            redirect("student/evaluate/{$sched_id}",'refresh');
        }

        $course_evaluated = $this->main_model->get_course_evaluated($StudNo);


        if(in_array($cfn, $course_evaluated))
        {
            $this->session->set_flashdata('error', 'Unable to process your request. You have already finished evaluating this subject.');
            $this->main_model->add_studentlogs($StudNo, 'Unable to process your request. You have already finished evaluating this subject.');
            return redirect('student/current_load','refresh');
        }

        if ( ! count($sched) || $sched_id == NULL)
        {
            $this->session->set_flashdata('error', 'Unable to process your request. The schedule you\'re accessing does not exist.');
            $this->main_model->add_studentlogs($StudNo, 'Unable to process your request. The schedule you\'re accessing does not exist.');
            return redirect('student/current_load','refresh');
        }

        $studsched = $this->main_model->get_studsched($cfn, $StudNo);
        if ( ! count($studsched))
        {
            $this->session->set_flashdata('error', 'Unable to process your request. The schedule you\'re accessing is not included in your COR(Certificate of Registration).');
            $this->main_model->add_studentlogs($StudNo, 'Unable to process your request. The schedule you\'re accessing is not included in your COR(Certificate of Registration).');
            return redirect('student/current_load','refresh');
        }

        $course_evaluated = $this->db->where(array(
                                                'StudNo' => $StudNo,
                                                'cfn' => $cfn,
                                                'SyId' => $current_sysem->SyId,
                                                'SemId' => $current_sysem->SemId,
                                            ))->get('tblstudcourseeval')->row();

        if(!count($course_evaluated))
        {
            $course_eval = array(
                'StudNo' => $StudNo,
                'cfn' => $cfn,
                'SyId' => $current_sysem->SyId,
                'SemId' => $current_sysem->SemId,
                'created_at' => date('Y-m-d H:i:s'),
            );
            $this->db->insert('tblstudcourseeval',$course_eval);
        }

        $category = $this->main_model->get_category();

        if(isset($_POST['btn_proceed_feedback']))
        {
            /*for ($i=0; $i < count($_POST['question_id']); $i++)
            {
                if( ! isset($_POST['rating_'.$_POST['question_id'][$i]]))
                {
                    $this->session->set_flashdata('error', 'Please answer all question.');
                    redirect('student/evaluate/'.$sched_id,'refresh');
                    break;
                }
            }*/
            foreach ($_POST['stud_questionnaire'] as $key => $value)
            {
                $this->form_validation->set_rules('stud_questionnaire['.$key.'][stud_rating]', 'Rating', 'trim|required|min_length[1]|max_length[1]');
            }
            $form_validation = $this->form_validation->run();

            if ($form_validation == FALSE)
            {
                $this->session->set_flashdata('error', 'Please answer all question.');
                redirect('student/evaluate/'.$sched_id, 'refresh');
            }

            $data['student_questionnaire'] = $this->main_model->get_student_questionnaire(
                                                $cfn,
                                                $current_sysem->SyId,
                                                $current_sysem->SemId,
                                                $StudNo,
                                                $faculty_id
                                            );

            if ( empty($data['student_questionnaire']))
            {
                $this->main_model->batch_save($_POST);
                return redirect("student/feedback/{$sched_id}",'refresh');
            }

            $this->main_model->batch_save($_POST, 'studans_id');
            return redirect("student/feedback/{$sched_id}",'refresh');


/*
            for ($i=0; $i < count($_POST['question_id']); $i++)
            {
                $current_studanswer = $this->main_model->get_studanswer($StudNo, $cfn, $_POST['question_id'][$i]);

                $studanswer['studno'] = $StudNo;
                $studanswer['question_id'] = $_POST['question_id'][$i];
                $studanswer['category_id'] = $_POST['current_category'][$i];
                $studanswer['cfn'] = $cfn;
                $studanswer['SyId'] = $this->session->userdata('SyId');
                $studanswer['SemId'] = $this->session->userdata('SemId');
                $studanswer['prof_id'] = $faculty_id;
                $studanswer['stud_rating'] = $_POST['rating_'.$_POST['question_id'][$i]];
                $studanswer['date_fill'] = date('Y-m-d h:i:s');

                if(count($current_studanswer))
                {
                    $this->db->where(array('cfn'=>$cfn,'studno'=>$StudNo,'question_id'=>$_POST['question_id'][$i]));
                    $this->db->update('tblstudanswer',$studanswer);
                }
                else
                {
                    $this->db->insert('tblstudanswer',$studanswer);
                }
            }*/

        }

        // fetch data
        $data['profinfo'] = $this->main_model->get_profinfo($cfn, $faculty_id);
        $data['rating_scale'] = $this->main_model->get_ratingscale();

        $data['courseeval_count'] = count($this->main_model->get_course_evaluated($StudNo));
        $data['current_category'] = 0;
        $data['question'] = $this->main_model->get_question();
        // $data['studanswer'] = $this->main_model->get_studanswer($StudNo, $cfn);

        $data['student_questionnaire'] = $this->main_model->get_student_questionnaire(
                                            $cfn,
                                            $current_sysem->SyId,
                                            $current_sysem->SemId,
                                            $StudNo,
                                            $faculty_id
                                        );

        $data['isnewdata'] = TRUE;
        if ( empty($data['student_questionnaire']))
        {
            $data['student_questionnaire'] = $this->main_model->new_student_questionnaire(
                                            $cfn,
                                            $current_sysem->SyId,
                                            $current_sysem->SemId,
                                            $StudNo,
                                            $faculty_id
                                        );
            $data['isnewdata'] = FALSE;
        }


        return $this->load->view("student/eval-first-page",$data);
    }

    public function feedback($sched_id)
    {
        if( !(bool) $this->session->userdata('loggedin'))
        {
            $this->session->set_flashdata('error', 'User credential not found, Please login.');
            redirect(base_url('student'));
        }

        $data['show_clock'] = TRUE;
        $StudNo = $this->session->userdata("StudNo");
        $sched_id = intval($sched_id);
        $sched = $this->main_model->get_schedule($sched_id, TRUE);
        $cfn = $sched->cfn;
        $faculty_id = $sched->faculty_id;
        $curr_sy = $this->session->userdata('SyId');
        $curr_sem = $this->session->userdata('SemId');

        $stud_timer = $this->db->where(array(
                                    'StudNo' => $StudNo,
                                    'SyId' => $curr_sy,
                                    'SemId' => $curr_sem,
                                ))->get('tblstudevaltimer')->row();

        if(count($stud_timer))
        {
            if( strtotime($stud_timer->remaining_time) <= strtotime('00:00:00') )
            {
                $this->main_model->add_studentlogs($StudNo, 'Unable to process your request, You have reach the maximum time of evaluation please proceed to TASC office for instruction.');
                $this->session->set_flashdata('error', 'Unable to process your request, You have reach the maximum time of evaluation please proceed to TASC office for instruction.');
                redirect('student/current_load','refresh');
            }
            else
            {
                $remaining = $stud_timer->remaining_time;
                $data['remaining_time'] = (intval(date('i',strtotime($remaining))) * 60) + intval(date('s',strtotime($remaining)));
            }
        }
        else
        {
            $data = array(
                'remaining_time' => '00:45:00',
                'StudNo' => $StudNo,
                'SyId' => $curr_sy,
                'SemId' => $curr_sem,
                'created_at'=>date('Y-m-d H:i:s'),
            );
            $this->db->insert('tblstudevaltimer',$data);

            redirect('student/current_load','refresh');
        }

        $course_evaluated = $this->main_model->get_course_evaluated($StudNo);


        if(in_array($cfn, $course_evaluated))
        {
            $this->session->set_flashdata('error', 'Unable to process your request. You have already finished evaluating this subject.');
            return redirect('student/current_load','refresh');
        }

        if(isset($_POST['btnsubmit']))
        {

            $condition = array(
                                'StudNo' => $StudNo,
                                'cfn' => $cfn,
                                'SyId' => $curr_sy,
                                'SemId' => $curr_sem,
                           );

            $this->db->where($condition)->update('tblstudcourseeval',array('is_complete'=>1));

            $studcourseeval_id = $this->db->where($condition)->get('tblstudcourseeval')->row()->studcourseeval_id;

            $this->main_model->add_studentlogs($StudNo, 'Finish evaluation course with studcourseeval_id = '.$studcourseeval_id);

            if( ! empty($_POST['studfeedback']) )
            {
                $feedback = array(
                    'studcourseeval_id' => $studcourseeval_id,
                    'feedback' => $this->input->post('studfeedback'),
                );
                $this->db->insert('tblfeedback',$feedback);
            }

            $is_done_evaluation = $this->main_model->is_done_evaluation($StudNo);
            if($is_done_evaluation)
            {
                $studeval = array(
                    'IsEvaluated' => 1,
                    'DateAccomplished' => date('Y-m-d H:i:s'),
                    'Hold' => 0,
                );

                $this->db->where(array(
                        'SyId' => $curr_sy,
                        'SemId' => $curr_sem,
                        'StudNo' => $StudNo,
                    ));
                $this->db->update('tblstudevaluation',$studeval);
                $this->logout('You have successfully finished the evaluation');
                $this->main_model->add_studentlogs($StudNo, 'Finish evaluation.');
            }

            $this->session->set_flashdata('message','Subject has been successfully evaluted.');
            redirect('student/current_load','refresh');
        }

        if ( ! count($sched))
        {
            $this->session->set_flashdata('error', 'Unable to process your request. The schedule you\'re accessing does not exist.');
            return redirect('student/current_load','refresh');
        }


        $studsched = $this->main_model->get_studsched($cfn, $StudNo);
        if ( ! count($studsched))
        {
            $this->session->set_flashdata('error', 'Unable to process your request. The schedule you\'re accessing is not included in your COR(Certificate of Registration).');
            return redirect('student/current_load','refresh');
        }

        // fetch data
        $data['profinfo'] = $this->main_model->get_profinfo($cfn, $faculty_id);
        $data['evaluation_summary'] = $this->main_model->get_evaluation_summary($cfn, $StudNo);

        $this->load->view("student/eval-feedback",$data);
    }

    public function update_remaining_time()
    {
        $current_sysem = $this->main_model->get_current_sysem();
        $StudNo = $this->session->userdata('StudNo');
        $remaining_time = gmdate("H:i:s", $this->input->post('remaining_time'));

        $this->db->where(array(
                            'StudNo' => $StudNo,
                            'SyId' => $current_sysem->SyId,
                            'SemId' => $current_sysem->SemId,
                    ))->update('tblstudevaltimer',array('remaining_time'=>$remaining_time));

        if($this->input->post('remaining_time') <= 0)
        {
            $this->session->set_flashdata('error', 'You have reach the time limit of evaluation, please proceed to TASC office for instruction.');
            echo 'RELOAD';
            return FALSE;
        }
    }

    public function logout($message = 'You have successfully logout.')
    {
        if(isset($_POST['reason']))
        {
            $this->form_validation->set_rules('reason', 'Reason', 'trim|required|xss_clean');

            if ($this->form_validation->run())
            {
                $current_sysem = $this->main_model->get_current_sysem();
                $StudNo = $this->session->userdata('StudNo');
                $data = array(
                    'reason' => $_POST['reason'],
                    'StudNo' => $StudNo,
                    'SyId' => $current_sysem->SyId,
                    'SemId' => $current_sysem->SemId,
                    'created_at' => date('Y-m-d H:i:s'),
                );

                $this->db->insert('tblincevalreason',$data);
            }
            else
            {
                $this->session->set_flashdata('error', 'Please input your reason to logout.');
                redirect('student/current_load','refresh');
            }
        }

        $this->main_model->add_studentlogs($this->session->userdata('StudNo'), 'Logout student');
        $this->main_model->update_logout_r($this->session->userdata('StudNo'));
        // $this->db->query('update tblevaluationdate set max_users = max_users-1');
        $this->session->set_flashdata('message', $message);

        $data = array(
                'timelogout' => date('Y-m-d H:i:s'),
                'isloggedin' => 0,
            );

        $this->loginout('LOGOUT' ,$data, $this->session->userdata('studentlogin_id'));

        $this->session->unset_userdata('loggedin');
        $this->session->unset_userdata('studentlogin_id');
        redirect("student",'refresh');
    }

    public function logout_modal()
    {
        $StudNo = $this->session->userdata('StudNo');
        $data['studload_count'] = $this->session->userdata('studload_count');
        $data['studcourseeval_count'] = count($this->main_model->get_course_evaluated($StudNo));
        $this->load->view('student/logout_modal.php',$data);
    }

}
