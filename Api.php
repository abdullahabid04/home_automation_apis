<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Api extends REST_Controller {
  public function __construct() {
    parent::__construct();
    $this->load->model("api/HomeAuto", "wf");
  }

  public function customersignup_post() {
    $user_name = $this->security->xss_clean($this->input->post("user_name"));
    $e_mail = $this->security->xss_clean($this->input->post("e_mail"));
    $mobile_no = $this->security->xss_clean($this->input->post("mobile_no"));
    $address = $this->security->xss_clean($this->input->post("address"));
    $city = $this->security->xss_clean($this->input->post("city"));
    $password = $this->security->xss_clean($this->input->post("password"));
    
    $this->form_validation->set_rules("user_name", "user_name", "required");
    $this->form_validation->set_rules("e_mail", "e_mail", "required|valid_email|is_unique[waterfilter_accounts.e_mail]");
    $this->form_validation->set_rules("mobile_no", "mobile_no", "required|is_unique[waterfilter_accounts.mobile_no]");
    $this->form_validation->set_rules("address", "address", "required");
    $this->form_validation->set_rules("city", "city", "required");
    $this->form_validation->set_rules("password", "password", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors(),
        "user_id" => ""
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_name) && !empty($e_mail) && !empty($mobile_no) && !empty($password) && !empty($address)) {
        $signup_data = array(
          "user_name" => $user_name,
          "e_mail" => $e_mail,
          "mobile_no" => $mobile_no,
          "address" => $address,
          "city" => $city,
          "password" => $password
        );

        $data = $this->wf->signupcustomer($signup_data);

        if ($data['signup_status']) {
          $this->response(array(
            "status" => 1,
            "message" => "SignUp data inserted successfully",
            "user_id" => $data['user_id']
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Please enter valid credentials",
            "user_id" => ""
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "Please fill all fields correctly",
          "user_id" => ""
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }

  public function verifyaccount_post() {
    $mobile_no = $this->security->xss_clean($this->input->post("mobile_no"));
    $verification_code = $this->security->xss_clean($this->input->post("verification_code"));

    $this->form_validation->set_rules("mobile_no", "mobile_no", "required");
    $this->form_validation->set_rules("verification_code", "verification_code", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($mobile_no) && !empty($verification_code)) {

        $data = $this->wf->accountverify($mobile_no, $verification_code);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "Congratulations! Your account has been created"
          ), REST_Controller::HTTP_NOT_FOUND);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Oops! Verification code not matched try again"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }

  public function customerlogin_post() {
    $mobile_no = $this->security->xss_clean($this->input->post("mobile_no"));
    $password = $this->security->xss_clean($this->input->post("password"));

    $this->form_validation->set_rules("mobile_no", "mobile_no", "required");
    $this->form_validation->set_rules("password", "password", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors(),
        "user" => null
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($mobile_no) && !empty($password)) {

        $data = $this->wf->logincustomer($mobile_no, $password);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "Congratulations! You are logged in",
            "user" => $data['user_data']
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Oops! your credentials are wrong",
            "user" => null
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "Please fill all the fields",
          "user" => null
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function getmyprofile_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));

    $this->form_validation->set_rules("user_id", "user_id", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors(),
        "user" => null
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id)) {
    
        $data = $this->wf->getprofile($user_id);
    
        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "Profile found",
            "profile" => $data['profile']
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Profile not found",
            "profile" => null
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed",
          "profile" => null
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }

  public function updatemyprofile_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $user_name = $this->security->xss_clean($this->input->post("user_name"));
    $mobile_no = $this->security->xss_clean($this->input->post("mobile_no"));
    $city = $this->security->xss_clean($this->input->post("city"));
    $address = $this->security->xss_clean($this->input->post("address"));

    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("user_name", "user_name", "required");
    $this->form_validation->set_rules("mobile_no", "mobile_no", "required");
    $this->form_validation->set_rules("city", "city", "required");
    $this->form_validation->set_rules("address", "address", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors(),
        "user" => null
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($user_name) && !empty($mobile_no) && !empty($city) && !empty($address)) {
        $data = $this->wf->updateprofile($user_id, $user_name, $mobile_no, $city, $address);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "Profile updated successfully",
            "user" => $data['user'][0]
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Please enter valid credentials",
            "user" => null
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "Please fill all fields correctly",
          "user" => null
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function changepassword_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $old_password = $this->security->xss_clean($this->input->post("old_password"));
    $new_password = $this->security->xss_clean($this->input->post("new_password"));

    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("old_password", "old_password", "required");
    $this->form_validation->set_rules("new_password", "new_password", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($old_password) && !empty($new_password)) {
        $data = $this->wf->updatepassword($user_id, $old_password, $new_password);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "password updated successfully"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Please enter valid credentials"
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "Please fill all fields correctly"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function getapplink_post() {
    $os = $this->security->xss_clean($this->input->post("os"));

    $this->form_validation->set_rules("os", "os", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors(),
        "link" => ""
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($os)) {
        $data = $this->wf->applink($os);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "password updated successfully",
            "link" => $data['link']
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Please enter valid credentials",
            "link" => ""
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "Please fill all fields correctly",
          "link" => ""
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }

  public function requestchangepassword_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));

    $this->form_validation->set_rules("user_id", "user_id", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id)) {
        $data = $this->wf->requestchangepassword($user_id);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "password updated successfully"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Please enter valid credentials"
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "Please fill all fields correctly"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }

  public function verifyrequestpassword_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $code = $this->security->xss_clean($this->input->post("code"));

    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("code", "code", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($code)) {
        $data = $this->wf->verifyrequestpassword($user_id, $code);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "password updated successfully"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Please enter valid credentials"
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "Please fill all fields correctly"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function changeforgetpassword_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $password = $this->security->xss_clean($this->input->post("password"));

    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("password", "password", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty(!empty($password))) {
        $data = $this->wf->changeforgetpassword($user_id, $password);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "password updated successfully"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Please enter valid credentials"
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "Please fill all fields correctly"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function getcompanycontacts_get() {
    $data = $this->wf->getcontactscompany();
    if ($data['status']) {
      $this->response(array(
        "status" => 1,
        "message" => "Contacts fetched",
        "contactor" => $data['contactor']
      ), REST_Controller::HTTP_OK);
    } else {
      $this->response(array(
        "status" => 0,
        "message" => "Contacts not fetched",
        "contactor" => []
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }

  public function getmyreferrals_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors(),
        "referrals"  => null
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if(!empty($user_id)) {    
        $data = $this->wf->getreferrals($user_id);
        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "referrals found",
            "referrals" => $data['referrals']
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "referrals not found",
            "referrals" => null
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "all fields are needed",
          "referrals" => null
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function addnewreferral_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $referral_name = $this->security->xss_clean($this->input->post("referral_name"));
    $referral_mobile = $this->security->xss_clean($this->input->post("referral_mobile"));
    
    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("referral_name", "referral_name", "required");
    $this->form_validation->set_rules("referral_mobile", "referral_mobile", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($referral_name) && !empty($referral_mobile)) {
        $data = $this->wf->addreferral($user_id, $referral_name, $referral_mobile);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "referral added"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "referral not sent"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "all fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function sendcomplaints_post() {
    $mobile_no = $this->security->xss_clean($this->input->post("mobile_no"));
    $complain = $this->security->xss_clean($this->input->post("complain"));
    
    $this->form_validation->set_rules("mobile_no", "mobile_no", "required");
    $this->form_validation->set_rules("complain", "complain", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($mobile_no)) {
        $data = $this->wf->complainsend($mobile_no, $complain);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "Complain sent"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Complain not sent"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "all fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function makehome_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $home_name = $this->security->xss_clean($this->input->post("home_name"));
      
    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("home_name", "home_name", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($home_name)) {
        $data = $this->wf->createhome($user_id, $home_name);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "home created successfully"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "home not created"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function gethome_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    
    $this->form_validation->set_rules("user_id", "user_id", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors(),
        "total" => 0,
        "home" => null
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id)) {
        $data = $this->wf->readhome($user_id);
        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "homes found",
            "total" => count($data['data']),
            "home" => $data['data']
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "homes not found",
            "total" => 0,
            "home" => null
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed",
          "total" => 0,
          "home" => null
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function renamehome_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $home_id = $this->security->xss_clean($this->input->post("home_id"));
    $home_name = $this->security->xss_clean($this->input->post("home_name"));
    
    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("home_id", "home_id", "required");
    $this->form_validation->set_rules("home_name", "home_name", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($home_id) && !empty($home_name)) {
        $data = $this->wf->updatehome($user_id, $home_id, $home_name);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "home updated successfully"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "home not updated"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function removehome_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $home_name = $this->security->xss_clean($this->input->post("home_id"));
    
    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("home_id", "home_id", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($home_name)) {
        $data = $this->wf->deletehome($user_id, $home_name);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "home removed successfully",
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "home not removed",
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function makeroom_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $home_id = $this->security->xss_clean($this->input->post("home_id"));
    $room_name = $this->security->xss_clean($this->input->post("room_name"));
      
    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("home_id", "home_id", "required");
    $this->form_validation->set_rules("room_name", "room_name", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($home_id) && !empty($room_name)) {
        $data = $this->wf->createroom($user_id, $home_id, $room_name);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "room created successfully"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "room not created"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function getroom_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $home_id = $this->security->xss_clean($this->input->post("home_id"));
    
    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("home_id", "home_id", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors(),
        "total" => 0,
        "room" => null
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($home_id)) {
        $data = $this->wf->readroom($user_id, $home_id);
        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "room found",
            "total" => count($data['data']),
            "room" => $data['data']
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "room not found",
            "total" => 0,
            "room" => null
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed",
          "total" => 0,
          "room" => null
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function renameroom_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $room_id = $this->security->xss_clean($this->input->post("room_id"));
    $room_name = $this->security->xss_clean($this->input->post("room_name"));
    
    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("room_id", "room_id", "required");
    $this->form_validation->set_rules("room_name", "room_name", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($room_id) && !empty($room_name)) {
        $data = $this->wf->updateroom($user_id, $room_id, $room_name);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "room updated successfully",
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "room not updated",
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function removeroom_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));  
    $room_id = $this->security->xss_clean($this->input->post("room_id"));
      
    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("room_id", "room_id", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($room_id)) {
        $data = $this->wf->deleteroom($user_id, $room_id);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "room removed successfully",
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "room not removed",
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function makedevice_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $home_id = $this->security->xss_clean($this->input->post("home_id"));
    $room_id = $this->security->xss_clean($this->input->post("room_id"));
    $device_id = $this->security->xss_clean($this->input->post("device_id"));
    $device_name = $this->security->xss_clean($this->input->post("device_name"));
    $device_type = $this->security->xss_clean($this->input->post("device_type"));

    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("home_id", "home_id", "required");
    $this->form_validation->set_rules("room_id", "room_id", "required");
    $this->form_validation->set_rules("device_id", "device_id", "required");
    $this->form_validation->set_rules("device_name", "device_name", "required");
    $this->form_validation->set_rules("device_type", "device_type", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($device_id) && !empty($home_id) && !empty($room_id) && !empty($device_name) && !empty($device_type)) {
        $device_data1 = array(
            "user_id" => $user_id,
            "home_id" => $home_id,
            "room_id" => $room_id,
            "device_id" => $device_id,
            "device_name" => $device_name,
            "device_type" => $device_type
          );
        $device_data2 = array(
            "user_id" => $user_id,
            "home_id" => $home_id,
            "room_id" => $room_id,
            "user_role" => "admin",
            "shared" => 0,
            "device_id" => $device_id,
            "device_name" => $device_name,
            "active" => 0
          );
          
        $data = $this->wf->createdevice($device_data1, $device_data2);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "your device has been added successfully"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "device not added" 
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "Please fill all the fields"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function getdevice_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));

    $this->form_validation->set_rules("user_id", "user_id", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors(),
        "total" => null,
        "devices" => null
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id)) {
        $data = $this->wf->readdevice($user_id);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "Devices found",
            "total" => count($data['devices']),
            "devices" => $data['devices']
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Devices not found",
            "total" => null,
            "devices" => null
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed",
          "total" => null,
          "devices" => null
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }

  public function getdeviceinfo_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));

    $this->form_validation->set_rules("user_id", "user_id", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors(),
        "total" => null,
        "info" => null
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id)) {
        $data = $this->wf->readdeviceinfo($user_id);

        if ($data['status']) {
          $this->response(array(
            "status" => 1,
            "message" => "Devices found",
            "total" => count($data['devices']),
            "info" => $data['devices']
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Devices not found",
            "total" => null,
            "info" => null
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed",
          "total" => null,
          "info" => null
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function renamedevice_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $device_id = $this->security->xss_clean($this->input->post("device_id"));
    $device_name = $this->security->xss_clean($this->input->post("device_name"));

    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("device_id", "device_id", "required");
    $this->form_validation->set_rules("device_name", "device_name", "required");
    
    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($device_id) && !empty($device_name)) {
        $data = $this->wf->updatedevice($user_id, $device_id, $device_name);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "Device updated"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Device not updated"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }

  public function removedevice_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $device_id = $this->security->xss_clean($this->input->post("device_id"));
    
    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("device_id", "device_id", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($device_id)) {
        $data = $this->wf->deletedevice($user_id, $device_id);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "Device removed"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Device not removed"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function deviceonoff_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $device_id = $this->security->xss_clean($this->input->post("device_id"));
    $device_status = $this->security->xss_clean($this->input->post("device_status"));
    
    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("device_id", "device_id", "required");
    $this->form_validation->set_rules("device_status", "device_status", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($device_id) && !empty($device_status)) {
        $status = ($device_status == "on")? 1 : 0;
        $data = $this->wf->onoffdevice($user_id, $device_id, $status);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "Device powered $device_status"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Device not powered $device_status"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
  
  public function sharedevice_post() {
    $user_id = $this->security->xss_clean($this->input->post("user_id"));
    $shared_to_contact = $this->security->xss_clean($this->input->post("shared_to_contact"));
    $home_id = $this->security->xss_clean($this->input->post("home_id"));
    $room_id = $this->security->xss_clean($this->input->post("room_id"));
    $device_id = $this->security->xss_clean($this->input->post("device_id"));
    $device_name = $this->security->xss_clean($this->input->post("device_name"));
    $device_type = $this->security->xss_clean($this->input->post("device_type"));

    $this->form_validation->set_rules("user_id", "user_id", "required");
    $this->form_validation->set_rules("shared_to_contact", "shared_to_contact", "required");
    $this->form_validation->set_rules("home_id", "home_id", "required");
    $this->form_validation->set_rules("room_id", "room_id", "required");
    $this->form_validation->set_rules("device_id", "device_id", "required");
    $this->form_validation->set_rules("device_name", "device_name", "required");
    $this->form_validation->set_rules("device_type", "device_type", "required");

    if ($this->form_validation->run() === FALSE) {
      $this->response(array(
        "status" => 0,
        "message" => validation_errors()
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {
      if (!empty($user_id) && !empty($shared_to_contact) &&!empty($home_id) &&!empty($room_id) &&!empty($device_id) &&!empty($device_name) &&!empty($device_type)) {
        $data = $this->wf->deviceshare($user_id, $shared_to_contact, $home_id, $room_id, $device_id, $device_name, $device_type);

        if ($data) {
          $this->response(array(
            "status" => 1,
            "message" => "Device shared"
          ), REST_Controller::HTTP_OK);
        } else {
          $this->response(array(
            "status" => 0,
            "message" => "Device not shared"
          ), REST_Controller::HTTP_NOT_FOUND);
        }
      } else {
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }
}
