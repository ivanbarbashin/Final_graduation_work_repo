<?php

class Workout {
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

    public function __construct($conn, $workout_id, $weekday=0){
        if ($workout_id == 0){
            $this->holiday = true;
        }else{
            $select_sql = "SELECT * FROM workouts WHERE id=$workout_id";
            if ($select_result = $conn->query($select_sql)){
                $this->weekday = $weekday;
                foreach ($select_result as $item){
                    $this->id = $item['id'];
                    $this->loops = $item['loops'];
                    $exercises = json_decode($item['exercises']);
                    $reps = json_decode($item['reps']);
                    $approaches = json_decode($item['approaches']);
                    $this->name = $item['name'];
                    $this->creator = $item["creator"];
                    for ($i = 0; $i < count($exercises); $i++){
                        array_push($this->exercises, new User_Exercise($conn, $exercises[$i], $reps[$i], $approaches[$i]));
                    }
                }
            }else{
                echo $conn->error;
            }
            $select_result->free();
        }
    }

    public function set_muscles (){
        $muscles = array(
            "arms" => 0,
            "legs" => 0,
            "press" => 0,
            "back" => 0,
            "chest" => 0,
            "cardio" => 0,
            "cnt" => 0
        );
        foreach ($this->exercises as $exercise){
            foreach ($exercise->muscles as $muscle){
                $muscles[$muscle]++;
                $muscles['cnt']++;
            }
        }
        foreach ($muscles as $muscle=>$value){
            if ($value != 0){
                $this->muscles[$muscle] = round($value / $muscles['cnt'] * 100, 0);
            }
        }

        return $muscles;
    }

    public function get_id(){
        return $this->id;
    }

    public function print_exercises($conn){
        foreach ($this->exercises as $exercise){
            $exercise->print_it($conn);
        }
    }

    public function get_groups_amount(){
        $cnt = 0;
        foreach ($this->muscles as $key=>$value){
            if ($value!=0) $cnt++;
        }
        return $cnt;
    }

    public function print_workout_info($expand_buttons=0, $user_id=-1, $additional_info=false){
        $workout = false;
        ?>
        <section class="workouts-card__info">
            <?php
            if ($this->holiday){ ?>
                <div class="day-workouts__card-day-off">Выходной</div>
            <?php }else{ $workout = true; ?>
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
                <?php if ($expand_buttons == 1){ ?>
                <div class="day-workouts__card-buttons">
                    <div class="workouts-card__info-line"></div>
                    <?php if ($additional_info){ ?>
                        <img class="day-workouts__card-img" src="../img/done.svg" alt="">
                    <?php }else{ ?>
                        <img class="day-workouts__card-img" src="../img/not_done.svg" alt="">
                    <?php }?>
                </div>
            <?php } else if ($expand_buttons == 2){ ?>
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
        return $workout;
    }

    public function print_workout_info_block($day, $expand_buttons=0, $user_id=-1, $is_done=false, $button=''){ ?>
        <section class="day-workouts__card">
            <h3 class="day-workouts__card-title"><?php echo get_day($day); ?></h3>
            <div class="day-workouts__card-content">
                <?php $this->print_workout_info($expand_buttons, $user_id, $is_done); ?>
            </div>
            <?php if ($button != '') echo $button; ?>
        </section>
    <?php
    }

    public function is_done($conn, $user_id, $day){
        $next_day = $day + 86400;
        $sql = "SELECT id FROM workout_history WHERE user=$user_id AND date_completed >= $day AND date_completed < $next_day";
        if ($result = $conn->query($sql)){
            if ($result->num_rows > 0){
                return true;
            }
        }else{
            echo $conn->error;
        }
        return false;
    }
}

class Control_Workout extends Workout{
    private $is_done = false;
    public $date;

    public function __construct($conn, $workout_id){
        if ($workout_id == 0){
            $this->holiday = true;
        }else{
            $select_sql = "SELECT * FROM control_workouts WHERE id=$workout_id";
            if ($select_result = $conn->query($select_sql)){
                foreach ($select_result as $item){
                    $this->id = $item['id'];
                    $exercises = json_decode($item['exercises']);
                    $reps = json_decode($item['reps']);
                    $this->name = $item['name'];
                    $this->creator = $item["creator"];
                    $this->is_done = $item["is_done"];
                    $this->date = $item["date"];
                    $this->weekday = date("N", $item["date"]);
                    if ($this->is_done)
                        for ($i = 0; $i < count($exercises); $i++)
                            array_push($this->exercises, new User_Exercise($conn, $exercises[$i], $reps[$i]));
                    else
                        for ($i = 0; $i < count($exercises); $i++)
                            array_push($this->exercises, new User_Exercise($conn, $exercises[$i], 0));

                }
            }else{
                echo $conn->error;
            }
            $select_result->free();
        }
    }

    public function get_is_done()
    {
        return $this->is_done;
    }

    public function print_control_workout_info($expand_buttons = 0, $user_id = -1, $additional_info = false)
    {
        ?>
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
            <?php if (!$this->is_done) echo '<a class="button-text day-workouts__card-button day-workouts__card-button--start" href="control_workout_session.php?id='.$this->id.'"><p>Начать</p><img src="../img/arrow_white.svg" alt=""></a>'; ?>
        </section>
        <?php
    }

    public function print_control_exercises($conn, $user_data=NULL){
        foreach ($this->exercises as $exercise){
            if ($user_data != NULL)
                $exercise->print_control_exercise($conn, $exercise->is_featured($user_data));
            else
                $exercise->print_control_exercise($conn);
        }
    }
}
