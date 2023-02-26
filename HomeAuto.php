<?php

defined('BASEPATH') or exit('No direct script access allowed');

class HomeAuto extends CI_Model {
    public function signupcustomer($data) {
        $query = "SELECT `id` FROM `waterfilter_accounts` ORDER BY `id` DESC limit 1";
        $exec_query = $this->db->query($query);
        if (count($exec_query->result()) > 0) {
            $id = $exec_query->result()[0]->id;
        } else {
            $id = 0;
        }
        $data['user_id'] = ($id + 1) . '-' . $data['user_name'] . '-' . $data['mobile_no'];
        $this->db->insert('waterfilter_accounts', $data);
        $signup_status = ($this->db->affected_rows() != 1) ? 0 : 1;
        $verification_code = rand(100000, 999999);

        $account_verification = array(
            "mobile_no" => $data['mobile_no'],
            "user_id" => $data['user_id'],
            "verification_code" => $verification_code,
            "verified" => 1
        );

        $this->db->insert('waterfilter_accountverification', $account_verification);
        $verification_status = ($this->db->affected_rows() != 1) ? 0 : 1;
        
        $home = array(
            "user_id" => $data['user_id'],
            "home_id" => "home1" . '-' . rand(1000, 9999),
            "home_name" => "home1"
            );
        
        $room = array(
            "user_id" => $data['user_id'],
            "home_id" => $home['home_id'],
            "room_id" => $home['home_id'] . '-' . "room1",
            "room_name" => "room1"
            );

        $this->db->insert('user_homes', $home);
        $this->db->insert('user_rooms', $room);

        $response['signup_status'] = $signup_status;
        $response['user_id'] = $data['user_id'];
        $response['code'] = $verification_code;

        return $response;
    }

    public function accountverify($mobile_no, $verification_code) {
        $query = "SELECT `verification_code` FROM `waterfilter_accountverification` WHERE `mobile_no` = '$mobile_no'";
        $exec_query = $this->db->query($query);

        if (count($exec_query->result()) > 0) {
            $code = $exec_query->result()[0]->verification_code;
            if ($verification_code === $code) {
                $set_status_query = "UPDATE `waterfilter_accountverification` SET `verified` = '1' WHERE `mobile_no` = '$mobile_no'";
                $exec_query_status = $this->db->query($set_status_query);
                $status = ($this->db->affected_rows() != 1) ? 0 : 1;
                if ($status) {
                    $response['status'] = 1;
                } else {
                    $response['status'] = 0;
                }
            } else {
                $response['status'] = 0;
            }
        } else {
            $response['status'] = 0;
        }

        return $response;
    }

    public function logincustomer($mobile_no, $password) {
        $query = "SELECT * FROM `waterfilter_accounts` WHERE `mobile_no` = '$mobile_no'";
        $exec_query = $this->db->query($query);
        $pass = $exec_query->result()[0]->password;
        $check_query = "SELECT `verified` FROM `waterfilter_accountverification` WHERE `mobile_no` = '$mobile_no'";
        $exec_check = $this->db->query($check_query);
        $verified = $exec_check->result()[0]->verified;
        
        if((count($exec_query->result()) > 0)&&(count($exec_check->result()) > 0)) {
            if(($verified) && ($pass === $password)) {
                $response['status'] = 1;
                $response['user_data'] = $exec_query->result()[0];
            }
        } else {
            $response['status'] = 0;
        }
        return $response;
    }
    
    public function getprofile($user_id) {
        $query = "SELECT * FROM `waterfilter_accounts` WHERE `user_id` = '$user_id'";
        $exec_query = $this->db->query($query);

        if (count($exec_query->result()) > 0) {
            $result['status'] = 1;
            $result['profile'] = $exec_query->result()[0];
        } else {
            $result['status'] = 0;
        }

        return $result;
    }

    public function updateprofile($user_id, $user_name, $mobile_no, $city, $address) {
        $query = "UPDATE `waterfilter_accounts` SET `user_name` = '$user_name', `mobile_no` = '$mobile_no', `city` = '$city', `address` = '$address' WHERE `user_id` = '$user_id'";
        $exec_query = $this->db->query($query);
        $query2 = "UPDATE `waterfilter_accountverification` SET `mobile_no` = '$mobile_no' WHERE `user_id` = '$user_id'";
        $exec_query2 = $this->db->query($query2);
        
        $status = 1;
        
        if($status) {
            $query1 = "SELECT * FROM `waterfilter_accounts` WHERE `user_id` = '$user_id'";
            $exec_query1 = $this->db->query($query1);
            
            $result['status'] = 1;
            $result['user'] = $exec_query1->result();
        } else {
            $result['status'] = 0;
            $result['user'] = null;
        }
        
        return $result;
    }
    
    public function updatepassword($user_id, $old_password, $new_password) {
        $query_ = "SELECT `password` FROM `waterfilter_accounts` WHERE `user_id` = '$user_id'";
        $exec_query_ = $this->db->query($query_);
        
        if(count($exec_query_->result()) > 0) {
            $password = $exec_query_->result()[0]->password;
            
            if ($password == $old_password) {
                $query = "UPDATE `waterfilter_accounts` SET `password` = '$new_password' WHERE `user_id` = '$user_id'";
                $exec_query = $this->db->query($query);
                $result['status'] = ($this->db->affected_rows() != 1) ? 0 : 1;
            } else {
                $result['status'] = 0;
            }
        } else {
            $result['status'] = 0;
        }
        return $result;
    }
    
    public function applink($os) {
        if ($os == "ios") {
            $ios_query = "SELECT `link` FROM `applinks` WHERE `os` = 'ios'";
            $exec_ios_query = $this->db->query($ios_query);
            if(count($exec_ios_query->resut()) > 0) {
                $result['status'] = 1;
                $result['link'] = $exec_ios_query-result()[0]->link;
            } else {
                $result['status'] = 0;
                $result['link'] = "";
            }
        } elseif ($os == "android") {
            $android_query = "SELECT `link` FROM `applinks` WHERE `os` = 'android'";
            $exec_android_query = $this->db->query($android_query);
            if(count($exec_ios_query->resut()) > 0) {
                $result['status'] = 1;
                $result['link'] = $exec_android_query-result()[0]->link;
            } else {
                $result['status'] = 0;
                $result['link'] = "";
            }
            
        } else {
            $result['status'] = 0;
            $result['link'] = "";
        }
    }

    public function getcontactscompany() {
        $query = "SELECT * FROM `waterfilter_contactus`";
        $exec_query = $this->db->query($query);

        if (count($exec_query->result()) > 0) {
            $result['status'] = 1;
            $result['contactor'] = $exec_query->result();
        } else {
            $result['status'] = 0;
        }
        return $result;
    }

    public function getreferrals($user_id) {
        $query = "SELECT * FROM `waterfilter_referralprogram` WHERE `user_id` = '$user_id'";
        $exec_query = $this->db->query($query);

        if (count($exec_query->result()) > 0) {
            $result['status'] = 1;
            $result['referrals'] = $exec_query->result();
        } else {
            $result['status'] = 0;
        }
        return $result;
    }
    
    public function addreferral($user_id, $referral_name, $referral_mobile) {
        $query = "SELECT * FROM `waterfilter_accounts` WHERE `user_id` = '$user_id'";
        $exec_query = $this->db->query($query);

        if (count($exec_query->result()) > 0) {
            $mobile_no = $exec_query->result()[0]->mobile_no;
            
            $data = array(
                "user_id" => $user_id,
                "mobile_no" => $mobile_no,
                "referral_name" => $referral_name,
                "referral_mobile" => $referral_mobile
                );
            $this->db->insert("waterfilter_referralprogram", $data);
            $status = ($this->db->affected_rows() != 1) ? 0 : 1;
        } else {
            $status = 0;
        }
        return $status;
    }

    public function complainsend($mobile_no, $complain) {
        $data = array(
            "mobile_no" => $mobile_no,
            "complaint" => $complain
        );

        $this->db->insert('waterfilter_complaints', $data);
        return ($this->db->affected_rows() != 1) ? 0 : 1;
    }
    
    public function createhome($user_id, $home_name){
        $home_id = $home_name . "-" . rand(100000, 999999);
        $data = array(
            "user_id" => $user_id,
            "home_id" => $home_id,
            "home_name" => $home_name
            );
        
        $this->db->insert("user_homes", $data);
        $result['status'] = ($this->db->affected_rows() != 1) ? 0 : 1;
        
        return $result;
    }
    
    public function readhome($user_id) {
        $query = "SELECT * FROM `user_homes` WHERE `user_id` = '$user_id'";
        $exec_query = $this->db->query($query);
        
        if(count($exec_query->result()) > 0){
            $result['status'] = 1;
            $result['data'] = $exec_query->result();
        }else{
            $result['status'] = 0;
        }
        
        return $result;
    }
    
    public function updatehome($user_id, $home_id, $home_name) {
        $query = "UPDATE `user_homes` SET `home_name` = '$home_name' WHERE `user_id` = '$user_id' AND `home_id` = '$home_id'";
        $exec_query = $this->db->query($query);
        return ($this->db->affected_rows() != 1) ? 0 : 1;
    }
    
    public function deletehome($user_id, $home_id) {
        $query = "DELETE FROM `user_homes` WHERE `user_id` = '$user_id' AND `home_id` = '$home_id'";
        $exec_query = $this->db->query($query);
        return ($this->db->affected_rows() != 1) ? 0 : 1;
    }
    
    public function createroom($user_id, $home_id, $room_name){
        $room_id = $home_id . '-' . $room_name;
        $data = array(
            "user_id" => $user_id,
            "home_id" => $home_id,
            "room_id" => $room_id,
            "room_name" => $room_name
            );
        $this->db->insert("user_rooms", $data);
        
        $result['status'] = ($this->db->affected_rows() != 1) ? 0 : 1;
        
        return $result;
    }
    
    public function readroom($user_id, $home_id) {
        $query = "SELECT * FROM `user_rooms` WHERE `user_id` = '$user_id' AND `home_id` = '$home_id'";
        $exec_query = $this->db->query($query);
        
        if(count($exec_query->result()) > 0){
            $result['status'] = 1;
            $result['data'] = $exec_query->result();
        }else{
            $result['status'] = 0;
        }
        
        return $result;
    }
    
    public function updateroom($user_id, $room_id, $room_name) {
        $query = "UPDATE `user_rooms` SET `room_name` = '$room_name' WHERE `user_id` = '$user_id' AND `room_id` = '$room_id'";
        $exec_query = $this->db->query($query);
        return ($this->db->affected_rows() != 1) ? 0 : 1;
    }
    
    public function deleteroom($user_id, $room_id) {
        $query = "DELETE FROM `user_rooms` WHERE `user_id` = '$user_id' AND `room_id` = '$room_id'";
        $exec_query = $this->db->query($query);
        return ($this->db->affected_rows() != 1) ? 0 : 1;
    }
    
    public function createdevice($data1, $data2) {
        $this->db->insert("user_devices", $data1);
        $this->db->insert("device_info", $data2);
        return ($this->db->affected_rows() != 1) ? 0 : 1;
    }
    
    public function readdevice($user_id) {
        $query = "SELECT * FROM `user_devices` WHERE `user_id` = '$user_id'";
        $exec_query = $this->db->query($query);

        if (count($exec_query->result()) > 0) {
            $result['status'] = 1;
            $result['devices'] = $exec_query->result();
        } else {
            $result['status'] = 0;
        }

        return $result;
    }
    
    public function readdeviceinfo($user_id) {
        $query = "SELECT * FROM `device_info` WHERE `user_id` = '$user_id'";
        $exec_query = $this->db->query($query);

        if (count($exec_query->result()) > 0) {
            $result['status'] = 1;
            $result['devices'] = $exec_query->result();
        } else {
            $result['status'] = 0;
        }

        return $result;
    }
 
    public function updatedevice($user_id, $device_id, $device_name) {
        $query = "UPDATE `user_devices` SET `device_name` = '$device_name' WHERE `user_id` = '$user_id' AND `device_id` = '$device_id'";
        $exec_query = $this->db->query($query);
        return ($this->db->affected_rows() != 1) ? 0 : 1;
    }
    
    public function deletedevice($user_id, $device_id) {
        $query1 = "DELETE FROM `user_devices` WHERE `user_id` = '$user_id' AND `device_id` = '$device_id'";
        $exec_query1 = $this->db->query($query1);
        $query2 = "DELETE FROM `device_info` WHERE `user_id` = '$user_id' AND `device_id` = '$device_id'";
        $exec_query2 = $this->db->query($query2);

        return ($this->db->affected_rows() != 1) ? 0 : 1;
    }
    
    public function deviceshare($user_id, $shared_to_contact, $home_id, $room_id, $device_id, $device_name, $device_type) {
        $query1 = "SELECt * FROM `waterfilter_accounts` WHERE `mobile_no` = '$shared_to_contact'";
        $exec_query1 = $this->db->query($query1);
        
        if(count($exec_query1->result()) > 0) {
            $shared_to_user_id = $exec_query1->result()[0]->user_id;
            $data1 = array(
                "user_id" => $shared_to_user_id,
                "home_id" => $home_id,
                "room_id" => $room_id,
                "device_id" => $device_id,
                "device_name" => $device_name,
                "device_type" => $device_type
            );
            $data2 = array(
                "user_id" => $shared_to_user_id,
                "home_id" => $home_id,
                "room_id" => $room_id,
                "user_role" => "user",
                "shared" => 1,
                "device_id" => $device_id,
                "device_name" => $device_name,
                "active" => 0
            );
            
            $this->db->insert("user_devices", $data1);
            $this->db->insert("device_info", $data2);
            
            $query2 = "UPDATE `device_info` SET `shared` = 1 WHERE `user_id` = '$user_id' AND `device_id` = '$device_id'";
            $exec_query2 = $this->db->query($query2);
            
            return ($this->db->affected_rows() != 1) ? 0 : 1;
        } else {
            return 0;
        }
    }
    
    public function onoffdevice($user_id, $device_id, $device_status) {
        $query = "UPDATE `device_info` SET `active` = '$device_status' WHERE `user_id` = '$user_id' AND `device_id` = '$device_id'";
        $exec_query = $this->db->query($query);
        return ($this->db->affected_rows() != 1) ? 0 : 1;
    }
}
