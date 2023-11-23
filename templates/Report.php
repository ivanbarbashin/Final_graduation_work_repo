<?php

class Report {
    private $id;
    public $user;
    public $message;
    public $rate;
    public $date;

    public function get_id(){ // Method to get the report ID
        return $this->id;
    }

    public function __construct($user=-1, $message='', $rate=-1){ // Constructor to create a new report
        if ($message != '' && isset($rate) && $rate != -1){
            $this->user = $user;
            $this->message = $message;
            $this->rate = $rate;
            $this->date = time();
        }
    }

    public function dates(){ // Method to format and return the report date
        return date('j F, Y', $this->date); // return the report date
    }

    public function insert($conn){ // Method to insert the report into the database
        $error_array = array(
            "fill_all_the_fields" => false, // Indicator for required fields not filled
            "success_new_report" => false, // Indicator for successful report insertion
        );

        // Check if required fields are filled
        if ($this->message == "" || empty($this->rate) || $this->rate == -1){
            $error_array['fill_all_the_fields'] = true;
            return $error_array;
        }
        // Insert the report data into the database
        $insert_sql = "INSERT INTO reports(user, message, rate, date) VALUES($this->user, '$this->message', $this->rate, $this->date)";
        if ($conn -> query($insert_sql)){
            $error_array['success_new_report'] = true;
        }else{
            echo $conn->error; // Print database error if insertion fails
        }
        return $error_array; // Return the status of the report insertion
    }
}