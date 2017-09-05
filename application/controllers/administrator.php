<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administrator extends CI_Controller {

	function __construct() {
        parent::__construct();

        // load model
        $this->load->model('main_model');

        //setting default time zone
    	date_default_timezone_set("Asia/Manila");
    }


    public function index()
    {
    	if($this->input->post('btn_login') !== NULL)
    	{
    		// setup validation rules
	    	$rules = array(
		        array('field' => 'Username','label' => 'Username','rules' => 'trim|xss_clean|required'),
		        array('field' => 'Password','label' => 'Password','rules' => 'trim|xss_clean|required|callback__validate_login'),
			);

	    	// add validation rules
			$this->form_validation->set_rules($rules);

			if ($this->form_validation->run())
	        {
	        	redirect(base_url("administrator/student_verification"));
	        }
    	}

		$this->load->view('administrator/admin-login');
    }

    public function _validate_login()
    {
    	$username = $this->input->post('Username');
    	$password = sha1($this->input->post('Password'));

    	$result = $this->db->where(array('dbUname'=>$username,'dbPword'=>$password))
    			 		   ->get('tblusers')->row();

    	if(count($result))
    	{
    		$this->session->set_userdata('user_account',$result);
    		return TRUE;
    	}

    	$this->form_validation->set_message('_validate_login', 'Username or Password is invalid.');
		return FALSE;		 
    }

    public function student_verification()
    {
    	if( $this->session->userdata('user_account') != NULL )
    	{
	    	$data = array();

	    	if(isset($_POST['btn_verify']) && $this->input->post('StudNo') != '')
	    	{
				$StudNo = $this->input->post('StudNo');
	    		$result = $this->main_model->verify_student($StudNo);

				if ($result == TRUE)
		        {
	    			$data['success'] = 'Student has been successfully verified.';
	    			$data['student'] = $this->main_model->get_student($StudNo);
	    			$data['studno'] = $StudNo;
		        }
		        else
		        {
		        	redirect('administrator/student_verification','refresh');
		        }
	    	}

			$this->load->view('administrator/admin-student-verification',$data);
    	}
    	else
    	{
    		$this->session->set_flashdata('error', 'User credential not found, Please login.');
    		redirect('administrator','refresh');
    	}
    }

	public function logout()
	{
		$this->session->sess_destroy();
		redirect("administrator");
	}
	
}

/* End of file administrator.php */
/* Location: ./application/controllers/administrator.php */
