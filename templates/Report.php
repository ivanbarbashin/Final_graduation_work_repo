<?php

class Report {
    private $id;
    public $user;
    public $message;
    public $rate;
    public $date;

    public function get_id(){
        return $this->id;
    }

    public function __construct($user=-1, $message='', $rate=-1){
        if ($message != '' && isset($rate) && $rate != -1){
            $this->user = $user;
            $this->message = $message;
            $this->rate = $rate;
            $this->date = time();
        }
    }

    public function dates(){
        return date('j F, Y', $this->date);
    }

    public function insert($conn){
        $error_array = array(
            "fill_all_the_fields" => false,
            "success_new_report" => false,
        );

        if ($this->message == "" || empty($this->rate) || $this->rate == -1){
            $error_array['fill_all_the_fields'] = true;
            return $error_array;
        }
        $insert_sql = "INSERT INTO reports(user, message, rate, date) VALUES($this->user, '$this->message', $this->rate, $this->date)";
        if ($conn -> query($insert_sql)){
            $error_array['success_new_report'] = true;
        }else{
            echo $conn->error;
        }
        return $error_array;
    }
}