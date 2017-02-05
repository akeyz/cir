<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income extends MY_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->load->model('income/income_model');
  }

  public function phone_post()
  {
  	if (empty($this->input->get('phone'))) {
  		exit();
  	}

    $data_phone['phone'] = substr($this->input->get('phone'), -11);

    $data['code'] = '0';
    $data['message'] = 'failed';

    $time = !empty($this->input->get('time')) && is_numeric($this->input->get('time')) ? $this->input->get('time') : '40';
    $time = date("Y-m-d H:i:s", time() - 60*$time);

    $this->income_model->delete_outdate_phone($time);

    if (!empty($data_phone['phone'])) {
      $data_check = $this->income_model->get_data_by_phone($data_phone['phone']);

      if (empty($data_check)) {
        $this->income_model->add_phone($data_phone);
      }
      
      $data['code'] = '1';
      $data['message'] = 'successed';
    }
    
    $this->response($data);
  }

  public function sms_post()
  {
    $data_sms['phone'] = $this->input->get('phone');
    $data_sms['sms'] = $this->input->get('sms');

    $data['code'] = '0';
    $data['message'] = 'failed';

    if (!empty($data_sms['phone']) && !empty($data_sms['sms'])) {
      $this->income_model->add_sms($data_sms);
      
      $data['code'] = '1';
      $data['message'] = 'successed';
    }
    
    $this->response($data);
  }

}

/* End of file income.php */
/* Location: ./application/controllers/api/income.php */