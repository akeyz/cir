<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_model extends MY_Model {

  private $TABLE_INCOME_PHONE = 'income_phone';
  private $TABLE_INCOME_SMS = 'income_sms';

  public function __construct()
  {
    parent::__construct();
    //Do your magic here
  }

  public function add_phone($data)
  {
    $this->master->insert($this->TABLE_INCOME_PHONE,$data);
    return $this->master->insert_id();
  }

  public function get_data_by_phone($phone)
  {
    $this->slave->where('phone',$phone);
    $query = $this->slave->get($this->TABLE_INCOME_PHONE);
    $data = $query->row_array();

    if (!empty($data)) {
      return $data;
    }
    return NULL;
  }

  public function add_sms($data)
  {
    $this->master->insert($this->TABLE_INCOME_SMS,$data);
    return $this->master->insert_id();
  }

  public function delete_outdate_phone($time)
  {
  	$sql = "
			DELETE FROM `yzm_income_phone`
			WHERE `date_created` < '".$time."'
  		";

  	return $this->master->query($sql);
  }

}

/* End of file income_model.php */
/* Location: ./application/models/income/income_model.php */