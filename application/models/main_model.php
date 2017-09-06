<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_Model extends CI_Model {

	public $integrated = '';
	public $hsu = '';
	public $current_sysem = 'IsCurrentSem';
	protected $allowed_time = 45;

	function __construct() {
        parent::__construct();

        $this->integrated = (ENVIRONMENT == 'production') ? 'umakunil_integrated' : 'umaktest_integrated';
        $this->hsu = (ENVIRONMENT == 'production') ? 'umakunil_hsu_enrollment' : 'umaktest_hsu_enrollment';

    }

	/* updates */
	public function get_current_sysem()
	{
		$this->db->join($this->integrated . '.tblsem as x', 'tblsysem.SemId = x.SemId', 'left');
		$this->db->join($this->integrated . '.tblsy as y', 'tblsysem.SyId = y.SyId', 'left');
		return $this->db->where($this->current_sysem,1)
				  ->get($this->integrated. '.tblsysem')->row();
	}

	public function get_type()
	{
		return $this->session->userdata('type');
	}

	public function get_schedule($sched_id = NULL, $single = FALSE)
	{
		$method = $single ? 'row' : 'result';
		$type = $this->get_type();

		$table = $this->integrated .'.tblsched';
		$column = array(
			'faculty_id',
			'cfn',
			'remark',
		);

		$param = array(
			$table . '.SyId' => $this->session->userdata('SyId'),
			$table . '.SemId' => $this->session->userdata('SemId'),
			$table . '.sched_id' => $sched_id,
		);

		if ($type == 'HSU')
		{
			$table = $this->hsu . '.schedules';
			$column = array(
				'prof_id as faculty_id',
				'CFN as cfn',
			);

			$param = array(
				$table . '.SyId' => $this->session->userdata('SyId'),
				$table . '.SemId' => $this->session->userdata('SemId'),
				$table . '.sched_id' => $sched_id,
			);
		}

		$this->db->where($param);
		$this->db->select($column, FALSE);

		return $this->db->get($table)->$method();
	}

	public function get_studsched($cfn, $StudNo)
	{
		$type = $this->get_type();
		$table = $this->integrated . '.tblstudentschedule';
		$where = array('Cfn' => $cfn, 'StudNo' => $StudNo);

		if ($type == 'HSU')
		{
			$table = $this->hsu . '.student_schedules';
			$where = array('CFN' => $cfn, 'student_id' => $StudNo);
		}

		return $this->db->where($where)->get($table)->row();
	}


	public function get_student_type($StudNo, $SyId, $SemId)
	{
		$college = $this->db->where(array(
			'SyId' => $SyId,
			'SemId' => $SemId,
			'StudNo' => $StudNo,
			'IsPrinted' => 1,
		))->get($this->integrated. '.tblenrollmenttrans')->row();

		if(count($college))
		{
			return 'COLLEGE';
		}

		$hsu = $this->db->where(array(
			'sy_id' => $SyId,
			'sem_id' => $SemId,
			'stud_id' => $StudNo,
		))->get($this->hsu. '.student_enrollments')->row();

		if(count($hsu))
		{
			return 'HSU';
		}

		return FALSE;
	}

	public function get_student_information($StudNo,$student_type)
	{
		$curr_sy = $this->session->userdata('SyId');
		$curr_sem = $this->session->userdata('SemId');

		if($student_type == 'COLLEGE')
		{
			$select_arr = array(
				'college.CollegeDesc',
				'college.CollegeCode',
				'major.MajorDesc',
				'program.ProgramDesc',
				'studinfo.StudNo',
				'studinfo.Lname',
				'studinfo.Fname',
				'studinfo.Mname',
				'studinfo.YrLevel',
			);

			$where_arr = array(
				'enrolltrans.SyId' => $curr_sy,
				'enrolltrans.SemId' => $curr_sem,
				'enrolltrans.StudNo' => $StudNo,
				'enrolltrans.IsPrinted' => 1,
			);

			return $this->db->select($select_arr)
							   ->where($where_arr)
							   ->join($this->integrated. '.tblstudinfo as studinfo','studinfo.StudNo = enrolltrans.StudNo','LEFT')
						       ->join($this->integrated. '.tblstudcurriculum as studcurriculum','studcurriculum.StudNo = enrolltrans.StudNo','LEFT')
						       ->join($this->integrated. '.tblcurriculum as curriculum','curriculum.CurriculumId = studcurriculum.CurriculumId','LEFT')
						       ->join($this->integrated. '.tblcollege as college','college.CollegeId = curriculum.CollegeId','LEFT')
						       ->join($this->integrated. '.tblprogram as program','program.ProgramId = curriculum.ProgramId','LEFT')
						       ->join($this->integrated. '.tblmajor as major','major.MajorId = curriculum.MajorId','LEFT')
						  	   ->get($this->integrated. '.tblenrollmenttrans as enrolltrans')->row();


		}

		if($student_type == 'HSU')
		{
			$select_arr = array(
				'"HIGHER SCHOOL NG UMAK" as CollegeDesc',
				'"HSU" as CollegeCode',
				'"" as MajorDesc',
				'"K TO 12" as ProgramDesc',
				'studinfo.stud_id as StudNo',
				'studinfo.family_name as Lname',
				'studinfo.first_name as Fname',
				'studinfo.middle_name as Mname',
				// 'studinfo.yr_level as YrLevel',
				'enrolltrans.yr_level as YrLevel',
				'enrolltrans.yr_level as year_section',
			);

			$condition = array(
				'enrolltrans.sy_id' => $curr_sy,
				'enrolltrans.sem_id' => $curr_sem,
				'enrolltrans.stud_id' => $StudNo,
			);

			$this->db->_protect_identifiers = FALSE;
			return $this->db->select($select_arr, FALSE)
							  ->join($this->hsu .'.examinees as studinfo','studinfo.stud_id = "'. $StudNo . '" AND studinfo.is_actived = 1', 'LEFT')
							  ->join($this->hsu .".tblsysem as sysem","sysem.SyId = enrolltrans.sy_id AND sysem.SemId = enrolltrans.sem_id", "LEFT")
							  ->where($condition)
							  ->get($this->hsu .'.student_enrollments as enrolltrans')->row();
		}


		return FALSE;
	}

	public function get_student_currload($StudNo, $student_type)
	{
		$curr_sy = $this->session->userdata('SyId');
		$curr_sem = $this->session->userdata('SemId');

		if($student_type == 'COLLEGE')
		{
			$condition = array(
				'sched.isExcluded' => 0,
				'studsched.IsActive' => 1,
				'enrolltrans.StudNo' => $StudNo,
				'enrolltrans.SemId' => $curr_sem,
				'enrolltrans.SyId' => $curr_sy,
				'IsPrinted' => 1,
			);

			$select_arr = array(
				'faculty.faculty_id',
				'CONCAT(faculty.Lastname,", ",faculty.Firstname," ",faculty.Middlename) as faculty_name',
				'studsched.*',
				'IF(studexcluded_id IS NULL,"0","1") as is_excluded',
				'sched.sched_id',
				'sched.cfn',
				'sched.remark',
				'year_section',
				'CourseCode',
				'CourseDesc',
				'course.Units',
			);
						      // ->join($this->integrated .'.tblstudentschedule as studsched', 'studsched.StudNo = enrolltrans.StudNo and studsched.SyId = enrolltrans.SyId and studsched.SemId = enrolltrans.SemId', 'LEFT')

			return $this->db->select($select_arr)
						      ->join($this->integrated .'.tblstudentschedule as studsched', "studsched.SyId = enrolltrans.SyId and studsched.SemId = enrolltrans.SemId and studsched.StudNo = '{$StudNo}'", 'LEFT')
						      ->join($this->integrated .'.tblsched as sched', 'sched.cfn = studsched.Cfn', 'LEFT')
						      ->join($this->integrated .'.tblfacultydisplay as faculty', 'faculty.faculty_id = sched.faculty_id', 'LEFT')
						      ->join($this->integrated .'.tblcourse as course', 'course.CourseId = sched.subject_id', 'LEFT')
						      ->join('tblstudexcluded as studexclude', 'studexclude.cfn = studsched.Cfn and studexclude.StudNo = studsched.StudNo and studexclude.is_actived = 1', 'LEFT')
						      ->where($condition)
						      // ->where("remark != 'DISSOLVED'")
						      ->get($this->integrated .'.tblenrollmenttrans as enrolltrans')->result();
		}

		if($student_type == 'HSU')
		{
			$select_arr = array(
				'faculty.faculty_id',
				'CONCAT(faculty.Lastname,", ",faculty.Firstname," ",faculty.Middlename) as faculty_name',
				'studsched.*',
				'IF(studexcluded_id IS NULL,"0","1") as is_excluded',
				'sched.CFN as cfn',
				'sched.remark',
				'sched.subcode as CourseCode',
				'sched.subdes as CourseDesc',
				'sched.sched_id',
				'enrolltrans.yr_level as year_section',
			);

			$condition = array(
				'enrolltrans.sy_id' => $curr_sy,
				'enrolltrans.sem_id' => $curr_sem,
				'enrolltrans.stud_id' => $StudNo,
				'sched.isExcluded' => 0,
				'sched.is_actived' => 1,
				'studsched.is_actived' => 1,
			);

							  // ->join($this->hsu .'.student_schedules as studsched','enrolltrans.stud_id = studsched.student_id AND studsched.SemId = ' .  $curr_sem . ' AND studsched.SyId = ' . $curr_sy, 'LEFT')
			return $this->db->select($select_arr, FALSE)
							  ->join($this->hsu .'.student_schedules as studsched',
							  		"studsched.SyId = enrolltrans.sy_id AND studsched.SemId = enrolltrans.sem_id AND studsched.student_id = '{$StudNo}'", 'LEFT')
							  ->join($this->hsu .".schedules as sched", "sched.CFN = studsched.CFN", "LEFT")
							  ->join($this->integrated .'.tblfacultydisplay as faculty','sched.prof_id = faculty.faculty_id','LEFT')
							  ->join($this->hsu .".tblsysem as sysem","sysem.SyId = sched.SyId AND sysem.SemId = sched.SemId", "LEFT")
						      ->join('tblstudexcluded as studexclude','studexclude.cfn = studsched.CFN and studexclude.StudNo = studsched.student_id and studexclude.is_actived = 1','LEFT')
							  ->where($condition)
							  ->get($this->hsu .'.student_enrollments as enrolltrans')->result();
		}
	}

	public function get_profinfo($cfn, $faculty_id)
	{
		$type = $this->get_type();

		if($type == 'COLLEGE')
		{
			$where_arr = array(
				'sched.cfn' => $cfn,
				'faculty.faculty_id' => $faculty_id,
			);

			$select_arr = array(
				'CONCAT(faculty.Lastname,", ",faculty.Firstname," ",faculty.Middlename) as faculty_name',
				'faculty.faculty_id',
				'sched.cfn',
				'course.CourseCode',
				'course.CourseDesc',
			);

			return $this->db->where($where_arr)
							->select($select_arr)
							->join($this->integrated .'.tblcourse as course','course.CourseId = sched.subject_id','LEFT')
							->join($this->integrated .'.tblfacultydisplay as faculty','faculty.faculty_id = sched.faculty_id','LEFT')
					 	    ->get($this->integrated .'.tblsched as sched')->row();
		}

		if($type == 'HSU')
		{
			$where_arr = array(
				'sched.cfn' => $cfn,
				'faculty.faculty_id' => $faculty_id,
			);

			$select_arr = array(
				'CONCAT(faculty.Lastname,", ",faculty.Firstname," ",faculty.Middlename) as faculty_name',
				'faculty.faculty_id',
				'sched.nametable as cfn',
				'sched.subcode as CourseCode',
				'sched.subdes as CourseDesc',
			);

			return $this->db->where($where_arr)
							->select($select_arr)
							->join($this->integrated .'.tblfacultydisplay as faculty','faculty.faculty_id = sched.prof_id','LEFT')
					 	    ->get($this->hsu .'.schedules as sched')->row();
		}
	}

	public function get_category()
	{
		return $this->db->order_by('category_id')->get('tblcategory')->result();
	}

	public function get_question()
	{
		return $this->db->join('tblcategory','tblcategory.category_id = tblquestion.QuestionCatId','left')
						->order_by('tblquestion.QuestionCatId')
						->get('tblquestion')->result();
	}

	public function get_studanswer($StudNo, $cfn, $question = FALSE)
	{
		$curr_sy = $this->session->userdata('SyId');
		$curr_sem = $this->session->userdata('SemId');

		$condition = array(
			'studno' => $StudNo,
			'SyId' => $curr_sy,
			'SemId' => $curr_sem,
			'cfn' => $cfn,
		);

		if($question)
		{
			$this->db->where('question_id', $question);
		}

		$studanswer = $this->db->where($condition)
						->get('tblstudanswer')->result();

		$data = array();

		foreach ($studanswer as $studans)
		{
			$data[intval($studans->question_id)] = $studans->stud_rating;
		}

		return $data;
	}

	public function batch_save($post, $primary_id = NULL)
	{
		if ($primary_id === NULL)
		$this->db->insert_batch('tblstudanswer', $post['stud_questionnaire']);
		else
		$this->db->update_batch('tblstudanswer', $_POST['stud_questionnaire'], $primary_id);

		return $this->db->affected_rows();
	}


	public function new_student_questionnaire($cfn, $sy_id, $sem_id, $StudNo, $prof_id)
	{
		$data = array();

		foreach(self::get_question() as $key => $value)
		{
			$question = new stdClass();
			$question->studans_id = NULL;
			$question->studno = $StudNo;
			$question->question_id = $value->QuestionId;
			$question->category_id = $value->QuestionCatId;
			$question->cfn = $cfn;
			$question->SyId = $sy_id;
			$question->SemId = $sem_id;
			$question->prof_id = $prof_id;
			$question->stud_rating = NULL;
			// $question->date_fill = date('Y-m-d H:i:s');
			$question->category_desc = $value->category_desc;
			$question->category_percentage = $value->category_percentage;
			$question->Question = $value->Question;
			$data[] = $question;
		}

		return $data;
	}

	public function get_student_questionnaire($cfn, $sy_id, $sem_id, $StudNo, $prof_id)
	{
		$this->db->select('tblstudanswer.*, category_desc, category_percentage, Question');

		$this->db->where('cfn', $cfn);
		$this->db->where('SyId', $sy_id);
		$this->db->where('SemId', $sem_id);
		$this->db->where('StudNo', $StudNo);
		$this->db->order_by('category_id, question_id');
		$this->db->join('tblquestion','tblstudanswer.question_id = tblquestion.QuestionId','left');
		$this->db->join('tblcategory','tblcategory.category_id = tblquestion.QuestionCatId','left');

		$data = $this->db->get('tblstudanswer')->result();

		if ( ! empty($data))
		return $data;

		// return self::get_new($cfn, $sy_id, $sem_id, $StudNo, $prof_id);
	}

	/* end updates */

	// get rating scale
	function get_ratingscale(){
		return $this->db->get('tblratingscale')->result();
	}

	public function verify_student($StudNo)
	{
     	$sysem = $this->main_model->get_current_sysem();

		$curr_sy = $sysem->SyId;
		$curr_sem = $sysem->SemId;

		$condition = array(
			'SyId' => $curr_sy,
			'SemId' => $curr_sem,
			'StudNo' => $StudNo,
			'IsPrinted' => 1,
		);

		$college = $this->db->where($condition)
						   ->get($this->integrated . '.tblenrollmenttrans')->row();

		$hsu = $this->db->where(array('sy_id'=>$curr_sy,'sem_id'=>$curr_sem,'stud_id'=>$StudNo))
						   ->get($this->hsu . '.student_enrollments')->row();

		if( ! count($college) && ! count($hsu))
		{
			$this->session->set_flashdata('error', 'Student is not officially enrolled.');
			return FALSE;
		}

		unset($condition['IsPrinted']);

		$result = $this->db->where($condition)
						   ->join('tblstudevaluation','tblstudevaluation.evaluation_id = tbltascactivation.evaluation_id','left')
						   ->get('tbltascactivation')
						   ->result();

		if(count($result))
		{
			$this->session->set_flashdata('error', 'Student already verified.');
			return FALSE;
		}

		$studeval = array(
			'tblstudevaluation.SyId' => $curr_sy,
			'tblstudevaluation.SemId' => $curr_sem,
			'StudNo' => $StudNo,
			'IsEvaluated' => 0,
			'created_at' => date('Y-m-d H:i:s'),
			'is_actived' => 1,
		);

		$this->db->insert('tblstudevaluation',$studeval);

		$tasactivation = array(
			'Assessedby' => $this->session->userdata('user_account')->dbUserid,
			'DateAssessed' => date('Y-m-d H:i:s'),
			'evaluation_id' => $this->db->insert_id(),
			'created_at' => date('Y-m-d H:i:s'),
		);

		$this->db->insert('tbltascactivation',$tasactivation);

		return TRUE;
	}

	public function get_student($StudNo)
	{
		$this->db->select('CONCAT(Lname,", ",Fname," ",Mname) as student', FALSE);
		$this->db->where('StudNo',$StudNo);
		$result = $this->db->get($this->integrated . '.tblstudinfo')->row();

		if(count($result))
		{
			return $result->student;
		}

		$this->db->select('CONCAT(family_name,", ",first_name," ",middle_name) as student', FALSE);
		$this->db->where('stud_id',$StudNo);
		$result = $this->db->get($this->hsu . '.examinees')->row();

		if(count($result))
		{
			return $result->student;
		}
	}

	public function get_course_evaluated($StudNo)
	{
		$sysem = $this->main_model->get_current_sysem();

		$curr_sy = $sysem->SyId;
		$curr_sem = $sysem->SemId;

		$condition = array(
			'SyId' => $curr_sy,
			'SemId' => $curr_sem,
			'StudNo' => $StudNo,
			'is_complete' => 1,
		);

		$result = $this->db->select('cfn')->where($condition)->get('tblstudcourseeval')->result();

		$data = array();

		foreach ($result as $row)
		{
			$data[] = $row->cfn;
		}

		return $data;
	}

	public function get_incomplete_evaluation($StudNo)
	{
		$sysem = $this->main_model->get_current_sysem();

		$curr_sy = $sysem->SyId;
		$curr_sem = $sysem->SemId;

		$condition = array(
			'SyId' => $curr_sy,
			'SemId' => $curr_sem,
			'StudNo' => $StudNo,
			'is_complete' => 0,
		);

		$result = $this->db->select('cfn')->where($condition)->get('tblstudcourseeval')->result();

		$data = array();

		foreach ($result as $row)
		{
			$data[] = $row->cfn;
		}

		return $data;
	}

	public function is_done_evaluation($StudNo)
	{
		$curr_sy = $this->session->userdata('SyId');
		$curr_sem = $this->session->userdata('SemId');

		$studload_count = $this->session->userdata('studload_count');
		$course_eval = $this->db->where(array(
											'StudNo' => $StudNo,
											'SyId' => $curr_sy,
											'SemId' => $curr_sem,
											'is_complete' => 1
										))->get('tblstudcourseeval')->num_rows();

		if($studload_count == $course_eval)
		{
			return TRUE;
		}

		return FALSE;
	}

	public function get_evaluation_summary($cfn,$StudNo)
	{
		$this->db->select(array('Question','stud_rating','category_desc'));
		$this->db->join('tblquestion','tblquestion.QuestionId = tblstudanswer.question_id','left');
		$this->db->join('tblcategory','tblcategory.category_id = tblstudanswer.category_id','left');
		return $this->db->where(array('cfn'=>$cfn,'StudNo'=>$StudNo))->get('tblstudanswer')->result();
	}

	public function is_barred_stud($StudNo, $stud_type)
	{
		if($stud_type == 'COLLEGE')
		{
			return $this->db->where(array(
								'StudNo' => $StudNo,
								'olea' => 1,
								'is_actived' => 1,
								'is_itc_clear' => 0,
								'is_reg_clear' => 0,
							))
					 ->get($this->integrated .'.tblbarredstudent')
					 ->row();
		}
		else
		{
			return $this->db->where(array(
								'StudNo' => $StudNo,
								'is_actived' => 1,
							))
					 ->get($this->hsu .'.tblbarredstudent')
					 ->row();
		}
	}

	public function with_pending_cmat($StudNo)
	{
		$sysem = $this->main_model->get_current_sysem();

		$curr_sy = $sysem->SyId;
		$curr_sem = $sysem->SemId;

		$this->db->select(array(
				'A.StudNo',
				'B.cfn_from',
				'B.cfn_to',
			));
		$this->db->where(array(
				'A.StudNo' => $StudNo,
				'A.SyId' => $curr_sy,
				'A.SemId' => $curr_sem,
				'A.printed_at' => '0000-00-00 00:00:00',
				'A.confirm_at != ' => '0000-00-00 00:00:00',
				'A.is_actived' => 1,
			));
		$this->db->join($this->integrated . '.tblstudcmatloads as B','A.cmat_id = B.cmat_id AND A.StudNo = B.StudNo','left');
		$result = $this->db->get($this->integrated . '.tblstudcmat as A')->result();

		$data = array();
		foreach ($result as $row)
		{
			if($row->cfn_from != '')
			$data[] = $row->cfn_from;

			if($row->cfn_to != '')
			$data[] = $row->cfn_to;
		}

		return $data;
	}

	public function add_studentlogs($studNo, $remarks)
	{
		$this->db->insert('tblstudentlogs',array(
											'StudNo'=>$studNo,
											'Remarks'=>$remarks,
											'created_at'=>date('Y-m-d h:i:s')
										));
	}

	public function feval_login($StudNo)
	{
		$this->db->where('StudNo',$StudNo);
		$student_login = $this->db->get('tblstudfevallogin')->row();

		if(!empty($student_login))
		{

		}
	}

	public function count_login()
	{
		$this->db->select('count(*) as  cnt',FALSE)
				 ->where('IsLogin',1);
		return $this->db->get('tblstudfevallogin')->row();
	}

	public function update_logout_r($StudNo = NULL)
	{
		$update_logout = array(
							'IsLogin' =>0,
							'LogoutTime' => date('Y-m-d H:i')
						 );
		$this->db->where('IsLogin',1);
		if ($StudNo == NULL)
			$this->db->where("LoginTime <='".date('Y-m-d H:i',strtotime("-{$this->allowed_time} minutes",strtotime(date('Y-m-d H:i'))))."'");
		else
			$this->db->where('StudNo',$StudNo);

		$this->db->update('tblstudfevallogin',$update_logout);
	}

	public function update_login($StudNo=null,$ITC)
	{
		$IsITC = 0;
		if ($ITC == "ITC") 
			$IsITC = 1;

		$this->db->select('count(*) as  cnt',FALSE);
		$count = $this->db->where(array('StudNo'=>$StudNo))->get('tblstudfevallogin')->row();

		$update_login = array(
							'StudNo' =>$StudNo,
							'IsLogin' =>1,
							'LoginTime' => date('Y-m-d H:i'),
							'IsITC' => $IsITC
						 );
		if ($count->cnt >= 1)
		{
			unset($update_login['StudNo']);
			$update_login['updated_at'] = date('Y-m-d H:i');
			$this->db->where('StudNo',$StudNo);
			return $this->db->update('tblstudfevallogin',$update_login);
		}
		else
		{

			return $this->db->insert('tblstudfevallogin',$update_login);
		}

	}
}
