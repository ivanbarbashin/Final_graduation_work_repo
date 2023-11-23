<?php

class Program {
    private $id;
    public $name;
    public $program; # []
    public $rating;
    public $workouts=array();
    public $muscles=array();
    public $weeks=1;
    public $date_start;

    public function __construct($conn, $id){ // Constructor to initialize the Program
        if ($id != 0){
            $select_sql = "SELECT * FROM programs WHERE id=$id";
            if ($select_result = $conn->query($select_sql)){
                foreach ($select_result as $item){
                    $this->id = $item['id'];
                    $this->name = $item['name'];
                    $this->program = json_decode($item['program']); // Decoding program data from JSON
                    $this->rating = $item['rating'];
                }
            }else{
                echo $conn->error; // Output database error if query fails
            }
            $select_result->free(); // free up the result set
        }else{
            $this->id = $id;
        }
    }

    public function get_id(){ // Method to retrieve the program ID
        return $this->id; // ruturn program id
    }

    public function set_workouts($conn){ // Method to set workouts associated with the program
        for ($i = 0; $i < 7; $i++){
            array_push($this->workouts, new Workout($conn, $this->program[$i], $i));
        }
    }

    public function count_workouts(){ // Method to count the total number of workouts in the program
        $cnt = 0;
        foreach ($this->program as $id){
            if ($id != 0){
                $cnt++;
            }
        }
        return $cnt * $this->weeks; // return number of workouts in the program
    }

    public function count_exercises(){ // Method to count the total number of exercises in the program
        $cnt = 0;
        foreach ($this->workouts as $workout){
            $cnt += count($workout->exercises);
        }
        return $cnt * $this->weeks; // return number of exercises in the program
    }

    public function print_program_info(){ // Method to print program information?>
        <section class="cover" navigation="true">
            <?php
            foreach ($this->workouts as $workout){ // print workout info items
                $workout->print_workout_info(true);
            }
            ?>
        </section>
    <?php }

    public function set_additional_data($conn, $user){ // Method to set additional data for the program (start date, weeks)
        $sql = "SELECT date_start, weeks FROM program_to_user WHERE user=$user AND (date_start + 604800 * weeks) > ".time()." LIMIT 1"; // SQL query to fetch program start date and duration (in weeks) for a specific user
        if ($result = $conn->query($sql)){ // If the SQL query executes successfully
            foreach ($result as $times){ // Iterating through the fetched result
                $this->date_start = $times['date_start']; // Assigning the 'date_start' value from the result to the object property
                $this->weeks = $times["weeks"]; // Assigning the 'weeks' value from the result to the object property
            }
        }else{
            echo $conn->error; // Output database error if query fails
        }

    }
}