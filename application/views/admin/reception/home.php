<?php $this->load->view(ADMIN_DIR . "reception/reception_header"); ?>



<?php

//now we will check if the current module is assigned to the user or not
/*$this->data['current_action_id'] = $current_action_id = $this->module_m->actionIdFromName($this->controller_name, $this->method_name);*/
$this->data['current_action_id'] = $current_action_id = $this->module_m->allowed_module_id($this->controller_name);

$allowed_modules = $this->mr_m->rightsByRole($this->session->userdata("role_id"));

//var_dump($allowed_modules);
//add role homepage to allowed modules
$allowed_modules[] = $this->session->userdata("role_homepage_id");

if (!in_array($current_action_id, $allowed_modules)) { ?>

  <div style=" margin:0px auto; width:100%; text-align:center !important;">
    <div style="margin:150px !important;">

      <h1 style="color: #d9534f;  font-size: 80px;  ">Access Denied</h1>
      <div class="content">
        <h3>Oops! Something went wrong</h3>
        <p>You are not allowed to access this module. Thanks.</p>
        <div class="btn-group">
          <a href="<?php echo site_url(ADMIN_DIR . $this->session->userdata("role_homepage_uri")); ?>" class="btn btn-danger"><i class="fa fa-chevron-left"></i> Go Back</a>
        </div>
      </div>

    </div>

  </div>

<?php
  exit();
}

?>



<?php
$add_form_attr = array("class" => "form-horizontal");
echo form_open_multipart(ADMIN_DIR . "reception/save_data", $add_form_attr);
?>
<div class="row">
  <!-- MESSENGER -->
  <div class="col-md-3">
    <div class="box border blue" id="messenger">
      <div class="box-title">
        <h4><i class="fa fa-user"></i>Patient Detail</h4>
      </div>
      <div class="box-body">

        <table style="width: 100%;" style="font-size: 8px;">

          <tr>
            <td style="text-align: center; color: #BC181D; font-size:12px; width:210px"><?php $file = pathinfo($system_global_settings[0]->sytem_admin_logo);
                                                                                        $log = $file['dirname'] . '/' . $file['filename'] . '_thumb.' . $file['extension'];
                                                                                        ?>
              <!-- <a href="<?php echo site_url(ADMIN_DIR . $this->session->userdata("role_homepage_uri")); ?>"> 
        <img src="<?php echo site_url("assets/uploads/" . $log); ?>" alt="<?php echo $system_global_settings[0]->system_title ?>" 
        title="<?php echo $system_global_settings[0]->system_title ?>" class="img-responsive " style="width:40px !important;"></a>-->
              <strong>Tareen Infertility & Impotence Center</strong>
            </td>
            <td style="font-size:12px">
              <ul class="nav navbar-nav pull-right" style="padding:0px">
                <li style="float:right" class="dropdown user" id="header-user"> <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="username"><?php //echo $this->session->userdata("user_title"); 
                                            ?>Logout <i class="fa fa-angle-down"></i></span> </a>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo site_url(ADMIN_DIR . "users/update_profile"); ?>"><i class="fa fa-user"></i> Update Profile</a></li>
                    <li><a href="<?php echo site_url(ADMIN_DIR . "users/logout"); ?>"><i class="fa fa-power-off"></i> Log Out</a></li>
                  </ul>
                </li>




              </ul>
            </td>
          </tr>
        </table>
        <hr style="margin-top: -5px;" />
        <link rel="stylesheet" href="<?php echo site_url("assets/" . ADMIN_DIR . "/other_files/jquery-ui.css"); ?>">
        <table style="width: 100%;">
          <tr>
            <td colspan="2">
              <div style="padding: 5px;">
                Search By Filled By Patient ID:
                <input class="form-control" type="text" id="patientId" name="patientId" value="" placeholder="Patient ID" />
              </div>
            </td>
          </tr>
          <tr>
            <td>Patient Name: </td>

            <td>
              <input style="display: none;" type="text" name="validate" id="validate" required="required" />

              <input type="hidden" name="patientID" id="patientID" />

              <input class="form-control" type="text" name="patient_name" value="" id="patient_name" autocomplete="off" class="ui-autocomplete-input" style="" required="required" title="Name" placeholder="Name">

              <span id="please_wait"></span>

            </td>


            <script>
              $('#patientId').on('keydown', function(e) {
                if (e.keyCode == 13) {

                  var patient_id = $('#patientId').val();
                  // alert(patient);
                  $('#please_wait').html('<p style="text-align:center"><strong>Please Wait...... Loading</strong></p>');
                  $.ajax({
                    type: "POST",
                    url: "<?php echo site_url(ADMIN_DIR . "/reception/get_patient_by_patient_id"); ?>",
                    data: {
                      patient_id: patient_id
                    }
                  }).done(function(data) {
                    var patient = jQuery.parseJSON(data);
                    //alert(patient);
                    $('#patient_name').val(patient.patient_name);
                    $('#patientID').val(patient.patient_id);
                    $('#patient_address').val(patient.patient_address);
                    $('#patient_age').val(patient.patient_age);
                    if (patient.patient_gender == 'Male') {
                      $('#male').attr('checked', true);
                    } else {
                      $('#female').attr('checked', true);
                    }
                    $('#patient_mobile_no').val(patient.patient_mobile_no);

                  });
                  $('#please_wait').hide();
                }

              });

              $(function() {
                var availableTags = [
                  <?php $query = "SELECT `patient_id`, `patient_name` 
                                  FROM `patients` 
                                  WHERE DATE(`created_date`) = DATE(NOW()) GROUP BY `patient_name`";
                  $today_patients = $this->db->query($query)->result();
                  foreach ($today_patients as $patient) { ?> "<?php echo $patient->patient_name; ?>", "<?php echo $patient->patient_id; ?>",
                  <?php } ?>


                ];
                $("#patient_name").autocomplete({
                  source: availableTags
                });
              });

              $('#patient_name').on('keydown', function(e) {
                if (e.keyCode == 13) {
                  var patient = $('#patient_name').val();
                  $('#please_wait').html('<p style="text-align:center"><strong>Please Wait...... Loading</strong></p>');
                  $.ajax({
                    type: "POST",
                    url: "<?php echo site_url(ADMIN_DIR . "/reception/get_patient_detail"); ?>",
                    data: {
                      patient: patient
                    }
                  }).done(function(data) {
                    var patient = jQuery.parseJSON(data);

                    $('#patientID').val(patient.patient_id);
                    $('#patient_address').val(patient.patient_address);
                    $('#patient_age').val(patient.patient_age);
                    if (patient.patient_gender == 'Male') {
                      $('#male').attr('checked', true);
                    } else {
                      $('#female').attr('checked', true);
                    }
                    $('#patient_mobile_no').val(patient.patient_mobile_no);

                  });
                  $('#please_wait').hide();
                }

              });
            </script>

          </tr>


          <tr>
            <td>Age: </td>
            <td><input type="number" name="patient_age" min="10" max="100" value="" id="patient_age" class="form-control" style="" required="required" title="Patient Age" placeholder="Patient Age"></td>
          </tr>
          <tr>
            <td>Sex: </td>
            <td>
              <input onclick="$('#female2').prop('checked', true);" type="radio" name="patient_gender" value="Male" id="male" style="" required="required" class="uniform">
              <label for="patient_gender" style="margin-left:10px;">Male</label>
              <input onclick="$('#male2').prop('checked', true);" type="radio" name="patient_gender" value="Female" id="female" style="" required="required" class="uniform">
              <label for="patient_gender" style="margin-left:10px;">Female</label>
            </td>
          </tr>
          <tr>
            <td>Mobile No:</td>
            <td><input type="text" minlength="11" name="patient_mobile_no" value="" id="patient_mobile_no" class="form-control" style="" title="Mobile No" placeholder="Mobile No"></td>
          </tr>
          <tr>
            <td>Address: </td>
            <td><input type="text" name="patient_address" value="" id="patient_address" class="form-control" style="" required="required" title="Address" placeholder="Address"></td>
          </tr>
          <tr>
            <td>Referred By: </td>
            <td><select class="form-control" required name="refered_by">
                <?php

                $query = "SELECT * FROM `doctors` WHERE `status`=1";
                $query_result = $this->db->query($query);
                $doctors = $query_result->result();

                foreach ($doctors as $doctor) { ?>
                  <option value="<?php echo $doctor->doctor_id; ?>"><?php echo $doctor->doctor_name; ?></option>
                <?php }  ?>
                <?php echo form_error("refered_by", "<p class=\"text-danger\">", "</p>"); ?>

              </select></td>
          </tr>
          <tr>
            <td colspan="2">

              <div id="multiple_token_option" style="display: none;">
                <input id="singleToken" type="radio" name="toke_type" value="single" checked required onclick="single_token()" /> Single Token
                <input id="multipleToken" type="radio" name="toke_type" value="multiple" required onclick="multiple_token()" /> Multiple Tokens
                <div id="second_patient_for" style="padding: 10px; display:none;">
                  <h5>Other Patient Detail</h5>
                  <table>
                    <tr>
                      <td>Patient Name: </td>
                      <td>
                        <input class="patient2" type="text" name="patient2_name" class="form-control" />
                      </td>
                    </tr>
                    <tr>
                      <td>Patient Age: </td>
                      <td>
                        <input class="patient2" type="number" name="patient2_age" class="form-control" />
                      </td>
                    </tr>
                    <tr>
                      <td>Sex: </td>
                      <td><input onclick="$('#female').prop('checked', true);" class="patient2" type="radio" name="patient2_gender" value="Male" id="male2" style="" class="uniform">
                        <label for="patient_gender" style="margin-left:10px;">Male</label>
                        <input onclick="$('#male').prop('checked', true);" class="patient2" type="radio" name="patient2_gender" value="Female" id="female2" style="" class="uniform">
                        <label for="patient_gender" style="margin-left:10px;">Female</label>
                      </td>
                    </tr>
                  </table>

                </div>
              </div>
            </td>
          </tr>



        </table>
        <hr />




        <div>





          <div id="test_price_list" style="min-height: 130px;">
            <table class="table table-bordered">
              <tr>
                <td>#</td>
                <td>Test Name</td>
                <td>Price</td>
              </tr>

            </table>
          </div>
          <style>
            table>thead>tr>th,
            .table>tbody>tr>th,
            .table>tfoot>tr>th,
            .table>thead>tr>td,
            .table>tbody>tr>td,
            .table>tfoot>tr>td {
              padding: 5px !important;
            }
          </style>

          <hr />
          <div style="margin-bottom:10px; border:1px dashed #666666; border-radius:5px;  padding:5px; margin-bottom:5px">
            <table style="width:100%">
              <tr>
                <td rowspan="3"><strong>Discount:<strong> <br />
                      <input <?php if ($this->session->userdata('user_id') != 3) { ?> readonly <?php } ?> onkeyup="update_price_list()" type="number" name="discount" value="0" id="discount" class="for m-control" style="width:80px" /></td>
                <th>Total Price:</th>
                <td id="test_total_price">00.00 Rs. </td>
              </tr>
              <tr>
                <th>Discount: </th>
                <td id="discount_total">00.00 Rs.</td>
              </tr>
              <tr>
                <th>Payable: </th>
                <th id="payable">00.00 Rs.</th>
              </tr>
            </table>
            <div id="dicount_options" style="display: none;">
              <table style="margin-top: 5px;">
                <tr>
                  <td>
                    Discount Type:</td>
                  <td> <select name="discount_type_id" id="discount_type_id">
                      <option value="">Select Discount Type</option>
                      <?php $query = "SELECT * FROM discount_types";
                      $discount_types = $this->db->query($query)->result();
                      foreach ($discount_types as $discount_type) {
                      ?>
                        <option value="<?php echo $discount_type->discount_type_id; ?>"><?php echo $discount_type->discount_type; ?></option>
                      <?php } ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    On Reference By :</td>
                  <td> <input type="text" name="discount_ref_by" id="discount_ref_by" value="" />

                  </td>
                </tr>
              </table>
            </div>
            <input type="hidden" name="testGroupIDs" value="" id="testGroupIDs" />
          </div>
          <input type="submit" name="submit" value="Save and Print" class="btn btn-primary" style="width:100%">
        </div>


      </div>
    </div>
  </div>
  <div class="col-md-3" style="padding-left:1px !important; padding-right:1px !important; ">


    <div class="box border blue" id="messenger">
      <div class="box-title">
        <h4><i class="fa fa-flask"></i>Service Tokens (Reception)</h4>
      </div>
      <div class="box-body">
        <div style="background-color: white; padding-bottom:10px">
          <?php
          $query = "SELECT * FROM `test_groups` WHERE category_id=5";
          $appinments = $this->db->query($query)->result();
          foreach ($appinments as $appinment) {

          ?>
            <input class="appointments" style="display: inline;" name="test_group_id[]" id="TG_<?php echo $appinment->test_group_id; ?>" onclick="set_price('<?php echo $appinment->test_group_id; ?>', '<?php echo $appinment->test_group_name; ?>', '<?php echo $appinment->test_price; ?>', '<?php echo $appinment->test_time; ?>')" type="radio" value="<?php echo $appinment->test_group_id; ?>" />
            <strong><?php echo $appinment->test_group_name; ?></strong> <span style="margin-left: 5px;"></span>
            <?php if ($appinment->test_group_id == 4) {
              $query = "SELECT `invoices`.`today_count` FROM invoices WHERE `invoices`.`opd_doctor` = 4 and `invoices`.`category_id` = 5 
              and DATE(`invoices`.`created_date`) = DATE(NOW())
              AND is_deleted=0";
              $dr_appointments = $this->db->query($query)->result();
              $appointment_array = array();
              foreach ($dr_appointments as $dr_appointment) {
                $appointment_array[] = $dr_appointment->today_count;
              }

            ?>
              <table class="table table-bordered" id="dr_app_list" style="display: none; padding-top:10px">
                <tr>
                  <th colspan="10">
                    <h4 style="text-align: center;">Today Appointments</h4>
                  </th>
                </tr>
                <?php
                $count_row = 1;
                for ($i = 1; $i <= 130; $i++) { ?>
                  <?php if ($count_row == 1) { ?> <tr> <?php } ?>
                    <td style="text-align: center; <?php if (in_array($i, $appointment_array)) { ?> background-color:#90EE90 <?php } ?> ">

                      <?php if (in_array($i, $appointment_array)) {
                        $query = "SELECT COUNT(*) as total FROM invoices 
                        WHERE `invoices`.`opd_doctor` = 4 and `invoices`.`category_id` = 5 
                        AND DATE(`invoices`.`created_date`) = DATE(NOW())
                        AND today_count='" . $i . "'";

                        $appointment_total = $this->db->query($query)->row()->total;
                        if ($appointment_total == 1) {
                      ?>
                          <i class="fa fa-check" aria-hidden="true"></i>
                        <?php }

                        if ($appointment_total == 2) { ?>
                          <span class="fa-stack  text-success">
                            <i class="fa fa-check" style="margin-left:-3px;"></i>
                            <i class="fa fa-check" style="margin-left:-4px"></i>
                          </span>
                        <?php } ?>
                        <br /><strong style="vertical-align: middle;"><?php echo $i; ?> </strong>
                      <?php } else { ?>
                        <input class="appointment_numbers" type="radio" name="appointment_no" value="<?php echo $i; ?>" />
                        <br /> <small><?php echo $i; ?></small>
                      <?php } ?>

                    </td>
                    <?php if ($count_row == 10) {
                      $count_row = 0; ?>
                    <tr> <?php } ?>
                  <?php
                  $count_row++;
                } ?>

              </table>
            <?php } ?>

          <?php } ?>


        </div>



        <div class="row" style="font-size: 12px !important;">
          <link rel="stylesheet" type="text/css" href="<?php echo site_url("assets/" . ADMIN_DIR . "other_files/jquery.dataTables.css") ?>">

          <script type="text/javascript" charset="utf8" src="<?php echo site_url("assets/" . ADMIN_DIR . "other_files/jquery.dataTables.js") ?>"></script>
          <script type="text/javascript" language="javascript" src="<?php echo site_url("assets/" . ADMIN_DIR . "other_files/dataTables.buttons.min.js") ?>"></script>
          <script type="text/javascript" language="javascript" src="<?php echo site_url("assets/" . ADMIN_DIR . "other_files/jszip.min.js") ?>"></script>
          <script type="text/javascript" language="javascript" src="<?php echo site_url("assets/" . ADMIN_DIR . "other_files/pdfmake.min.js") ?>"></script>
          <script type="text/javascript" language="javascript" src="<?php echo site_url("assets/" . ADMIN_DIR . "other_files/vfs_fonts.js") ?>"></script>
          <script type="text/javascript" language="javascript" src="<?php echo site_url("assets/" . ADMIN_DIR . "other_files/buttons.html5.min.js") ?>"></script>
          <script type="text/javascript" language="javascript" src="<?php echo site_url("assets/" . ADMIN_DIR . "other_files/buttons.print.min.js") ?>"></script>

          <div class="col-md-12">

            <table id="testGroupsTable" class="display">
              <thead>
                <tr>
                  <th>#</th>
                  <!-- <th>G_ID</th> -->
                  <th>Test(s)</th>
                  <!-- <th>Time</th> -->
                  <th>Price</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($test_groups as $test_group) { ?>
                  <tr>
                    <td>
                      <input class="test_list" style="display: inline;" name="test_group_id[]" id="TG_<?php echo $test_group->test_group_id; ?>" onclick="set_price('<?php echo $test_group->test_group_id; ?>', '<?php echo $test_group->test_group_name; ?>', '<?php echo $test_group->test_price; ?>', '<?php echo $test_group->test_time; ?>')" type="checkbox" value="<?php echo $test_group->test_group_id; ?>" />
                    </td>
                    <!-- <td><?php echo $test_group->test_group_id; ?></td> -->
                    <td><strong style="margin-left:2px;">
                        <?php echo $test_group->test_group_name; ?>
                      </strong></td>

                    <!-- <td><?php
                              if ($test_group->category_id != 5) {
                                echo $test_group->test_time . " min";
                              }
                              ?></td> -->
                    <td><?php echo $test_group->test_price; ?> Rs.</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <script>
              $(document).ready(function() {
                $('#testGroupsTable').DataTable({
                  "pageLength": 10,
                  "lengthChange": false

                });
              });
              $(document).ready(function() {
                $('#receipts_table').DataTable({
                  "paging": false,
                  "lengthChange": false,
                  "sorting": false
                });
              });
            </script>
          </div>



        </div>



      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="box border blue" id="messenger">
      <div class="box-title">
        <h4><i class="fa fa-file"></i>Receipt / LAB Tests Status and Reports</h4>
      </div>
      <div class="box-body" style="font-size: 12px !important;">
        <div style="border-radius: 5px; border: 1px solid gray; padding:5px; margin-bottom: 5px;">
          <table width="100%">
            <tr>
              <td><span style="cursor: pointer;" class="pull-left" onclick="get_today_progress_report()">
                  <i class="fa fa-bar-chart" aria-hidden="true"></i> Today Report</span></td>
              <td><span class="pull-right">
                  <strong> Search Old Records</strong> <input onkeyup="get_patient_search_result()" type="text" name="search" id="search" class="f orm-control" placeholder="Search by ID or Name" style="width: 150px; display: inline;" />

                </span></td>
            </tr>
          </table>

          <div id="search_result"></div>
        </div>
        <script>
          function get_today_progress_report() {
            $.ajax({
              type: "POST",
              url: "<?php echo site_url(ADMIN_DIR); ?>/reception/today_progress_report/",
              data: {}
            }).done(function(data) {

              $('#test_form_title').html('Today Report');
              $('#test_form_body').html(data);
              $('#test_form').modal('show');


            });
          }
        </script>


        <script>
          function get_patient_search_result() {
            var search = $('#search').val();
            if (search != "") {
              $.ajax({
                type: "POST",
                url: "<?php echo site_url(ADMIN_DIR); ?>/lab/get_patient_search_result/",
                data: {
                  search: search
                }
              }).done(function(data) {
                $('#search_result').html(data);
              });
            } else {
              $('#search_result').html("");
            }
          }
        </script>


        <table class="table table-bordered" id="receipts_table" style="margin-top: -10px;">
          <thead>
            <tr>
              <th>#</th>
              <th>Token</th>
              <th>Name</th>

              <th>Gender</th>
              <th>Receipts</th>
              <!-- <th>Price</th>
              <th>Discount</th> -->
              <th>Rs:</th>
              <th>Status</th>
            </tr>
          </thead>
          <?php foreach ($all_tests as $test) {
            $color = '';
            if ($test->status == 1) {
              $color = "#E9F1FC";
            }
            if ($test->status == 2) {
              $color = "#ffe8e7";
            }
            if ($test->status == 3) {
              $color = "#F0FFF0";
            }

          ?>
            <tr style="background-color: <?php echo $color; ?>;
            
            <?php if ($test->is_deleted == 1) { ?>
              text-decoration: line-through;
            <?php }  ?>
            ">
              <td><?php echo $test->invoice_id; ?> </td>
              <td><?php
                  if ($test->category_id != 5) {
                    echo $test_categories[$test->category_id] . "-" . $test->today_count;
                  } else {
                    $query = "SELECT test_group_name FROM test_groups WHERE test_group_id = '" . $test->opd_doctor . "'";
                    $opd_doctor = $this->db->query($query)->result()[0]->test_group_name;
                    echo $opd_doctor . "-" . $test->today_count;
                  } ?>
              </td>
              <td><a href="#" onclick="update_patient_detail('<?php echo $test->patient_id; ?>')"><?php echo $test->patient_name; ?></a></td>
              <!-- <td><?php echo $test->patient_mobile_no; ?></td> -->
              <td><?php echo $test->patient_gender; ?></td>
              <td>
                <a style="margin-left: 10px;" target="new" href="<?php echo site_url(ADMIN_DIR . "lab/print_patient_test_receipts/$test->invoice_id") ?>"><i class="fa fa-print" aria-hidden="true"></i> Receipt</a>

              </td>
              <!-- <td><?php echo $test->price; ?></td>
              <td><?php echo $test->discount; ?></td> -->
              <td><?php echo $test->total_price; ?></td>
              <td>
                <?php //if ($test->category_id == 1) {
                if (1 == 1) {
                  if ($test->status == 1 and $test->is_deleted == 0) { ?>
                    New
                    <?php if ($this->session->userdata('user_id') == 3) { ?>
                      <a target="new" href="<?php echo site_url(ADMIN_DIR . "lab/delete_invoice/$test->invoice_id") ?>" class="pull-right"><i class="fa fa-times" style="color:red"></i> Cancel</a>
                    <?php } ?>
                  <?php } ?>
                  <?php if ($test->status == 2) { ?>
                    Inprogress
                  <?php  } ?>
                  <?php if ($test->status == 3 and $test->opd_doctor != 4) { ?>
                    <?php if ($test->status == 3 and $test->opd_doctor == 1) { ?>
                      <a style="margin-left: 10px;" target="new" href="<?php echo site_url(ADMIN_DIR . "ultrasounds/print_patient_test_report/$test->invoice_id") ?>"><i class="fa fa-print" aria-hidden="true"></i> Print Report</a>
                    <?php } else { ?>
                      <a style="margin-left: 10px;" target="new" href="<?php echo site_url(ADMIN_DIR . "lab/print_patient_test_report/$test->invoice_id") ?>"><i class="fa fa-print" aria-hidden="true"></i> Print Report</a>
                    <?php } ?>
                <?php  }
                }  ?>
              </td>
            </tr>
          <?php } ?>
        </table>

      </div>
    </div>
  </div>
</div>


<?php echo form_close(); ?>


<div id="information_model" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" onclick="$('#information_model').modal('hide');">&times;</button>
        <h4 class="modal-title" id="information_model_title">Modal Header</h4>
      </div>
      <div class="modal-body" id="information_model_body" style="text-align:center !important">
        <div></div>
        <h3 id="invoice_id"></h3>
        <h4 id="patient_name"></h4>
        <div id="other_info" style="margin-bottom:10px; border:1px dashed #666666; border-radius:5px; "></div>
        <form action="<?php echo site_url(ADMIN_DIR . 'reception/save_and_process') ?>" method="post">
          <input type="hidden" value="" name="invoice_id" id="invoiceid" />
          <input type="hidden" value="" name="patient_group_test_ids" id="patientgrouptestids" />
          <input required="required" placeholder="Enter test token ID" type="hidden" name="test_token_id" value="<?php echo time(); ?>" />
          <input type="submit" value="Save and Process" name="save_and_process" />
        </form>
      </div>
      <div class="modal-footer" style="display: none">
        <button type="submit" class="btn btn-success">Save</button>
      </div>
    </div>
  </div>
</div>



<div id="test_form" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:90%">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" onclick="$('#test_form').modal('hide');">&times;</button>
        <h4 class="modal-title" id="test_form_title">Test Report</h4>
      </div>
      <div class="modal-body" id="test_form_body" style="text-align:center !important">

      </div>
      <div class="modal-footer" style="display: none">
        <button type="submit" class="btn btn-success">Save</button>
      </div>
    </div>
  </div>
</div>

<div id="update_patient_detail" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Patient Detail
          <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </h5>

      </div>
      <div class="modal-body" id="patient_detail_body">
        <p>Modal body text goes here.</p>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  function update_patient_detail(patient_id) {
    $.ajax({
      type: "POST",
      url: "<?php echo site_url(ADMIN_DIR); ?>/reception/get_patient_detail_by_id/",
      data: {
        patient_id: patient_id
      }
    }).done(function(data) {
      $('#patient_detail_body').html(data);

      $('#update_patient_detail').modal('show');

    });


  }

  function test_token(invoice_id, Patient_name, other_info) {
    // $('#information_model_body').html('<div style="padding: 32px; text-align: center;"><img  src="<?php echo site_url('assets/admin/preloader.gif'); ?>" /></div>');
    //alert(invoice_id);
    $('#information_model_title').html('Assign Test Token ID');
    $('#invoice_id').html("Invoice No: " + invoice_id);
    $('#patient_name').html("Patient Name: " + Patient_name);
    $('#other_info').html($('#in_' + invoice_id).val());
    $('#patientgrouptestids').val($('#patient_group_test_ids_' + invoice_id).val());
    $('#invoiceid').val(invoice_id);

    $('#information_model').modal('show');

  }

  function get_patient_test_form(invoice_id) {

    $.ajax({
      type: "POST",
      url: "<?php echo site_url(ADMIN_DIR); ?>/lab/get_patient_test_form/",
      data: {
        invoice_id: invoice_id
      }
    }).done(function(data) {
      $('#test_form_body').html(data);
      $('#test_form').modal('show');
    });
  }

  function get_patient_test_report(invoice_id) {

    $.ajax({
      type: "POST",
      url: "<?php echo site_url(ADMIN_DIR); ?>/lab/get_patient_test_report/",
      data: {
        invoice_id: invoice_id
      }
    }).done(function(data) {
      $('#test_form_body').html(data);
      $('#test_form').modal('show');
    });
  }


  prices = [];
  var test_total_price = 0;


  function set_price(test_group_id, test_group_name, test_price, test_time) {

    if (test_group_id <= 4) {
      test_total_price = 0;
      prices = [];
      $('#validate').val("");
      $('.test_list').each(function() {
        $(this).prop('checked', false)
      });

      if (test_group_id == 4) {
        $('#dr_app_list').show();
        $('.appointment_numbers').prop('required', true);
        $('#multiple_token_option').show();
        //single_token();
        $("#singleToken").prop("checked", true);
        $('#second_patient_for').hide();
        $('.patient2').attr('required', false);
      } else {
        $('#dr_app_list').hide();
        $('.appointment_numbers').prop('required', false);
        $('#multiple_token_option').hide();
        $("#singleToken").prop("checked", true);
        $('#second_patient_for').hide();
        $('.patient2').attr('required', false);
      }

    } else {
      <?php $query = "SELECT * FROM `test_groups` WHERE category_id=5";
      $test_group_names = $this->db->query($query)->result();
      foreach ($test_group_names as $test_group_name) {
      ?>
        delete prices['<?php echo $test_group_name->test_group_name; ?>'];
      <?php } ?>

      $('.appointments').each(function() {
        $(this).prop('checked', false)
      });

      $('#dr_app_list').hide();
      $('.appointment_numbers').prop('required', false);


    }


    if ($('#TG_' + test_group_id).is(':checked')) {
      $('#validate').val("1");
      test_total_price = 0;
      prices[test_group_name] = {
        'price': test_price,
        'test_time': test_time,
        'test_group_id': test_group_id
      };
    } else {
      test_total_price = 0;
      delete prices[test_group_name];
      $('#validate').val("");
    }
    // prices.forEach(element => console.log(element));
    var price_list = '<table class="table table-bordered"><tr><td>#</td><td>Test Name</td><td>Price</td></tr>';

    var count = 0;
    var $testGrouupIds = "";
    for (var key in prices) {

      if (prices.hasOwnProperty(key))
        count = parseInt(count) + 1;
      price_list += '<tr><td>' + count + '</td>';
      price_list += '<td>' + key + '</td>';
      price_list += '<td>' + prices[key].price + '</td>';
      price_list += '</td></tr>';
      test_total_price = parseInt(test_total_price) + parseInt(prices[key].price);
      $testGrouupIds = $testGrouupIds + prices[key].test_group_id + ',';


    }
    $('#testGroupIDs').val($testGrouupIds);
    price_list += '</table>';

    $('#test_price_list').html(price_list);
    $('#test_total_price').html(test_total_price + '.00 Rs.');
    var discount = $('#discount').val();

    $('#discount_total').html(discount + '.00 Rs.');
    var payable = parseInt(test_total_price) - discount;
    $('#payable').html(payable + '.00 Rs.');

    //prices.forEach(test_price_list_function);
    // for (i = 0; i < prices.length; i++) {
    //     console.log(numbers[i]);
    //   } 


  }

  function update_price_list() {
    $('#test_total_price').html(test_total_price + '.00 Rs.');
    var discount = $('#discount').val();

    $('#discount_total').html(discount + '.00 Rs.');
    var payable = parseInt(test_total_price) - discount;
    $('#payable').html(payable + '.00 Rs.');
    if (discount > 0) {
      $('#dicount_options').show();
      $('#discount_type_id').attr("required", true);
      $('#discount_ref_by').attr("required", true);
    } else {
      $('#dicount_options').hide();
      $('#discount_type_id').attr("required", false);
      $('#discount_ref_by').attr("required", false);
    }


  }

  function test_price_list_function(test_group_name, values) {
    //alert();
    //$('#test_price_list').html(price_list);
    console.log(test_group_name);
  }

  function single_token() {
    <?php
    $query = "SELECT * FROM `test_groups` WHERE  category_id=5 and test_group_id=4";
    $appinment = $this->db->query($query)->row(); ?>
    set_price('<?php echo $appinment->test_group_id; ?>', '<?php echo $appinment->test_group_name; ?>', '<?php echo $appinment->test_price; ?>', '<?php echo $appinment->test_time; ?>');
    $("#singleToken").prop("checked", true);
    $('#second_patient_for').hide();
    $('.patient2').attr('required', false);

  }

  function multiple_token() {
    set_price('<?php echo $appinment->test_group_id; ?>', '<?php echo $appinment->test_group_name; ?>', '<?php echo ($appinment->test_price * 2); ?>', '<?php echo $appinment->test_time; ?>');
    $("#multipleToken").prop("checked", true);
    $('#second_patient_for').show();
    $('.patient2').attr('required', true);
  }
</script>

<script src="<?php echo site_url("assets/" . ADMIN_DIR . "js/jquery.inputmask.bundle.min.js"); ?>" type="text/javascript"></script>
<script>
  $(document).ready(function() {

    $("body").on('focus', '#patient_mobile_no', function() {
      $(this).inputmask("0999 9999999");
    });

  });
</script>
<?php $this->load->view(ADMIN_DIR . "reception/reception_footer"); ?>