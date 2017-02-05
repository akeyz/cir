<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Out extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
		$this->load->model(array('out/out_model'));
	}

	public function phone_post()
	{
		$phone = $this->out_model->get_active_phone();

		$data['code'] = '0';
		$data['message'] = 'failed';
		$data['phone'] = '';
		
		if (!empty($phone)) {
			$data['code'] = '1';
			$data['message'] = 'successed';
			$data['phone'] = $phone;
		}

		$this->response($data);
	}

	public function sms_post()
	{
		$phone = $this->input->get('phone');

		if (empty($phone)) {
			exit();
		}

		$sms = $this->out_model->get_sms_by_phone($phone);

		$data['code'] = '0';
		$data['message'] = 'failed';
		$data['sms'] = '';
		
		if (!empty($phone)) {
			$data['code'] = '1';
			$data['message'] = 'successed';
			$data['sms'] = $sms;
		}

		$this->response($data);
	}

	public function release_post()
	{
		$phone = $this->input->get('phone');

		if (empty($phone)) {
			$time = date("Y-m-d H:i:s", time() - 60*20);
			$this->out_model->delete_outdate_phone($time);
		}

		$data['code'] = '0';
		$data['message'] = 'failed';
		if ($this->out_model->remove_active_phone($phone)) {
			$data['code'] = '1';
			$data['message'] = 'successed';
		}

		$this->response($data);
	}

	public function black_post()
	{
		$phone = $this->input->get('phone');

		if (empty($phone)) {
			exit();
		}

		$data['code'] = '1';
		$data['message'] = 'successed';
		$data_black = $this->out_model->get_black_data_by_phone($phone);
		if (empty($data_black)) {
			if (!$this->out_model->add_black_phone(array('phone' => $phone))) {
				$data['code'] = '0';
				$data['message'] = 'failed';
			}
		}
		
		$this->response($data);
	}

	public function success_post()
	{
		$phone = $this->input->get('phone');
		$email = $this->input->get('email');

		if (!empty($phone) && !empty($email)) {
			if (!$this->out_model->get_success_data_by_phone_and_email($phone,$email)) {
				$data_success = array(
					'phone' => $phone,
					'email' => $email,
				);
				$this->out_model->add_success_phone($data_success);
			}
		}
	}

}

/* End of file out.php */
/* Location: ./application/controllers/api/out.php */