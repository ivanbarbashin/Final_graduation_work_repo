<?php

class Workout { // class of workout
    private $id;
    public $exercises = array();
    public $loops = 1;
    public $weekday;
    public $muscles = array(
        "arms" => 0,
        "legs" => 0,
        "press" => 0,
        "back" => 0,
        "chest" => 0,
        "cardio" => 0,
        "cnt" => 0
    );
    public $holiday = false;
    public $name = '';
    private $creator = 0;

    public function __construct($conn, $workout_id, $weekday=0){ // constrictor of Workout class
        if ($workout_id == 0){ // Check if the workout ID is 0, indicating a holiday
            $this->holiday = true;
        }else{ // Fetch workout details from the database based on the provided workout ID
            $select_sql = "SELECT * FROM workouts WHERE id=$workout_id";
            if ($select_result = $conn->query($select_sql)){ // Execute the SQL query
                $this->weekday = $weekday;
                foreach ($select_result as $item){ // Iterate through the result set
                    // Assign fetched values to object properties
                    $this->id = $item['id'];
                    $this->loops = $item['loops'];
                    $exercises = json_decode($item['exercises']);
                    $reps = json_decode($item['reps']);
                    $approaches = json_decode($item['approaches']);
                    $this->name = $item['name'];
                    $this->creator = $item["creator"];
                    for ($i = 0; $i < count($exercises); $i++){ // Create User_Exercise objects and add them to the 'exercises' array
                        array_push($this->exercises, new User_Exercise($conn, $exercises[$i], $reps[$i], $approaches[$i]));
                    }
                }
            }else{
                echo $conn->error; // Output any database errors
            }
            $select_result->free(); // Free the result set after processing
        }
    }

    public function set_muscles (){ // set muscle groups
        $muscles = array(
            "arms" => 0,
            "legs" => 0,
            "press" => 0,
            "back" => 0,
            "chest" => 0,
            "cardio" => 0,
            "cnt" => 0
        );
        foreach ($this->exercises as $exercise){ // Iterate through each exercise in the workout
            foreach ($exercise->muscles as $muscle){
                $muscles[$muscle]++;
                $muscles['cnt']++;
            }
        }
        foreach ($muscles as $muscle=>$value){ // Calculate the percentage distribution of muscles worked
            if ($value != 0){
                $this->muscles[$muscle] = round($value / $muscles['cnt'] * 100, 0);
            }
        }

        return $muscles; // Return the muscle array
    }

    public function get_id(){ // det id of workout
        return $this->id; // return id
    }

    public function print_exercises($conn){ // print exercises array
        foreach ($this->exercises as $exercise){
            $exercise->print_it($conn); // print each exercise
        }
    }

    public function get_groups_amount(){ // get ammount of workout groups
        $cnt = 0;
        foreach ($this->muscles as $key=>$value){ // Loop through each muscle group in the $this->muscles array
            if ($value!=0) $cnt++; // If the value (percentage) for a muscle group is not zero, increment the counter
        }
        return $cnt; // Return the count of muscle groups worked in the workout
    }

    public function print_workout_info($expand_buttons=0, $user_id=-1, $additional_info=false){ // print info about workout
        $workout = false;
        // section for workout information?>
        <section class="workouts-card__info">
            <?php
            if ($this->holiday){ ?>
                <div class="day-workouts__card-day-off">Выходной</div>
            <?php }else{ $workout = true; // Set the workout to true since it's not a holiday ?>
                <h2 class="day-workouts__card-name"><?php echo $this->name; ?></h2>
                <div class="workouts-card__info-line"></div>
                <div class="workouts-card__muscle-groups">
                    <p class="workouts-card__item">Руки: <span><?php echo $this->muscles["arms"]; ?>%</span></p>
                    <p class="workouts-card__item">Ноги: <span><?php echo $this->muscles["legs"]; ?>%</span></p>
                    <p class="workouts-card__item">Грудь: <span><?php echo $this->muscles["chest"]; ?>%</span></p>
                    <p class="workouts-card__item">Спина: <span><?php echo $this->muscles["back"]; ?>%</span></p>
                    <p class="workouts-card__item">Пресс: <span><?php echo $this->muscles["press"]; ?>%</span></p>
                    <p class="workouts-card__item">Кардио: <span><?php echo $this->muscles["cardio"]; ?>%</span></p>
                </div>
                <?php if ($expand_buttons == 1){ // buttons for my program page ?>
                <div class="day-workouts__card-buttons">
                    <div class="workouts-card__info-line"></div>
                    <?php if ($additional_info){ ?>
                        <img class="day-workouts__card-img" src="../img/done.svg" alt="">
                    <?php }else{ ?>
                        <img class="day-workouts__card-img" src="../img/not_done.svg" alt="">
                    <?php }?>
                </div>
            <?php } else if ($expand_buttons == 2){ // buttons for workout page ?>
                <div class="day-workouts__card-buttons">
                    <?php
                    if ($additional_info){ ?>
                        <button class="button-text day-workouts__card-button day-workouts__card-button--time"><p>Таймер</p><img src="../img/time.svg" alt=""></button>
                    <?php } ?>
                </div>
                <?php if ($additional_info){ ?>
                    <a href="workout_session.php" class="button-text day-workouts__card-button day-workouts__card-button--start"><p>Начать</p><img src="../img/arrow_white.svg" alt=""></a>
                <?php } ?>
            <?php }
            }
            ?>
        </section>
            <?php
        return $workout; // Return the status of the workout
    }

    public function print_workout_info_block($day, $expand_buttons=0, $user_id=-1, $is_done=false, $button=''){ // print block for my program page ?>
        <section class="day-workouts__card">
            <h3 class="day-workouts__card-title"><?php echo get_day($day); ?></h3>
            <div class="day-workouts__card-content">
                <?php $this->print_workout_info($expand_buttons, $user_id, $is_done); ?>
            </div>
            <?php if ($button != '') echo $button; ?>
        </section>
    <?php
    }

    public function is_done($conn, $user_id, $day){ // check if workout is done or not
        $next_day = $day + 86400; // Calculate the next day timestamp
        $sql = "SELECT id FROM workout_history WHERE user=$user_id AND date_completed >= $day AND date_completed < $next_day"; // SQL query to check if the workout is completed
        if ($result = $conn->query($sql)){ // Execute the query
            if ($result->num_rows > 0){
                return true; // return that user has completed the workout for the specified day
            }
        }else{
            echo $conn->error;
        }
        return false; // return that user has not completed the workout for the specified day
    }
}

class Control_Workout extends Workout{ // class of control workout
    private $is_done = false;
    public $date;

    public function __construct($conn, $workout_id){ // constructor for control workout
        if ($workout_id == 0){ // Check if the workout_id is 0, indicating a holiday
            $this->holiday = true;
        }else{ // Fetch workout details from the database using the provided workout_id
            $select_sql = "SELECT * FROM control_workouts WHERE id=$workout_id";
            if ($select_result = $conn->query($select_sql)){ // Attempt to execute the SQL query
                foreach ($select_result as $item){ // Assign values to object properties from the fetched data
                    $this->id = $item['id'];
                    $exercises = json_decode($item['exercises']);
                    $reps = json_decode($item['reps']);
                    $this->name = $item['name'];
                    $this->creator = $item["creator"];
                    $this->is_done = $item["is_done"];
                    $this->date = $item["date"];
                    $this->weekday = date("N", $item["date"]);
                    if ($this->is_done) // Populate $this->exercises array with User_Exercise objects
                        for ($i = 0; $i < count($exercises); $i++)
                            array_push($this->exercises, new User_Exercise($conn, $exercises[$i], $reps[$i]));
                    else
                        for ($i = 0; $i < count($exercises); $i++)
                            array_push($this->exercises, new User_Exercise($conn, $exercises[$i], 0));

                }
            }else{
                echo $conn->error; // Display any database errors encountered during the query
            }
            $select_result->free(); // Free the result set
        }
    }

    public function get_is_done() // check if workout if fone
    {
        return $this->is_done; // return true or false
    }

    public function print_control_workout_info($expand_buttons = 0, $user_id = -1, $additional_info = false)  // print info about control workout
    {
        // section for workout information?>
        <section class="workouts-card__info">
                <h2 class="day-workouts__card-name"><?php echo $this->name; ?></h2>
                <div class="workouts-card__info-line"></div>
                <div class="workouts-card__muscle-groups">
                    <p class="workouts-card__item">Руки: <span><?php echo $this->muscles["arms"]; ?>%</span></p>
                    <p class="workouts-card__item">Ноги: <span><?php echo $this->muscles["legs"]; ?>%</span></p>
                    <p class="workouts-card__item">Грудь: <span><?php echo $this->muscles["chest"]; ?>%</span></p>
                    <p class="workouts-card__item">Спина: <span><?php echo $this->muscles["back"]; ?>%</span></p>
                    <p class="workouts-card__item">Пресс: <span><?php echo $this->muscles["press"]; ?>%</span></p>
                    <p class="workouts-card__item">Кардио: <span><?php echo $this->muscles["cardio"]; ?>%</span></p>
                </div>
            <div class="workouts-card__info-line"></div>
            <!-- if not done print start button -->
            <?php if (!$this->is_done) echo '<a class="button-text day-workouts__card-button day-workouts__card-button--start" href="control_workout_session.php?id='.$this->id.'"><p>Начать</p><img src="../img/arrow_white.svg" alt=""></a>'; ?>
        </section>
        <?php
    }

    public function print_control_exercises($conn, $user_data=NULL){
        foreach ($this->exercises as $exercise){ // Iterate through each exercise in the workout
            if ($user_data != NULL) // Check if $user_data is provided
                $exercise->print_control_exercise($conn, $exercise->is_featured($user_data)); // Print the control exercise based on whether it's featured for the user
            else
                $exercise->print_control_exercise($conn); // If $user_data is not provided, print the control exercise without considering featured status
        }
    }
}
