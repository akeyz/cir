<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Out_model extends MY_Model {

	private $TABLE_OUT_PHONE = 'out_phone';
  private $TABLE_OUT_PHONE_BLACK = 'out_phone_black';
  private $TABLE_OUT_SUCCESS = 'out_success';

	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}

	public function get_active_phone()
	{
		$sql_lock = "LOCK TABLES yzm_out_phone WRITE, yzm_income_phone READ, yzm_out_phone_black READ;";
		$sql = '
			SELECT `phone`
			FROM yzm_income_phone
			WHERE `phone` NOT IN (SELECT `phone` FROM yzm_out_phone)
			AND `phone` NOT IN (SELECT `phone` FROM yzm_out_phone_black)
			ORDER BY `id` DESC
			LIMIT 1
			';
		$sql_unlock = "UNLOCK TABLES";

		//lock
		$this->master->query($sql_lock);
		//query
		$query = $this->master->query($sql);

		$data = $query->row_array();
		$phone = NULL;

		if (!empty($data)) {
			$phone = $data['phone'];
			$this->add_active_phone(array('phone' => $phone));
		}

		//unlock
		$this->master->query($sql_unlock);

		return $phone;
	}

	public function add_active_phone($data)
	{
		$this->master->insert($this->TABLE_OUT_PHONE,$data);
		return $this->master->insert_id();
	}

	public function get_black_data_by_phone($phone)
	{
		$sql = "
			SELECT `id`
			FROM `yzm_out_phone_black`
			WHERE `phone` = '".$phone."'
			";

		$query = $this->slave->query($sql);

		return $query->row_array();
	}

	public function add_black_phone($data)
	{
		$this->master->insert($this->TABLE_OUT_PHONE_BLACK,$data);
		return $this->master->insert_id();
	}

	public function remove_active_phone($phone)
	{
		$sql = "
			DELETE FROM `yzm_out_phone`
			WHERE `phone` = '".$phone."'
  		";

  	return $this->master->query($sql);
	}

	public function delete_outdate_phone($time)
  {
  	$sql = "
			DELETE FROM `yzm_out_phone`
			WHERE `date_created` < '".$time."'
  		";

  	return $this->master->query($sql);
  }

	public function get_sms_by_phone($phone)
	{
		$sql = "
			SELECT `yzm_income_sms`.`sms` 
			FROM `yzm_income_sms` 
			LEFT JOIN `yzm_out_phone` 
			ON `yzm_income_sms`.`phone` = `yzm_out_phone`.`phone` 
			WHERE `yzm_income_sms`.`date_created` > `yzm_out_phone`.`date_created`
			AND `yzm_income_sms`.`phone` = '".$phone."'
			";

		$query = $this->slave->query($sql);

		$data = $query->row_array();
		if (!empty($data)) {
			return $data['sms'];
		}
		return NULL;
	}

	public function get_success_data_by_phone_and_email($phone,$email)
	{
		$this->slave->where('phone',$phone);
		$this->slave->where('email',$email);
		$query = $this->slave->get($this->TABLE_OUT_SUCCESS);

		$data = $query->row_array();

		if (!empty($data)) {
			return $data;
		}

		return NULL;
	}

	public function add_success_phone($data)
	{
		$this->master->insert($this->TABLE_OUT_SUCCESS,$data);
		return $this->master->insert_id();
	}

}

/* End of file out_model.php */
/* Location: ./application/models/out/out_model.php */