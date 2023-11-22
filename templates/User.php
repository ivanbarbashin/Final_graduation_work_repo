<?php



class User {
    private $id;
    public $login='';
    private $status="user";
    public $name="Guest";
    public $surname='';
    public $description='';
    public $avatar=1;
    private $password='';
    public $subscriptions = [];
    public $subscribers = [];
    private $auth=false;
    public $featured_exercises = [];
    public $my_exercises = [];
    public $program;
    public $workout_history = [];
    public $featured_workouts = [];
    public $type;
    public $preparation;
    public $coach = NULL; # only for sportsmen
    public $doctor = NULL; # only for sportsmen
    private $requests = []; # only for coaches and doctors
    private $sportsmen = []; # only for coaches and doctors
    public $phys_updates = array();
    public $vk = NULL;
    public $tg = NULL;

    function set_subscriptions($conn){
        $sql = "SELECT user FROM subs WHERE subscriber=$this->id";
        $this->subscriptions = array();
        if ($result = $conn->query($sql)){
            foreach ($result as $user){
                array_push($this->subscriptions, $user['user']);
            }
        }else{
            echo $conn->query;
        }
    }
    function set_subscribers($conn){
        $sql = "SELECT subscriber FROM subs WHERE user=$this->id";
        $this->subscribers = array();
        if ($result = $conn->query($sql)){
            foreach ($result as $user){
                array_push($this->subscribers, $user['subscriber']);
            }
        }else{
            echo $conn->query;
        }
    }

    function set_staff($conn){
        $id = $this->get_id();
        $sql = "SELECT (SELECT coach FROM user_to_coach WHERE user = $id) AS selected_coach, (SELECT doctor FROM user_to_doctor WHERE user = $id) AS selected_doctor";
        if ($result = $conn->query($sql)){
            foreach ($result as $item){
                if ($item["selected_coach"] != null)
                    $this->coach = new User($conn, $item["selected_coach"]);
                if ($item["selected_doctor"] != null)
                    $this->doctor = new User($conn, $item["selected_doctor"]);
                return 1;
            }
        }
        echo $conn->error;
        return 0;
    }

    public function get_requests(){
        return $this->requests;
    }

    public function get_sportsmen(){
        return $this->sportsmen;
    }
    public function get_sportsmen_advanced($conn){
        $return_val = array(); // Initialize an empty array to store advanced sportsmen data

        // Iterate through the list of sportsmen using the get_sportsmen() method
        foreach ($this->get_sportsmen() as $sportsman)
            array_push($return_val, new User($conn, $sportsman)); // Create a new User object for each sportsman using the provided database connection ($conn)

        return $return_val; // Return an array containing advanced User objects for each sportsman
    }

    public function __construct($conn, $id=-1, $auth=false){
        if (isset($id) && $id != -1) {
            $select_sql = "SELECT * FROM users WHERE id=$id LIMIT 1";
            if ($select_result = $conn->query($select_sql)) {
                foreach ($select_result as $item) {
                    $this->id = $item['id'];
                    $this->login = $item['login'];
                    $this->name = $item['name'];
                    $this->surname = $item['surname'];
                    $this->status = $item['status'];
                    $this->avatar = $item['avatar'];
                    $this->password = $item['password'];
                    $this->description = $item['description'];
                    $this->featured_exercises = json_decode($item['featured_exercises']);
                    $this->my_exercises = json_decode($item['my_exercises']);
                    $this->featured_workouts = json_decode($item['featured_workouts']);
                    $this->preparation = $item["preparation"];
                    $this->type = $item["type"];
                    $this->vk = $item["vk"];
                    $this->tg = $item["tg"];
                }
                $this->auth = $auth;
                if ($this->get_status() == "coach" || $this->get_status() == "doctor"){
                    $sql2 = "SELECT id, user FROM requests WHERE receiver=".$this->get_id();
                    if ($result = $conn->query($sql2)){
                        foreach ($result as $item){
                            array_push($this->requests, $item);
                        }
                        $result->free();
                    }else{
                        echo $conn->error;
                    }
                    switch ($this->get_status()){
                        case "coach":
                            $sql3 = "SELECT user FROM user_to_coach WHERE coach=$this->id";
                            if ($result = $conn->query($sql3)){
                                foreach ($result as $item){
                                    array_push($this->sportsmen, $item["user"]);
                                }
                                $result->free();
                            }else{
                                echo $conn->error;
                            }
                            break;
                        case "doctor":
                            $sql4 = "SELECT user FROM user_to_doctor WHERE doctor=$this->id";

                            if ($result = $conn->query($sql4)){
                                foreach ($result as $item){
                                    array_push($this->sportsmen, $item["user"]);
                                }
                                $result->free();
                            }else{
                                echo $conn->error;
                            }
                            break;
                    }
                }
            }else{
                echo $conn -> error;
            }
            $select_result->free();
        }
    }
    public function get_auth(){
        return $this->auth;
    }
    public function get_id(){
        return $this->id;
    }
    public function is_admin(){
        return $this->status == "admin";
    }
    public function get_status(){
        return $this->status;
    }

    public function has_program($conn){
        $sql = "SELECT program FROM program_to_user WHERE user=$this->id AND (date_start + (604800 * weeks)) > " . time();
        if ($result = $conn->query($sql)){
            if ($result->num_rows == 0){
                return 0;
            }
            foreach ($result as $item){
                return (int)$item["program"];
            }
        }
    }

    public function check_the_login($header=true, $way="../"){
        if (!$this->get_auth()){
            if ($header){
                header('Location: '.$way.'log.php?please_log=1');
            }else{
                return false;
            }
        }
        if (!$header){
            return true;
        }
    }

    public function redirect_logged($way=''){
        if ($this->auth){
            header("Location: ".$way."user/profile.php");
        }
    }

    public function authenticate($conn, $login, $password){
        $error_array = array(
            "reg_fill_all_input_fields" => false,
            "reg_login_is_used" => false,
            "reg_login_too_short" => false,
            "reg_passwords_are_not_the_same" => false,
            "reg_password_not_fit" => false,
            "reg_password_too_short" => false,
            "reg_conn_error" => false,
            "reg_success" => false,
            "too_long_string" => false,
            "adding_stats" => false,
            "log_conn_error" => false,
            "log_fill_all_input_fields" => false,
            "log_incorrect_login_or_password" => false
        );

        $login = trim($login);
        $password = trim($password);

        if ($login == "" || $password == ""){
            $error_array['log_fill_all_input_fields'] = true;
            return $error_array;
        }
        $log_sql = "SELECT id, password FROM users WHERE login='$login' LIMIT 1";
        if (!($log_result = $conn->query($log_sql))){
            $error_array['log_conn_error'] = true;
            return $error_array;
        }
        if ($log_result->num_rows == 0){
            $error_array['log_incorrect_login_or_password'] = true;
            return $error_array;
        }

        foreach ($log_result as $check_password){
            if ($check_password['password'] != md5($password)){
                $error_array['log_incorrect_login_or_password'] = true;
                return $error_array;
            }else{
                $_SESSION["user"] = $check_password["id"];
            }
        }
        header('Location: user/profile.php');
    }

    public function reg($conn, $login, $status, $password, $password2, $name, $surname)
    {
        # ---------- collecting errors ---------------
        $error_array = array(
            "reg_fill_all_input_fields" => false,
            "reg_login_is_used" => false,
            "reg_login_too_short" => false,
            "reg_passwords_are_not_the_same" => false,
            "reg_password_not_fit" => false,
            "reg_password_too_short" => false,
            "reg_conn_error" => false,
            "reg_success" => false,
            "too_long_string" => false,
            "adding_stats" => false,
            "log_conn_error" => false,
            "log_fill_all_input_fields" => false,
            "log_incorrect_login_or_password" => false
        );
        # --------- deleting spaces --------------

        $login = trim($login);
        $password = trim($password);
        $password2 = trim($password2);
        $name = trim($name);
        $surname = trim($surname);

        # --------- checking data --------------

        if ($login == '' || $password == '' || $name == '' || $surname == '' || $status == NULL) { # checking blank fields
            $error_array['reg_fill_all_input_fields'] = true;
            return $error_array;
        }
        if (mb_strlen($login) < 3){
            $error_array['reg_login_too_short'] = true;
            return $error_array;
        }
        if (mb_strlen($password) < 8){
            $error_array['reg_password_too_short'] = true;
            return $error_array;
        }
        if (preg_match('/^[^\p{L}]+$/u', $password)){
            $error_array['reg_password_not_fit'] = true;
            return $error_array;
        }
        if ($password != $password2) { # checking password equality
            $error_array['reg_passwords_are_not_the_same'] = true;
            return $error_array;
        }
        $password = $password2 = md5($password); # hashing password
        if (strlen($login) > 32 || strlen($surname) > 32 || strlen($name) > 32 || strlen($password) > 256) { # checking length
            $error_array["too_long_string"] = true;
            return $error_array;
        }

        $check_sql = "SELECT id FROM users WHERE login='$login'";
        if ($reg_result = $conn->query($check_sql)) { # querying
            $rowsCount = $reg_result->num_rows;
            if ($rowsCount > 0) { # checking existence of the login
                $error_array['reg_login_is_used'] = true;
                return $error_array;
            }
        } else {
            $error_array['reg_conn_error'] = true;
            return $error_array;
        }

        $reg_sql = "INSERT INTO users(login, status, password, name, surname) VALUES('$login', '$status', '$password', '$name', '$surname')";
        if (!($conn->query($reg_sql))) { # querying
            $error_array['reg_conn_error'] = true;
            header("Location: reg_log.php?reg=1");
            # return $error_array;
        }

        insert_news($conn, "Пользователь $login зарегистрировался на платформе.", mysqli_insert_id($conn), false);
        /*
        $stats_sql = "INSERT INTO stats(user) VALUES (LAST_INSERT_ID())";
        if ($conn->query($stats_sql)) { # querying
            $_SESSION["reg_login"] = $login;
            header("Location: log.php?reg=1");
        } else {
            $error_array['adding_stats'] = true;
            return $error_array;
        }
        */
        return $error_array;
    }

    public function update($conn){
        $my_exercise = json_encode($this->my_exercises);
        $featured_exercises = json_encode($this->featured_exercises);
        $featured_workouts = json_encode($this->featured_workouts);
        $sql = "UPDATE users SET my_exercises='$my_exercise', featured_exercises='$featured_exercises', featured_workouts='$featured_workouts' WHERE id=$this->id";
        if ($conn->query($sql)){
            return true;
        }else{
            echo $conn->error;
            return false;
        }
    }

    public function get_avatar($conn){
        $select_sql = "SELECT file FROM avatars WHERE id=$this->avatar";
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

    public function update_avatar($conn, $data){
        if ($this->avatar == 1){
            $sql = "INSERT INTO avatars (file) VALUES ('$data')";
        }else{
            $sql = "UPDATE avatars SET file='$data' WHERE id=$this->avatar";
        }
        if ($conn->query($sql)){
            if ($this->avatar == 1){
                $new_avatar_id = mysqli_insert_id($conn);
                $update_sql = "UPDATE users SET avatar=$new_avatar_id WHERE id=$this->id";
                if ($conn->query($update_sql)){
                    header("Refresh: 0");
                }else{
                    echo $conn->error;
                }
            }else{
                header("Refresh: 0");
            }
        }else{
            echo $conn->error;
        }
    }

    public function change_featured($conn, $exercise_id){
        $index = array_search($exercise_id, $this->featured_exercises);
        if (is_numeric($index)) {
            array_splice($this->featured_exercises, $index, 1);
        }else{
            array_push($this->featured_exercises, $exercise_id);
        }

        $this->update($conn);
    }

    public function add_exercise($conn, $exercise_id){
        array_push($this->my_exercises, $exercise_id);
        $this->update($conn);
    }
    public function delete_exercise($conn, $exercise_id){
        $index = array_search($exercise_id, $this->my_exercises);
        if (is_numeric($index)) {
            array_splice($this->my_exercises, $index, 1);
        }
        $this->update($conn);
    }

    public function set_program($conn){
        $select_sql = "SELECT program FROM program_to_user WHERE user=$this->id AND date_start + weeks * 604800 >= ".time()." LIMIT 1"; // fix it
        if ($result_sql = $conn->query($select_sql)){
            if ($result_sql->num_rows == 0){
                $this->program = new Program($conn, 0);
                return false;
            }
            foreach ($result_sql as $item){
                $this->program = new Program($conn, $item['program']);
            }
            return true;
        }else{
            echo $conn->error;
            return false;
        }
    }
    public function get_news($conn){
        $sql = "SELECT news.message, news.date, news.personal, avatars.file, users.name, users.surname, users.login FROM ((news INNER JOIN users ON news.user=users.id) INNER JOIN avatars ON users.avatar=avatars.id) WHERE (user=$this->id";
        if (count($this->subscriptions) == 0){
            $this->set_subscriptions($conn);
        }
        if (count($this->subscriptions) != 0){
            $sql .= " OR ";
            foreach ($this->subscriptions as $subscription){
                $sql .= "(user=$subscription AND personal=0) OR ";
            }
            $sql = substr($sql, 0, -4);
        }
        $sql .= ") ORDER BY news.date DESC";
        if ($result = $conn->query($sql)){
            return $result;
        }else{
            echo $conn->error;
            return false;
        }
    }

    public function get_my_news($conn){
        $sql = "SELECT news.message, news.date, news.personal, avatars.file, users.name, users.surname, users.login FROM ((news INNER JOIN users ON news.user=users.id) INNER JOIN avatars ON users.avatar=avatars.id) WHERE user=$this->id ORDER BY date DESC";
        if ($result = $conn->query($sql)){
            return $result;
        }else{
            echo $conn->error;
            return false;
        }
    }

    public function print_workout_history($conn){ ?>
        <section class="last-trainings">
            <h1 class="last-trainings__title">Последние тренировки</h1>
            <div class="last-trainings__content">
                <?php
                    $sql = "SELECT * FROM workout_history WHERE user=$this->id";
                    if ($result = $conn->query($sql)){
                        if ($result->num_rows == 0){
                            echo "<p class='last-trainings__no-workout'>Нет тренировок</p>";
                        } else {
                            $rows = $result->fetch_all(MYSQLI_ASSOC);
                    
                            $reversed_rows = array_reverse($rows);
                    
                            foreach ($reversed_rows as $item){
                                $workout = new Workout($conn, $item['workout'], date("N", $item['date_completed']));
                                $workout->set_muscles();
                                $replacements = array(
                                    "{{ minutes }}" => round($item['time_spent'] / 60, 0),
                                    "{{ muscle_group_amount }}" => $workout->get_groups_amount(),
                                    "{{ exercise_amount }}" => count($workout->exercises),
                                    "{{ date }}" => date("d.m.Y", $item["date_completed"])
                                );
                                echo render($replacements, "../templates/workout_history_item.html");
                            }
                        }
                    } else {
                        echo "<p>".$conn->error."</p>";
                    }
                ?>
            </div>
        </section>
    <?php }


    public function get_workout_history($conn){
        $sql = "SELECT workout_history.id, workout_history.date_completed, workout_history.time_spent, workouts.exercises, workouts.approaches FROM workout_history INNER JOIN workouts ON workout_history.workout = workouts.id WHERE workout_history.user=$this->id ORDER BY workout_history.date_completed DESC";
        if ($result = $conn->query($sql)){
            foreach ($result as $item){
                array_push($this->workout_history, $item);
            }
        }else{
            echo $conn->error;
        }
    }

    function get_program_amount($conn){
        $sql = "SELECT program FROM program_to_user WHERE user=$this->id";
        if ($result = $conn->query($sql)){
            return $result->num_rows;
        }else{
            echo $conn->error;
        }
    }

    public function print_status(){
        switch ($this->get_status()){
            case "admin":
                echo "Админ";
                break;
            case "user":
                echo "Спортсмен";
                break;
            case "coach":
                echo "Тренер";
                break;
            case "doctor":
                echo "Доктор";
                break;
        }
    }

    public function print_prep()
    {
        if ($this->get_status() == "user" || $this->get_status() == "admin") {
            switch ($this->preparation) {
                case 0:
                    echo "Не указан";
                    break;
                case 1:
                    echo "Низкий";
                    break;
                case 2:
                    echo "Средний";
                    break;
                case 3:
                    echo "Высокий";
                    break;
            }
        }
    }
    public function print_type(){
        if ($this->type == 0){
            echo "Не указан";
            return;
        }
        if ($this->get_status() == "user" || $this->get_status() == "admin"){
            switch ($this->type){
                case 1:
                    echo "Любитель";
                    return;
                case 2:
                    echo "Проффесионал";
                    return;
            }
        }
        if ($this->get_status() == "coach"){
            switch ($this->type){
                case 2:
                    echo "Тренер команды";
                    return;
                case 1:
                    echo "Личный тренер";
                    return;
            }
        }
        if ($this->get_status() == "doctor"){
            switch ($this->type){
                case 2:
                    echo "Врач команды";
                    return;
                case 1:
                    echo "Личный врач";
                    return;
            }
        }
    }

    public function check_request($conn, $id){
        $user = $this->get_id();
        $sql = "SELECT id FROM requests WHERE user=$user AND receiver=$id";
        if ($result = $conn->query($sql)){
            return $result->num_rows > 0;
        }else{
            echo $conn->error;
            return false;
        }
    }

    public function update_phys($conn, $height, $weight){
        $sql = "INSERT INTO phys_updates (user, height, weight, date) VALUES ($this->id, $height, $weight, ".time().")";
        if (!$conn->query($sql)){
            echo $conn->error;
        }
    }

    public function get_phys_updates($conn){
        $sql = "SELECT height, weight, date FROM phys_updates WHERE user=$this->id ORDER BY date DESC";
        if ($result = $conn->query($sql)){
            foreach ($result as $item){
                $this->phys_updates[(string)$item["date"]] = array("height" => $item["height"], "weight" => $item["weight"]);
            }
        }else{
            echo $conn->error;
        }
    }

    public function get_current_phys_data($conn){
        $id = $this->get_id();
        $sql = "SELECT height, weight FROM phys_updates WHERE user=$id ORDER BY date DESC LIMIT 1";
        if ($result = $conn->query($sql)){
            foreach ($result as $item){
                return array("height" => $item["height"], "weight" => $item["weight"]);
            }
            return array("height" => 0, "weight" => 0);
        }else{
            echo $conn->error;
        }
    }

    public function get_doctor_data($conn, $user_id=NULL){
        $sql = NULL;
        switch ($this->get_status()){
            case "doctor":
                $sql = "SELECT * FROM doctor_data WHERE user=$user_id AND doctor=$this->id LIMIT 1";
                break;
            case "user":
                $sql = "SELECT * FROM doctor_data WHERE user=$this->id AND doctor=".$this->doctor->get_id()." LIMIT 1";
                break;
        }
        if ($sql != NULL && $result = $conn->query($sql)){
            foreach ($result as $item)
                return $item;
        }
        return NULL;
    }

    public function update_doctor_data($conn, $data){
        if ($this->get_id() != $data["doctor"])
            return;
        if ($data["intake_start"] == NULL)
            $data["intake_start"] = "NULL";
        if ($data["intake_end"] == NULL)
            $data["intake_end"] = "NULL";
        $sql = "UPDATE doctor_data SET recommendations='".$data["recommendations"]."', intake_start=".$data["intake_start"].", intake_end=".$data["intake_end"].", medicines='".$data["medicines"]."' WHERE doctor=$this->id AND user=".$data["user"];
        if (!$conn->query($sql)){
            echo $conn->error;
        }
    }

    public function get_closest_workout($conn){
        $this->program->set_additional_data($conn, $this->get_id());
        for ($i = 0; $i < 7; $i++)
            if ($this->program->program[(date("N") + $i - 1) % 7] != 0 && ((time() + $i * 86400) < ($this->program->date_start * $this->program->weeks * 604800)))
                return time() + $i * 86400;
        return NULL;
    }

    public function get_coach_data($conn, $user_id=NULL){
        $sql = NULL;
        switch ($this->get_status()){
            case "coach":
                $sql = "SELECT * FROM coach_data WHERE user=$user_id AND coach=$this->id LIMIT 1";
                break;
            case "user":
                $sql = "SELECT * FROM coach_data WHERE user=$this->id AND coach=".$this->coach->get_id()." LIMIT 1";
                break;
        }
        if ($sql != NULL && $result = $conn->query($sql)){
            foreach ($result as $item)
                return $item;
        }
        return NULL;
    }

    public function update_coach_data($conn, $data){
        if ($this->get_id() != $data["coach"])
            return;
        $sql = "UPDATE coach_data SET competitions='".$data["competitions"]."', goals='".$data["goals"]."', info='".$data["info"]."' WHERE coach=$this->id AND user=".$data["user"];
        if (!$conn->query($sql)){
            echo $conn->error;
        }
    }

    public function get_control_workouts($conn, $user_id=NULL, $is_done){
        $sql = NULL;
        switch ($this->get_status()) {
            case "coach":
                $sql = "SELECT id FROM control_workouts WHERE user=$user_id ";
                break;
            case "user":
                $sql = "SELECT id FROM control_workouts WHERE user=$this->id ";
                break;
        }
        if ($is_done)
            $sql .= "AND is_done=1 ORDER BY date DESC";
        else
            $sql .= "AND is_done=0 ORDER BY date";
        $returnval = array();
        if ($result = $conn->query($sql)){
            foreach ($result as $item){
                array_push($returnval, new Control_Workout($conn, $item["id"]));
            }
        }else{
            echo $conn->error;
        }
        return $returnval;
    }
}
