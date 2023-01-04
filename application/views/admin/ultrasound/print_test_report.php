<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>
    <?php $query = "SELECT test_group_name FROM test_groups WHERE test_group_id = '" . $invoice_detail->opd_doctor . "'";
    echo $title = $this->db->query($query)->result()[0]->test_group_name . " Report "; ?> <?php echo $invoice_detail->patient_id; ?>-INo:<?php echo $invoice_detail->invoice_id; ?>
  </title>
  <link rel="stylesheet" href="style.css">
  <link rel="license" href="http://www.opensource.org/licenses/mit-license/">
  <script src="script.js"></script>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="stylesheet" type="text/css" href="<?php echo site_url("assets/" . ADMIN_DIR); ?>/css/cloud-admin.css" media="screen,print" />
  <link rel="stylesheet" type="text/css" href="<?php echo site_url("assets/" . ADMIN_DIR); ?>/css/themes/default.css" media="screen,print" id="skin-switcher" />
  <link rel="stylesheet" type="text/css" href="<?php echo site_url("assets/" . ADMIN_DIR); ?>/css/responsive.css" media="screen,print" />
  <link rel="stylesheet" type="text/css" href="<?php echo site_url("assets/" . ADMIN_DIR); ?>/css/custom.css" media="screen,print" />


  <style>
    body {
      background: rgb(204, 204, 204);
      color: black;

    }


    page {
      background: white;
      display: block;
      margin: 0 auto;
      margin-bottom: 0.5cm;
      box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
    }

    page[size="A4"] {
      width: 21cm;
      /* height: 29.7cm;  */
      height: auto;
    }

    page[size="A4"][layout="landscape"] {
      width: 29.7cm;
      height: 21cm;
    }

    page[size="A3"] {
      width: 29.7cm;
      height: 42cm;
    }

    page[size="A3"][layout="landscape"] {
      width: 42cm;
      height: 29.7cm;
    }

    page[size="A5"] {
      width: 14.8cm;
      height: 21cm;
    }

    page[size="A5"][layout="landscape"] {
      width: 21cm;
      height: 14.8cm;
    }

    @media print {

      body,
      page {
        margin: 0;
        box-shadow: 0;
        color: black;

      }



    }


    .table>thead>tr>th,
    .table>tbody>tr>th,
    .table>tfoot>tr>th,
    .table>thead>tr>td,
    .table>tbody>tr>td,
    .table>tfoot>tr>td {
      padding: 4px;
      line-height: 1;
      vertical-align: top;
      border-top: 1px solid #ddd;
      font-size: 12px !important;
      color: black;
    }

    /* Styles go here */
    @media screen {
      .print-page-header {
        height: auto;
        display: none;
      }
    }




    @media screen {
      .page-footer {
        height: 50px;
        display: none;
      }
    }



    @media print {
      .page-footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        border-top: 1px solid gray;
        /* for demo */
        content: counter(page) " of " counter(pages);
        /* for demo */
      }

      .page-footer-space {
        height: 80px;

      }
    }

    @media screen {
      .page-footer {
        position: relative;

        width: 100%;
        border-top: 1px solid gray;
        /* for demo */
        display: block;
        /* for demo */
      }

      .page-footer-space {
        height: 80px;
        display: none;
      }
    }

    @media print {
      .print-page-header {
        position: fixed;
        top: 0mm;
        width: 100%;
        background: yellow;
        /* for demo */
        /* for demo */
      }

      .print-page-header-space {
        height: 90px;
      }
    }

    @media screen {
      .print-page-header {
        position: relative;
        top: 0mm;
        width: 100%;
        display: block;
        /* for demo */
        /* for demo */
      }

      .print-page-header-space {
        height: 90px;
        display: none;
      }
    }




    .page {
      page-break-after: always;
    }



    @page {
      margin: 20mm
    }

    @media print {
      thead {
        display: table-header-group;
      }

      tfoot {
        display: table-footer-group;
      }

      button {
        display: none;
      }

      body {
        margin: 0;
      }
    }
  </style>
</head>

<body>
  <page size='A4'>

    <div class="print-page-header" style="background-color: rgb(229, 228, 226) !important;">
      <table style="width:100%">
        <tr>
          <td style="padding-top: 10px;width: 90px !important;">
            <img src="<?php echo site_url("assets/uploads/" . $system_global_settings[0]->sytem_admin_logo); ?>" alt="<?php echo $system_global_settings[0]->system_title ?>" title="<?php echo $system_global_settings[0]->system_title ?>" style="width:80px !important" />
          </td>
          <td style="vertical-align: top; text-align:left; ">
            <h3 style="color:black; font-weight: bold"><?php echo $system_global_settings[0]->system_title ?> </h3>
            <h6 style="color:black; font-weight: bold"><?php echo $system_global_settings[0]->system_sub_title ?></h6>

          </td>
          <td style="text-align:left; vertical-align: middle;">
            <table style="font-size: smaller;">
              <tr>
                <td>Phone No: </td>
                <td><strong><?php echo str_replace(",", " - ", $system_global_settings[0]->phone_number) ?></strong></td>
              </tr>
              <tr>
                <td>Mobile No: </td>
                <td><strong><?php echo str_replace(",", " - ", $system_global_settings[0]->mobile_number) ?></strong></td>
              </tr>
              <tr>
                <td>Email:</td>
                <td><strong><?php echo $system_global_settings[0]->email_address; ?></strong></td>
              </tr>
            </table>

          </td>
        </tr>

      </table>
    </div>



    <div style="padding-left: 40px; padding-right: 40px; padding-top:0px !important;" contenteditable="true">

      <table style="width: 100%;" style="color:black">
        <thead>
          <tr>
            <th style="text-align: center;">
              <div class="print-page-header-space"></div>
              <p style="text-align:center">
              <h4 style="color:black; font-weight: bold"><?php echo $title; ?></h4>
              </p>
            </th>
          </tr>

        </thead>
        <tbody>

          <tr>
            <td>
              <table style="width: 100%; margin-top: 5px; margin-bottom: 10px;">
                <tr>
                  <td style="width: 40%;">

                    <div style="bor der: 1px dashed black; margin-top: 5px; padding:5px">
                      <table style="text-align: left; width:100%; font-size: 12px !important; color:black;">
                        <tr>
                          <th>Patient ID: <?php echo $invoice_detail->patient_id; ?></th>
                          <td>History No: <?php echo $invoice_detail->history_file_no; ?></td>
                        </tr>

                        <tr>
                          <th>Patient Name: </th>
                          <td><?php echo trim(ucwords(strtolower($invoice_detail->patient_name))); ?></td>
                        </tr>
                        <tr>
                          <th>Age / Sex: </th>
                          <td><?php echo @$invoice_detail->patient_age; ?> / <?php echo $invoice_detail->patient_gender; ?></td>
                        </tr>
                        <tr>
                          <th>Mobile No: </th>
                          <td><?php echo $invoice_detail->patient_mobile_no; ?></td>
                        </tr>
                        <tr>
                          <th>Address: </th>
                          <td><?php echo trim(ucwords(strtolower($invoice_detail->patient_address))); ?></td>
                        </tr>
                      </table>

                    </div>
                  </td>
                  <td style="width: 20%;"></td>
                  <td tyle="width: 40%;">
                    <div style="bor der: 1px dashed black; margin-top: 5px; padding:5px;">
                      <table style="text-align: left; width:100%; font-size: 12px !important; color:black">
                        <tr>
                          <th>Invoice No: </th>
                          <td> <?php echo $invoice_detail->invoice_id; ?></td>
                        </tr>
                        <tr>
                          <th>Refereed By:</th>
                          <td><?php echo str_replace("Muhammad", "M.", $invoice_detail->doctor_name) . "( " . $invoice_detail->doctor_designation . " )"; ?></td>
                        </tr>
                        <tr>
                          <th>Registered:</th>
                          <td><?php echo date("d M, Y h:i:s", strtotime($invoice_detail->created_date)); ?></td>
                        </tr>
                        <tr>
                          <th>Received:</th>
                          <td><?php echo date("d M, Y h:i:s", strtotime($invoice_detail->process_date)); ?></td>
                        </tr>
                        <tr>
                          <th>Reported:</th>
                          <td><?php echo date("d M, Y h:i:s", strtotime($invoice_detail->reported_date)); ?></td>
                        </tr>
                      </table>
                    </div>

                  </td>
                </tr>
              </table>
              <hr />
              <div class="report_remarks">
                <?php if ($invoice_detail->remarks) { ?>

                  <?php echo $invoice_detail->remarks; ?>
              </div>
            <?php } ?>
            <br />
            <br />
            <br />
            <?php

            $query = "SELECT `test_report_by` FROM `invoices` WHERE `invoice_id`= '" . $invoice_detail->invoice_id . "' ";
            $lab_technician_id = $this->db->query($query)->result()[0]->test_report_by;

            $query = "SELECT
                  `roles`.`role_title`,
                  `users`.`user_title`  
              FROM `roles`,
              `users` 
              WHERE `roles`.`role_id` = `users`.`role_id`
              AND `users`.`user_id`='" . $lab_technician_id . "'";
            $user_data = $this->db->query($query)->result()[0];
            ?>
            <div style="padding-left: 40px; padding-right: 40px; padding-top:0px !important; " contenteditable="true">
              <p class="divFooter" style="text-align: right;">
                <b><?php echo $user_data->user_title; ?> (<?php echo $user_data->role_title; ?>)</b>
                <br />
                <?php echo $system_global_settings[0]->system_title ?><br />Peshawar
              </p>
            </div>
            <br />

            </td>
          </tr>
          <tr>
            <td>

            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td>
              <div class="page-footer-space"></div>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
    <div class="page-footer" style="background-color: rgb(229, 228, 226) !important; border:1px solid rgb(229, 228, 226)">


      <p class="fixed-footer" style="text-align: center; background:#F9F9F9;">
        <strong><?php echo $system_global_settings[0]->address ?></strong>

        <br />
        <small>Print @ <?php echo date("d M, Y h:m:s A"); ?>
          by
          <?php
          $query = "SELECT
                      `roles`.`role_title`,
                      `users`.`user_title`  
                  FROM `roles`,
                  `users` 
                  WHERE `roles`.`role_id` = `users`.`role_id`
                  AND `users`.`user_id`='" . $this->session->userdata("user_id") . "'";
          $user_data = $this->db->query($query)->result()[0];
          ?>
          <?php echo $user_data->user_title; ?> (<?php echo $user_data->role_title; ?>)
        </small>
      </p>
    </div>
  </page>
</body>



</html>