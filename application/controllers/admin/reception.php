<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reception extends Admin_Controller
{

	/**
	 * constructor method
	 */
	public function __construct()
	{

		parent::__construct();
		$this->lang->load("patients", 'english');
		$this->lang->load("system", 'english');
		$this->load->model("admin/test_group_model");
		$this->load->model("admin/reports_model");
		$this->load->model("admin/invoice_model");
		$this->load->model("admin/test_type_model");


		$this->load->model("admin/patient_model");
		// $this->load->model("admin/patient_model");
		//$this->output->enable_profiler(TRUE);
	}
	//---------------------------------------------------------------



	public function today_progress_report()
	{

		$this->data = $this->reports_model->daily_reception_report();
		$this->load->view(ADMIN_DIR . "reception/today_report", $this->data);
	}


	/**
	 * Default action to be called
	 */
	public function index2()
	{

		$where = "`test_groups`.`status` IN (1) ORDER BY  test_group_name ASC";
		$this->data["test_groups"] = $this->test_group_model->get_test_group_list($where, false);
		$this->data["test_categories"] = $this->test_type_model->getList("test_categories", "test_category_id", "test_category", $where = "`test_categories`.`status` IN (1) ");


		$where = "`invoices`.`status` IN (1,2,3) AND DATE(`invoices`.`created_date`) = DATE(NOW())  ORDER BY `invoices`.`invoice_id` DESC";
		$this->data["all_tests"] = $this->invoice_model->get_invoice_list($where, false);
		$this->load->view(ADMIN_DIR . "reception/home2", $this->data);
	}

	/**
	 * Default action to be called
	 */
	public function index()
	{

		$where = "`test_groups`.`status` IN (1) AND category_id!=5 ORDER BY  test_group_name ASC";
		$this->data["test_groups"] = $this->test_group_model->get_test_group_list($where, false);
		$this->data["test_categories"] = $this->test_type_model->getList("test_categories", "test_category_id", "test_category", $where = "`test_categories`.`status` IN (1) ");


		$where = "`invoices`.`status` IN (1,2,3) AND DATE(`invoices`.`created_date`) = DATE(NOW())  ORDER BY `invoices`.`invoice_id` DESC";
		$this->data["all_tests"] = $this->invoice_model->get_invoice_list($where, false);
		$this->load->view(ADMIN_DIR . "reception/home", $this->data);
	}



	public function save_data()
	{

		//save patient data and get pacient id ....
		if ($this->input->post('patientID') and ($this->input->post('patient_name') != "Dr Ref" or  $this->input->post('patient_name') != "Dr. Ref")) {
			$patient_id = (int) $this->input->post('patientID');
		} else {
			$patient_id = $this->patient_model->save_data();
		}
		$test_group_ids = rtrim($this->input->post('testGroupIDs'), ',');
		//$test_group_ids =  implode(',', $this->input->post('test_group_id'));
		//exit();


		$query = "SELECT category_id FROM `test_groups` 
				WHERE `test_groups`.`test_group_id` IN (" . $test_group_ids . ")
				GROUP BY `category_id`";
		$category_id = $this->db->query($query)->result();
		if (count($category_id) > 1) {
			echo 'You select different group category. please select same group. click here <a href="' . site_url(ADMIN_DIR . "reception") . '" > Home </a>';
			exit();
		}


		$discount = $this->input->post("discount");

		$tax = $this->input->post("tax");
		$refered_by = $this->input->post("refered_by");

		$query = "SELECT SUM(`test_price`) as `total_test_price` 
				FROM `test_groups` 
				WHERE `test_groups`.`test_group_id` IN (" . $test_group_ids . ")";
		$query_result = $this->db->query($query);
		$total_test_price = $query_result->result()[0]->total_test_price;


		$inputs = array();

		$inputs["patient_id"]  =  $patient_id;
		$inputs["discount"]  =  $discount;
		$inputs["price"]  =  $total_test_price;
		$inputs["sale_tax"]  =  $tax;
		$inputs["total_price"]  =  ($total_test_price + $tax) - $discount;
		$inputs["patient_refer_by"]  =  $refered_by;
		$inputs["created_by"]  =  $this->session->userdata('user_id');
		$inputs["category_id"]  =  $category_id[0]->category_id;
		$inputs['alkhidmat_income'] = $inputs["total_price"];
		if ($discount > 0) {
			$inputs['discount_type_id'] = $this->input->post("discount_type_id");
			$inputs['discount_ref_by'] = $this->input->post("discount_ref_by");
		}


		if ($category_id[0]->category_id == 5) {
			$today_count = $this->db->query("SELECT count(*) as total FROM `invoices` 
			WHERE category_id = '" . $category_id[0]->category_id . "'
			AND opd_doctor = '" . $test_group_ids . "'
			AND DATE(created_date) = DATE(NOW())")->result()[0]->total;
			$inputs["opd_doctor"] = $test_group_ids;
			$inputs['alkhidmat_income'] = 0;
			$query = "SELECT `test_groups`.`share` FROM `test_groups` WHERE `test_groups`.`test_group_id`='" . $test_group_ids . "'";
			$inputs['alkhidmat_income'] = $this->db->query($query)->result()[0]->share;

			$inputs["patient_refer_by"]  =  1;
		} else {
			$today_count = $this->db->query("SELECT count(*) as total FROM `invoices` 
		               WHERE category_id = '" . $category_id[0]->category_id . "'
					   AND DATE(created_date) = DATE(NOW())")->result()[0]->total;
		}
		if ($test_group_ids == 4) {
			$inputs["today_count"]  =  $this->input->post("appointment_no");
			$status = 3;
		} else {
			$inputs["today_count"]  =  $today_count + 1;
			$status = 1;
		}


		$invoice_id  = $this->invoice_model->save($inputs);


		$where = "`test_groups`.`test_group_id` IN (" . $test_group_ids . ") ORDER BY `test_groups`.`order`";
		$patient_test_groups = $this->test_group_model->get_test_group_list($where, false);
		foreach ($patient_test_groups as $patient_test_group) {
			$query = "INSERT INTO `invoice_test_groups`(`invoice_id`, `patient_id`, `test_group_id`, `price`) 
				    VALUES ('" . $invoice_id . "', '" . $patient_id . "', '" . $patient_test_group->test_group_id . "', '" . $patient_test_group->test_price . "')";
			$this->db->query($query);
		}


		$test_token_id = time();
		$group_ids = $test_group_ids;

		// if ($category_id[0]->category_id == 1) {
		// } else {
		// }


		$query = "UPDATE `invoices` 
					SET `test_token_id`='" . $test_token_id . "',
						`test_report_by`='" . $this->session->userdata("user_id") . "',
						`status`='" . $status . "'
					WHERE `invoice_id` = '" . $invoice_id . "'";
		$this->db->query($query);

		// $query = "SELECT 
		// 			  `test_group_tests`.`test_group_id`,
		// 			  `tests`.`test_id`,
		// 			  `tests`.`test_category_id`,
		// 			  `tests`.`test_type_id`,
		// 			  `tests`.`test_name`,
		// 			  `tests`.`test_description`,
		// 			  `tests`.`normal_values` 
		// 			FROM
		// 			  `tests`,
		// 			  `test_group_tests`
		// 			WHERE  `tests`.`test_id` = `test_group_tests`.`test_id` 
		// 			AND `test_group_tests`.`test_group_id` IN (" . $group_ids . ") 
		// 			ORDER BY `test_group_tests`.`test_group_id` ASC, `test_group_tests`.`order` ASC";
		// $query_result = $this->db->query($query);
		// $all_tests = $query_result->result();
		// $order = 1;
		// foreach ($all_tests as $test) {
		// 	$query = "INSERT INTO `patient_tests`(`invoice_id`, 
		// 											  `test_group_id`, 
		// 											  `test_category_id`, 
		// 											  `test_type_id`, 
		// 											  `test_id`, 
		// 											  `test_name`, 
		// 											  `test_normal_value`, 
		// 											  `test_result`, 
		// 											  `remarks`,
		// 											  `created_by`,
		// 											  `order`) 
		// 									VALUES('" . $invoice_id . "',
		// 										   '" . $test->test_group_id . "',
		// 										   '" . $test->test_category_id . "',
		// 											'" . $test->test_type_id . "',
		// 											'" . $test->test_id . "',
		// 											'" . $test->test_name . "',
		// 											'" . $test->normal_values . "',
		// 											'',
		// 											'',
		// 											'" . $this->session->userdata("user_id") . "',
		// 											'" . $order++ . "')";
		// 	$this->db->query($query);
		// }

		$this->session->set_flashdata("msg_success", "Data Save Successfully.");
		redirect(ADMIN_DIR . "reception");
	}


	public function save_and_process()
	{

		$invoice_id = (int) $this->input->post("invoice_id");
		$test_token_id = (int) $this->input->post("test_token_id");
		$group_ids = trim(trim($this->input->post("patient_group_test_ids")), ",");

		$query = "UPDATE `invoices` 
					SET `test_token_id`='" . $test_token_id . "',
						`test_report_by`='" . $this->session->userdata("user_id") . "',
						`status`='2'
					WHERE `invoice_id` = '" . $invoice_id . "'";
		$this->db->query($query);

		$query = "SELECT 
					  `test_group_tests`.`test_group_id`,
					  `tests`.`test_id`,
					  `tests`.`test_category_id`,
					  `tests`.`test_type_id`,
					  `tests`.`test_name`,
					  `tests`.`test_description`,
					  `tests`.`normal_values` 
					FROM
					  `tests`,
					  `test_group_tests`
					WHERE  `tests`.`test_id` = `test_group_tests`.`test_id` 
					AND `test_group_tests`.`test_group_id` IN (" . $group_ids . ") 
					ORDER BY `test_group_tests`.`test_group_id` ASC, `test_group_tests`.`order` ASC";
		$query_result = $this->db->query($query);
		$all_tests = $query_result->result();
		$order = 1;
		foreach ($all_tests as $test) {
			$query = "INSERT INTO `patient_tests`(`invoice_id`, 
													  `test_group_id`, 
													  `test_category_id`, 
													  `test_type_id`, 
													  `test_id`, 
													  `test_name`, 
													  `test_normal_value`, 
													  `test_result`, 
													  `remarks`,
													  `created_by`,
													  `order`) 
											VALUES('" . $invoice_id . "',
												   '" . $test->test_group_id . "',
												   '" . $test->test_category_id . "',
													'" . $test->test_type_id . "',
													'" . $test->test_id . "',
													'" . $test->test_name . "',
													'" . $test->normal_values . "',
													'',
													'',
													'" . $this->session->userdata("user_id") . "',
													'" . $order++ . "')";
			$this->db->query($query);
		}



		redirect(ADMIN_DIR . "reception/");
	}


	public function complete_test()
	{
		$invoice_id = (int) $this->input->post("invoice_id");
		$query = "UPDATE `invoices` 
						SET `status`='3'
						WHERE `invoice_id` = '" . $invoice_id . "'";
		$this->db->query($query);
		redirect(ADMIN_DIR . "reception/");
	}

	public function get_patient_detail()
	{
		$patient = $this->db->escape($this->input->post('patient'));
		$query = "SELECT * FROM patients WHERE patient_name = $patient AND DATE(`created_date`)=DATE(NOW())";
		$patient_detail = $this->db->query($query)->result()[0];
		echo json_encode($patient_detail);
	}

	public function get_patient_detail_by_id()
	{
		$patient_id = $this->db->escape($this->input->post('patient_id'));
		$query = "SELECT * FROM patients WHERE patient_id = $patient_id";
		$this->data['patient'] = $this->db->query($query)->result()[0];
		$this->load->view(ADMIN_DIR . "reception/update_patient_detail", $this->data);
	}

	public function update_patient_data()
	{
		$patient_id = (int) $this->input->post("patient_id");
		$patient_name =  $this->db->escape($this->input->post("patient_name"));
		$patient_address =  $this->db->escape($this->input->post("patient_address"));
		$patient_age =  $this->db->escape($this->input->post("patient_age"));
		$patient_gender =  $this->db->escape($this->input->post("patient_gender"));
		$patient_mobile_no =  $this->db->escape($this->input->post("patient_mobile_no"));

		$query = "UPDATE patients SET patient_name = $patient_name,
		       patient_address = $patient_address,
			   patient_age = $patient_age,
			   patient_gender = $patient_gender,
			   patient_mobile_no = $patient_mobile_no
			   WHERE patient_id = '" . $patient_id . "'";
		$this->db->query($query);


		$this->session->set_flashdata("msg_success", "Patient Information Update Successfully.");
		redirect(ADMIN_DIR . "reception");
	}
}
