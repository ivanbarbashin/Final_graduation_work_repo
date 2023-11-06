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

    public function __construct($conn, $id){
        if ($id != 0){
            $select_sql = "SELECT * FROM programs WHERE id=$id";
            if ($select_result = $conn->query($select_sql)){
                foreach ($select_result as $item){
                    $this->id = $item['id'];
                    $this->name = $item['name'];
                    $this->program = json_decode($item['program']);
                    $this->rating = $item['rating'];
                }
            }else{
                echo $conn->error;
            }
            $select_result->free();
        }else{
            $this->id = $id;
        }
    }

    public function get_id(){
        return $this->id;
    }

    public function set_workouts($conn){
        for ($i = 0; $i < 7; $i++){
            array_push($this->workouts, new Workout($conn, $this->program[$i], $i));
        }
    }

    public function count_workouts(){
        $cnt = 0;
        foreach ($this->program as $id){
            if ($id != 0){
                $cnt++;
            }
        }
        return $cnt * $this->weeks;
    }

    public function count_exercises(){
        $cnt = 0;
        foreach ($this->workouts as $workout){
            $cnt += count($workout->exercises);
        }
        return $cnt * $this->weeks;
    }

    public function print_program_info(){ ?>
        <section class="cover" navigation="true">
            <?php
            foreach ($this->workouts as $workout){
                $workout->print_workout_info(true);
            }
            ?>
        </section>
    <?php }

    public function set_additional_data($conn, $user){
        $sql = "SELECT date_start, weeks FROM program_to_user WHERE user=$user AND (date_start + 604800 * weeks) > ".time()." LIMIT 1";
        if ($result = $conn->query($sql)){
            foreach ($result as $times){
                $this->date_start = $times['date_start'];
                $this->weeks = $times["weeks"];
            }
        }else{
            echo $conn->error;
        }

    }
}