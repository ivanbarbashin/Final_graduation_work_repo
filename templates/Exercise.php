<?php

class Exercise {
    private $id;
    public $name;
    public $static;
    public $description="";
    public $muscles=[];
    private $image=0;
    public $ratings = array();
    public $difficulty=3;
    private $creator=1;

    public function set_rating($conn){
        $sql = "SELECT user, rate FROM exercise_ratings WHERE exercise=".$this->id;
        if ($result = $conn->query($sql)){
            foreach ($result as $rate) {
                $this->ratings[$rate["user"]] = $rate["rate"];
            }
        }else
            echo $conn->error;
    }

    public function is_rated($user_id){
        foreach ($this->ratings as $key=>$value)
            if ($key == $user_id) return 1;
        return 0;
    }

    public function get_rating(){
        if (count($this->ratings) == 0)
            return 0;
        $cnt = 0;
        $rating = 0;
        foreach ($this->ratings as $key=>$value){
            $cnt++;
            $rating += $value;
        }
        return round($rating/$cnt, 1);
    }

    public function set_exercise_data($select_result){
        foreach ($select_result as $item){
            $this->id = $item['id'];
            $this->name = $item['name'];
            $this->description = $item['description'];
            $this->muscles = json_decode($item['muscles']);
            $this->image = $item['image'];
            $this->static = $item['static'];
            $this->creator = $item['creator'];
            $this->difficulty = $item["difficulty"];
        };
    }

    public function __construct($conn, $id=0){
        if ($id != 0){
            $select_sql = "SELECT * FROM exercises WHERE id=$id";
            if ($select_result = $conn->query($select_sql)){
                $this->set_exercise_data($select_result);
                $this->set_rating($conn);
            }else{
                echo $conn->error;
            }
            $select_result->free();
        }
    }

    public function get_id(){
        return $this->id;
    }

    public function get_image($conn){
        $select_sql = "SELECT file FROM exercise_images WHERE id=$this->image";
        if ($result_sql = $conn->query($select_sql)){
            foreach ($result_sql as $item){
                $image = $item['file'];
            }
        }else{
            $image=null;
            echo $conn->error;
        }

        return $image;
    }

    public function update_rating($conn){

    }

    public function is_featured($user){
        if (in_array($this->id, $user->featured_exercises)){
            return true;
        }
        return false;
    }
    public function is_mine($user){
        if (in_array($this->id, $user->my_exercises)){
            return true;
        }
        return false;
    }

    public function print_it($conn, $is_featured=false, $is_mine=false, $construct=false, $is_current=false){
        if ($this->description == ""){
            $description = "Нет описания";
        }else{
            $description = $this->description;
        }

        if ($construct){
            $button = '<button class="button-text exercise-item__add" name="add" value="'.$this->id.'" type="button"><p>Добавить</p> <img src="../img/add.svg" alt=""></button>';
        }else{
            if ($is_mine){
                $button = '<button class="button-text exercise-item__delite" name="delete" value="'.$this->id.'"><p>Удалить</p> <img src="../img/delete.svg" alt=""></button>';
            }else{
                $button = '<button class="button-text exercise-item__add" name="add" value="'.$this->id.'"><p>Добавить</p> <img src="../img/add.svg" alt=""></button>';
            }
        }

        if ($is_featured){
            $button_featured = '<button class="exercise-item__favorite exercise-item__favorite--selected" name="featured" value="'.$this->id.'"><img src="../img/favorite_added.svg" alt=""></button>';
        }else{
            $button_featured = '<button class="exercise-item__favorite" name="featured" value="'.$this->id.'"><img src="../img/favorite.svg" alt=""></button>';
        }

        $muscle_list = "";
        foreach ($this->muscles as $muscle){
            $muscle_list .= translate_group($muscle) . " ";
        }
        $muscle_list = str_replace(' ', '-', trim($muscle_list));

        $replaces = array(
            "{{ button }}" => $button,
            "{{ button_featured }}" => $button_featured,
            "{{ image }}" => $this->get_image($conn),
            "{{ name }}" => $this->name,
            "{{ rating }}" => $this->get_rating(),
            "{{ difficulty }}" => $this->difficulty,
            "{{ id }}" => $this->id,
            "{{ muscle }}" => $muscle_list,
            "{{ description }}" => $description
        );
        echo render($replaces, "../templates/exercise.html");
    }
}

class User_Exercise extends Exercise {
    public $reps;
    public $approaches;

    public function __construct($conn, $id = 0, $reps = 0, $approaches = 0) {
        parent::__construct($conn, $id);
        $this->reps = $reps;
        $this->approaches = $approaches;
    }

    public function print_it($conn, $is_featured=false, $is_mine=false, $construct=false, $is_current=false){
        if ($this->description == ""){
            $description = "Нет описания";
        }else{
            $description = $this->description;
        }
        if ($is_featured){
            $button_featured = '<button class="exercise-item__favorite exercise-item__favorite--selected" name="featured" value="'.$this->get_id().'"><img src="../img/favorite_added.svg" alt=""></button>';
        }else{
            $button_featured = '<button class="exercise-item__favorite" name="featured" value="'.$this->get_id().'"><img src="../img/favorite.svg" alt=""></button>';
        }
        $muscle_list = "";
        foreach ($this->muscles as $muscle){
            $muscle_list .= translate_group($muscle) . " ";
        }
        $muscle_list = str_replace(' ', '-', trim($muscle_list));
        $btn_done = '';
        if ($is_current)
            $btn_done = '<button class="button-text exercise-item__done"><p>Подход сделан</p><img src="../img/done_white.svg" alt=""></button>';

        $replaces = array(
            "{{ button_featured }}" => $button_featured,
            "{{ image }}" => $this->get_image($conn),
            "{{ name }}" => $this->name,
            "{{ rating }}" => $this->get_rating(),
            "{{ difficulty }}" => $this->difficulty,
            "{{ id }}" => $this->get_id(),
            "{{ muscle }}" => $muscle_list,
            "{{ reps }}" => $this->reps,
            "{{ approaches }}" => $this->approaches,
            "{{ description }}" => $description,
            "{{ button_done }}" => $btn_done
        );
        echo render($replaces, "../templates/user_exercise.html");
    }

    public function print_control_exercise($conn, $is_featured=false, $current=false, $construct=false){
        if ($this->description == ""){
            $description = "Нет описания";
        }else{
            $description = $this->description;
        }
        $muscle_list = "";
        foreach ($this->muscles as $muscle){
            $muscle_list .= translate_group($muscle) . " ";
        }
        $muscle_list = str_replace(' ', '-', trim($muscle_list));
        $inp = '<div class="exercise-item__repetitions"><p class="exercise-item__repetitions-score">Нет данных</p></div>';
        if ($current) {
            $inp = '<p class="exercise-item__repetitions-score"><input class="exercise-item__input" type="number" placeholder="результат" name="reps[]"></p>';
        }

        if ($construct)
            $inp = "<div class='exercise-item__buttons'><input type='hidden' name='exercise' value='".$this->get_id()."'><button class='button-text exercise-item__add'><p>Добавить</p><img src='../img/add.svg' alt=''></button></div>";

        if (!$construct && !$current && $this->reps > 0)
            $inp =  '<div class="exercise-item__repetitions"><p class="exercise-item__repetitions-score">'.$this->reps.'</p></div>';
        if (!$current)
            if ($is_featured)
                $button_featured = '<button class="exercise-item__favorite exercise-item__favorite--selected" name="featured" value="'.$this->get_id().'"><img src="../img/favorite_added.svg" alt=""></button>';
            else
                $button_featured = '<button class="exercise-item__favorite" name="featured" value="'.$this->get_id().'"><img src="../img/favorite.svg" alt=""></button>';
        else
            $button_featured = '';

        $replaces = array(
            "{{ image }}" => $this->get_image($conn),
            "{{ name }}" => $this->name,
            "{{ rating }}" => $this->get_rating(),
            "{{ difficulty }}" => $this->difficulty,
            "{{ muscle }}" => $muscle_list,
            "{{ description }}" => $description,
            "{{ input }}" => $inp,
            "{{ button_featured }}" => $button_featured
        );
        echo render($replaces, "../templates/control_exercise.html");
    }
}