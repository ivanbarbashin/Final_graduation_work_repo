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

    public function set_rating($conn){ // set exercise rating
        $sql = "SELECT user, rate FROM exercise_ratings WHERE exercise=".$this->id;  // SQL query to select users and their respective ratings for a specific exercise
        if ($result = $conn->query($sql)){ // If the SQL query executes successfully
            foreach ($result as $rate) { // Looping through the fetched results
                $this->ratings[$rate["user"]] = $rate["rate"];  // Assigning user ratings to the exercise's 'ratings' property
            }
        }else
            echo $conn->error; // Outputting any database error messages if the query fails
    }

    public function is_rated($user_id){ // check if exercises is rated
        foreach ($this->ratings as $key=>$value)  // Checks if a user has rated the exercise
            if ($key == $user_id) return 1; // If the user has rated, returns 1 (true)
        return 0; // Returns 0 (false) if the user hasn't rated
    }

    public function get_rating(){ // get rating of exercise
        if (count($this->ratings) == 0) // Calculates and retrieves the average rating for the exercise
            return 0; // If there are no ratings, returns 0
        $cnt = 0; // Counter for the number of ratings
        $rating = 0; // Accumulator for total ratings
        foreach ($this->ratings as $key=>$value){
            $cnt++; // Incrementing the count for each rating
            $rating += $value; // Accumulating the total rating
        }
        return round($rating/$cnt, 1); // Calculates and returns the average rating, rounded to one decimal place
    }

    public function set_exercise_data($select_result){ // set data to exercise
        // Sets exercise data based on fetched data from the database
        foreach ($select_result as $item){ // Assigning properties with data from the database query result
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

    public function __construct($conn, $id=0){ // contructor for exercise
        if ($id != 0){
            $select_sql = "SELECT * FROM exercises WHERE id=$id"; // Fetches exercise data from the database based on the provided ID
            if ($select_result = $conn->query($select_sql)){ // Sets exercise data using the retrieved information
                $this->set_exercise_data($select_result);
                $this->set_rating($conn); // Sets exercise ratings
            }else{
                echo $conn->error; // Outputs an error if the query fails
            }
            $select_result->free();
        }
    }

    public function get_id(){ // get exercise's id
        return $this->id; // Returns the ID of the exercise
    }

    public function get_image($conn){
        $select_sql = "SELECT file FROM exercise_images WHERE id=$this->image"; // Retrieves the image file associated with the exercise
        if ($result_sql = $conn->query($select_sql)){
            foreach ($result_sql as $item){
                $image = $item['file']; // Assigns the image file name
            }
        }else{
            $image=null;
            echo $conn->error; // Outputs an error if the query fails
        }

        return $image; // Returns the image file name or null if there's an error
    }

    public function update_rating($conn){

    }

    public function is_featured($user){ // check if exercise is featured
        if (in_array($this->id, $user->featured_exercises)){ // Checks if the exercise is among the featured exercises of the user
            return true; // Returns true if the exercise is among the user's featured exercises
        }
        return false; // Returns false if it's not among the featured exercises
    }
    public function is_mine($user){ // check if exercise is mine
        if (in_array($this->id, $user->my_exercises)){ // Checks if the exercise is among the user's owned exercises
            return true; // Returns true if the exercise is among the user's owned exercises
        }
        return false; // Returns false if it's not among the owned exercises
    }

    public function print_it($conn, $is_featured=false, $is_mine=false, $construct=false, $is_current=false){ // print exercise card
        // set description
        if ($this->description == ""){
            $description = "Нет описания";
        }else{
            $description = $this->description;
        }

        if ($construct){ // for construct pages
            $button = '<button class="button-text exercise-item__add" name="add" value="'.$this->id.'" type="button"><p>Добавить</p> <img src="../img/add.svg" alt=""></button>';
        }else{
            if ($is_mine){ // if mine
                $button = '<button class="button-text exercise-item__delite" name="delete" value="'.$this->id.'"><p>Удалить</p> <img src="../img/delete.svg" alt=""></button>';
            }else{
                $button = '<button class="button-text exercise-item__add" name="add" value="'.$this->id.'"><p>Добавить</p> <img src="../img/add.svg" alt=""></button>';
            }
        }

        if ($is_featured){ // if featured
            $button_featured = '<button class="exercise-item__favorite exercise-item__favorite--selected" name="featured" value="'.$this->id.'"><img src="../img/favorite_added.svg" alt=""></button>';
        }else{
            $button_featured = '<button class="exercise-item__favorite" name="featured" value="'.$this->id.'"><img src="../img/favorite.svg" alt=""></button>';
        }

        $muscle_list = "";
        foreach ($this->muscles as $muscle){
            $muscle_list .= translate_group($muscle) . " "; // Translate each muscle name and concatenate them with spaces
        }
        $muscle_list = str_replace(' ', '-', trim($muscle_list)); // Replace spaces with hyphens and trim the resulting string

        $replaces = array( // Create an array of placeholders and their corresponding values
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
        echo render($replaces, "../templates/exercise.html"); // Render the HTML template by replacing placeholders with actual data
    }
}

class User_Exercise extends Exercise { // class of exercise with reps and approaches
    public $reps;
    public $approaches;

    public function __construct($conn, $id = 0, $reps = 0, $approaches = 0) { // contruct for class
        parent::__construct($conn, $id); // Call the parent class constructor to set up basic properties
        $this->reps = $reps; // The number of repetitions for the exercise
        $this->approaches = $approaches; // The number of approaches for the exercise
    }

    public function print_it($conn, $is_featured=false, $is_mine=false, $construct=false, $is_current=false){ // print exercise card
        // set description
        if ($this->description == ""){  
            $description = "Нет описания";
        }else{
            $description = $this->description;
        }
        if ($is_featured){ // check featured
            $button_featured = '<button class="exercise-item__favorite exercise-item__favorite--selected" name="featured" value="'.$this->get_id().'"><img src="../img/favorite_added.svg" alt=""></button>';
        }else{
            $button_featured = '<button class="exercise-item__favorite" name="featured" value="'.$this->get_id().'"><img src="../img/favorite.svg" alt=""></button>';
        }
        $muscle_list = "";
        foreach ($this->muscles as $muscle){
            $muscle_list .= translate_group($muscle) . " "; // Translate each muscle name and concatenate them with spaces
        }
        $muscle_list = str_replace(' ', '-', trim($muscle_list)); // Replace spaces with hyphens and trim the resulting string
        $btn_done = '';
        if ($is_current)
            $btn_done = '<button class="button-text exercise-item__done"><p>Подход сделан</p><img src="../img/done_white.svg" alt=""></button>';

        $replaces = array( // Create an array of placeholders and their corresponding values
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
        echo render($replaces, "../templates/user_exercise.html"); // Render the HTML template by replacing placeholders with actual data
    }

    public function print_control_exercise($conn, $is_featured=false, $current=false, $construct=false){ // same as print_it function, but get control exercise data and render control exercise template
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