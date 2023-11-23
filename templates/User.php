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

    function set_subscriptions($conn){ // set subscriptions function
        $sql = "SELECT user FROM subs WHERE subscriber=$this->id"; // Query to retrieve subscriptions of the current user
        $this->subscriptions = array(); // Reset the subscriptions array
        if ($result = $conn->query($sql)){ // Execute the SQL query
            foreach ($result as $user){ // Loop through the query result to fetch subscriptions
                array_push($this->subscriptions, $user['user']); // Push each subscription user into the subscriptions array
            }
        }else{
            echo $conn->query; // Output an error message in case of query failure
        }
    }
    function set_subscribers($conn){ // the same as  subscriptions function, but with subscribers
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

    function set_staff($conn){ // add staff(coach and doctor)
        $id = $this->get_id(); // Get the ID of the current user
        $sql = "SELECT (SELECT coach FROM user_to_coach WHERE user = $id) AS selected_coach, (SELECT doctor FROM user_to_doctor WHERE user = $id) AS selected_doctor"; // SQL query to select the coach and doctor assigned to the user
        if ($result = $conn->query($sql)){  // Execute the SQL query
            foreach ($result as $item){  // Iterate through the query result
                if ($item["selected_coach"] != null) // If a coach is assigned, create a User object for the coach and assign it to the 'coach' property
                    $this->coach = new User($conn, $item["selected_coach"]);
                if ($item["selected_doctor"] != null) // If a doctor is assigned, create a User object for the doctor and assign it to the 'doctor' property
                    $this->doctor = new User($conn, $item["selected_doctor"]);
                return 1; // Return 1 to indicate success in setting the staff
            }
        }
        echo $conn->error; // Output an error message if the query fails
        return 0; // Return 0 if the process fails
    }

    public function get_requests(){ // get user's requests function
        return $this->requests; // returns requests
    }

    public function get_sportsmen(){ // get sportsmen function
        return $this->sportsmen; // returns sportsmen list
    }
    public function get_sportsmen_advanced($conn){
        $return_val = array(); // Initialize an empty array to store advanced sportsmen data

        // Iterate through the list of sportsmen using the get_sportsmen() method
        foreach ($this->get_sportsmen() as $sportsman)
            array_push($return_val, new User($conn, $sportsman)); // Create a new User object for each sportsman using the provided database connection ($conn)

        return $return_val; // Return an array containing advanced User objects for each sportsman
    }

    public function __construct($conn, $id=-1, $auth=false){ // contructor for class
        if (isset($id) && $id != -1) { // Check if the ID is set and not equal to -1
            $select_sql = "SELECT * FROM users WHERE id=$id LIMIT 1"; // SQL query to select user information based on the provided ID
            if ($select_result = $conn->query($select_sql)) {
                foreach ($select_result as $item) { // Execute the SQL query
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
                $this->auth = $auth; // Set the authentication status
                if ($this->get_status() == "coach" || $this->get_status() == "doctor"){ // Perform additional actions if the user is a coach or doctor
                    $sql2 = "SELECT id, user FROM requests WHERE receiver=".$this->get_id(); // get pending requests sent to the user
                    if ($result = $conn->query($sql2)){
                        foreach ($result as $item){
                            array_push($this->requests, $item);
                        }
                        $result->free();
                    }else{
                        echo $conn->error;
                    }
                    switch ($this->get_status()){ // get sportsmen associated with the coach or doctor
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
                echo $conn -> error; // print error message
            }
            $select_result->free(); //  free up the result set
        }
    }
    public function get_auth(){ // get auth function
        return $this->auth; // Returns the value of the 'auth' property, indicating if the user is authenticated or logged in.
    }
    public function get_id(){ // get id function
        return $this->id; // Returns the 'id' property of the user object, representing the user's unique identification.
    }
    public function is_admin(){  // get id user is admin function
        return $this->status == "admin"; // returns user is admin or not
    }
    public function get_status(){  // get status function
        return $this->status; // Returns the status of the user
    }

    public function has_program($conn){ // check if user has program
        $sql = "SELECT program FROM program_to_user WHERE user=$this->id AND (date_start + (604800 * weeks)) > " . time(); // SQL query to check if the user has an active program based on the current time.
        if ($result = $conn->query($sql)){ // Execute the query.
            if ($result->num_rows == 0){ // Check if there are any rows in the result.
                return 0; // If there are no rows, the user does not have an active program.
            }
            foreach ($result as $item){ // Iterate over the result set
                return (int)$item["program"];  // Return the ID of the active program for the user.
            }
        }
    }

    public function check_the_login($header=true, $way="../"){ // check user login
        if (!$this->get_auth()){ // Check if the user is not authenticated
            if ($header){ // If a header is required, redirect the user to the login page
                header('Location: '.$way.'log.php?please_log=1');
            }else{
                return false;  // If no header is required, return false to indicate authentication failure
            }
        }
        if (!$header){
            return true; // If no header is required and user is authenticated, return true to indicate successful authentication
        }
    }

    public function redirect_logged($way=''){ // regirect to profile of user
        if ($this->auth){
            header("Location: ".$way."user/profile.php"); // regirect to profile page
        }
    }

    public function authenticate($conn, $login, $password){
        $error_array = array( // Array to manage different error states during authentication
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

        // Trim login and password
        $login = trim($login);
        $password = trim($password);

        if ($login == "" || $password == ""){ // Check if login or password is empty
            $error_array['log_fill_all_input_fields'] = true;
            return $error_array;
        }
        $log_sql = "SELECT id, password FROM users WHERE login='$login' LIMIT 1"; // Query to select user ID and password from the database based on login
        if (!($log_result = $conn->query($log_sql))){
            $error_array['log_conn_error'] = true;
            return $error_array; // return array of error messages
        }
        if ($log_result->num_rows == 0){
            $error_array['log_incorrect_login_or_password'] = true;
            return $error_array; // return array of error messages
        }

        foreach ($log_result as $check_password){
            if ($check_password['password'] != md5($password)){
                $error_array['log_incorrect_login_or_password'] = true;
                return $error_array; // return array of error messages
            }else{
                $_SESSION["user"] = $check_password["id"];  // If password matches, set the user ID in the session
            }
        }
        header('Location: user/profile.php'); // Redirect to user profile
    }

    public function reg($conn, $login, $status, $password, $password2, $name, $surname) // check registration function
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

        return $error_array; // return array of error messages
    }

    public function update($conn){ // update exercises info
        // Encode arrays to JSON strings
        $my_exercise = json_encode($this->my_exercises);
        $featured_exercises = json_encode($this->featured_exercises);
        $featured_workouts = json_encode($this->featured_workouts);
        $sql = "UPDATE users SET my_exercises='$my_exercise', featured_exercises='$featured_exercises', featured_workouts='$featured_workouts' WHERE id=$this->id"; // Construct SQL query to update user data
        if ($conn->query($sql)){
            return true; // Return true if the update is successful
        }else{
            echo $conn->error;
            return false; // Return false to indicate update failure
        }
    }

    public function get_avatar($conn){ // function to get user avatar
        $select_sql = "SELECT file FROM avatars WHERE id=$this->avatar";
        if ($result_sql = $conn->query($select_sql)){  // Execute the SQL query to fetch the avatar file path
            foreach ($result_sql as $item){
                $image = $item['file']; // Get the file path from the database
            }
        }else{
            $image=null; // Set image to null in case of an error
            echo $conn->error;
        }

        return $image; // Return the file path to the avatar
    }

    public function update_avatar($conn, $data){ // function to update user avatar
        if ($this->avatar == 1){ // If the user doesn't have an avatar (ID = 1 is a placeholder)
            $sql = "INSERT INTO avatars (file) VALUES ('$data')";
        }else{ // If the user already has an avatar, update the existing avatar
            $sql = "UPDATE avatars SET file='$data' WHERE id=$this->avatar";
        }
        if ($conn->query($sql)){ // Execute the SQL query to update or insert the avatar
            if ($this->avatar == 1){ // If a new avatar was inserted, update the user's avatar ID
                $new_avatar_id = mysqli_insert_id($conn);
                $update_sql = "UPDATE users SET avatar=$new_avatar_id WHERE id=$this->id";
                if ($conn->query($update_sql)){ // Update the user's avatar reference in the 'users' table
                    header("Refresh: 0"); // Refresh the page after successful update
                }else{
                    echo $conn->error; // Output an error if the update fails
                }
            }else{
                header("Refresh: 0"); // // Refresh the page after successful update
            }
        }else{
            echo $conn->error; // Output an error if the query fails
        }
    }

    public function change_featured($conn, $exercise_id){ // function to change featured exercise
        $index = array_search($exercise_id, $this->featured_exercises); // Check if the exercise ID is already in the featured exercises array
        if (is_numeric($index)) {
            array_splice($this->featured_exercises, $index, 1); // If the exercise ID is found in the featured exercises array, remove it
        }else{
            array_push($this->featured_exercises, $exercise_id); // If the exercise ID is not found, add it to the featured exercises array
        }

        $this->update($conn); // Update the user's data in the database after modifying the featured exercises array
    }

    public function add_exercise($conn, $exercise_id){ // function to add exercise
        array_push($this->my_exercises, $exercise_id); // Add the exercise ID to the user's exercises array
        $this->update($conn); // Update the user's data in the database after modifying the exercises array
    }
    public function delete_exercise($conn, $exercise_id){
        $index = array_search($exercise_id, $this->my_exercises); // Find the index of the exercise ID in the user's exercises array
        if (is_numeric($index)) {
            array_splice($this->my_exercises, $index, 1);  // If the exercise ID is found in the array, remove it
        }
        $this->update($conn); // Update the user's data in the database after modifying the exercises array
    }

    public function set_program($conn){ // function to set program
        $select_sql = "SELECT program FROM program_to_user WHERE user=$this->id AND date_start + weeks * 604800 >= ".time()." LIMIT 1"; // fix it
        if ($result_sql = $conn->query($select_sql)){
            if ($result_sql->num_rows == 0){
                $this->program = new Program($conn, 0); // If no active program is found, set the program property to a default program (ID 0)
                return false;
            }
            foreach ($result_sql as $item){ // If an active program is found, set the program property
                $this->program = new Program($conn, $item['program']);
            }
            return true; // if success return true
        }else{
            echo $conn->error;
            return false; // if error return false
        }
    }
    public function get_news($conn){ // function to get news
        // Construct the initial SQL query
        $sql = "SELECT news.message, news.date, news.personal, avatars.file, users.name, users.surname, users.login FROM ((news INNER JOIN users ON news.user=users.id) INNER JOIN avatars ON users.avatar=avatars.id) WHERE (user=$this->id";
        if (count($this->subscriptions) == 0){ // Check if the user has subscriptions
            $this->set_subscriptions($conn); // If not, retrieve the subscriptions
        }
        if (count($this->subscriptions) != 0){ // If the user has subscriptions
            $sql .= " OR ";
            foreach ($this->subscriptions as $subscription){ // Append each subscription to the SQL query
                $sql .= "(user=$subscription AND personal=0) OR ";
            }
            $sql = substr($sql, 0, -4); // Remove the extra " OR " at the end of the query
        }
        $sql .= ") ORDER BY news.date DESC"; // Complete the SQL query
        if ($result = $conn->query($sql)){ // Execute the constructed query
            return $result;
        }else{
            echo $conn->error;
            return false;
        }
    }

    public function get_my_news($conn){ // get user's own news
        // Construct the SQL query to retrieve user's own news
        $sql = "SELECT news.message, news.date, news.personal, avatars.file, users.name, users.surname, users.login FROM ((news INNER JOIN users ON news.user=users.id) INNER JOIN avatars ON users.avatar=avatars.id) WHERE user=$this->id ORDER BY date DESC";
        if ($result = $conn->query($sql)){// Execute the SQL query
            return $result; // return result object
        }else{
            echo $conn->error;
            return false; // In case of an error, display the error message and return false
        }
    }

    public function print_workout_history($conn){ // print history of workout ?>
    <!-- print section -->
        <section class="last-trainings">
            <h1 class="last-trainings__title">Последние тренировки</h1>
            <div class="last-trainings__content">
                <?php
                    $sql = "SELECT * FROM workout_history WHERE user=$this->id"; // Construct the SQL query
                    if ($result = $conn->query($sql)){ // Execute the SQL query
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
                                echo render($replacements, "../templates/workout_history_item.html"); // render workout_history_item with replacements
                            }
                        }
                    } else {
                        echo "<p>".$conn->error."</p>";
                    }
                ?>
            </div>
        </section>
    <?php }


    public function get_workout_history($conn){ // get workout history
        // Construct the SQL query to retrieve user's own news
        $sql = "SELECT workout_history.id, workout_history.date_completed, workout_history.time_spent, workouts.exercises, workouts.approaches FROM workout_history INNER JOIN workouts ON workout_history.workout = workouts.id WHERE workout_history.user=$this->id ORDER BY workout_history.date_completed DESC";
        if ($result = $conn->query($sql)){ // Execute the SQL query
            foreach ($result as $item){
                array_push($this->workout_history, $item);
            }
        }else{ // In case of an error, display the error message and return false
            echo $conn->error;
        }
    }

    function get_program_amount($conn){ // get ammount of program
        $sql = "SELECT program FROM program_to_user WHERE user=$this->id";
        if ($result = $conn->query($sql)){
            return $result->num_rows; // return the count of programs
        }else{
            echo $conn->error;
        }
    }

    public function print_status(){ // print status of user(return status)
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

    public function print_prep() // level of physic(return physic parametr)
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
    public function print_type(){ // print user type(return types of user's paramtres)
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

    public function check_request($conn, $id){ // checks user's request
        $user = $this->get_id(); // Get the ID of the current user
        $sql = "SELECT id FROM requests WHERE user=$user AND receiver=$id"; // SQL query to check for a request between the current user and the provided $id
        if ($result = $conn->query($sql)){ // Check if the query executes successfully
            // Check if there are any rows returned from the query
            // If rows exist, a request is found, return true
            return $result->num_rows > 0;
        }else{ // If there's an error with the query, output the error message
            echo $conn->error;
            return false; // Return false indicating an error or no request found
        }
    }

    public function update_phys($conn, $height, $weight){ // update physic data
        // Prepare the SQL statement with placeholders
        $sql = "INSERT INTO phys_updates (user, height, weight, date) VALUES ($this->id, $height, $weight, ".time().")";
        if (!$conn->query($sql)){
            echo $conn->error; // print errors
        }
    }

    public function get_phys_updates($conn){ // get physic data updates
        
        $sql = "SELECT height, weight, date FROM phys_updates WHERE user=$this->id ORDER BY date DESC";
        if ($result = $conn->query($sql)){
            foreach ($result as $item){
                $this->phys_updates[(string)$item["date"]] = array("height" => $item["height"], "weight" => $item["weight"]);
            }
        }else{
            echo $conn->error;
        }
    }

    public function get_current_phys_data($conn){ // get current user's physic data
        $id = $this->get_id();
        $sql = "SELECT height, weight FROM phys_updates WHERE user=$id ORDER BY date DESC LIMIT 1"; // SQL query to select height, weight, and date from the 'phys_updates' table for a specific user ID
        if ($result = $conn->query($sql)){ // Executing the SQL query and checking for success
            foreach ($result as $item){
                return array("height" => $item["height"], "weight" => $item["weight"]); // return physical updates in an associative array indexed by date, containing height and weight information
            }
            return array("height" => 0, "weight" => 0); // return phycis updates with zeros
        }else{
            echo $conn->error; // Displaying an error message if the query fails
        }
    }

    public function get_doctor_data($conn, $user_id=NULL){ // get data for doctor
        $sql = NULL;
        switch ($this->get_status()){ // Construct SQL query based on the user's status
            case "doctor":  // For a doctor, fetch data of a specific user they are assigned to
                $sql = "SELECT * FROM doctor_data WHERE user=$user_id AND doctor=$this->id LIMIT 1";
                break;
            case "user": // For a user, fetch their data related to their assigned doctor
                $sql = "SELECT * FROM doctor_data WHERE user=$this->id AND doctor=".$this->doctor->get_id()." LIMIT 1";
                break;
        }
        if ($sql != NULL && $result = $conn->query($sql)){  // if the SQL query is valid and execute it, fetching the result
            foreach ($result as $item)
                return $item; // Loop through the result set and return the first item
        }
        return NULL; // Return NULL if no data or an error occurs
    }

    public function update_doctor_data($conn, $data){ // update data for doctor
        if ($this->get_id() != $data["doctor"]) // Verify if the provided doctor ID matches the instance's doctor ID
            return; // Exit if the doctor ID doesn't match
        if ($data["intake_start"] == NULL)  // Set NULL for 'intake_start' and 'intake_end' fields if data is NULL
            $data["intake_start"] = "NULL";
        if ($data["intake_end"] == NULL)
            $data["intake_end"] = "NULL";
        // Construct and execute SQL query to update doctor data
        $sql = "UPDATE doctor_data SET recommendations='".$data["recommendations"]."', intake_start=".$data["intake_start"].", intake_end=".$data["intake_end"].", medicines='".$data["medicines"]."' WHERE doctor=$this->id AND user=".$data["user"];
        if (!$conn->query($sql)){
            echo $conn->error; // Perform the query and handle errors if any
        }
    }

    public function get_closest_workout($conn){ // fet user's closest workout
        $this->program->set_additional_data($conn, $this->get_id()); // Fetch additional program data for the user
        for ($i = 0; $i < 7; $i++) // Iterate over the next 7 days
            if ($this->program->program[(date("N") + $i - 1) % 7] != 0 && ((time() + $i * 86400) < ($this->program->date_start * $this->program->weeks * 604800)))
                return time() + $i * 86400; // Return the timestamp of the closest upcoming workout
        return NULL; // Return NULL if no upcoming workout is found within the next 7 days
    }

    public function get_coach_data($conn, $user_id=NULL){ // get coach data
        $sql = NULL;
        switch ($this->get_status()){
            case "coach": // Fetch coach data for a specified user ID and coach ID
                $sql = "SELECT * FROM coach_data WHERE user=$user_id AND coach=$this->id LIMIT 1";
                break;
            case "user":  // Fetch coach data for the current user and their assigned coach
                $sql = "SELECT * FROM coach_data WHERE user=$this->id AND coach=".$this->coach->get_id()." LIMIT 1";
                break;
        }
        if ($sql != NULL && $result = $conn->query($sql)){  // Check if the SQL query is defined and execute it, returning the data if available
            foreach ($result as $item)
                return $item; // Return the retrieved coach data
        }
        return NULL; // Return NULL if no coach data is found
    }

    public function update_coach_data($conn, $data){ // update coach data
        if ($this->get_id() != $data["coach"]) // Check if the current user ID matches the coach ID in the data
            return; // If not matched, exit the function
        // Prepare SQL query to update coach data based on provided information
        $sql = "UPDATE coach_data SET competitions='".$data["competitions"]."', goals='".$data["goals"]."', info='".$data["info"]."' WHERE coach=$this->id AND user=".$data["user"];
        if (!$conn->query($sql)){
            echo $conn->error; // Execute the SQL query and handle errors, if any
        }
    }

    public function get_control_workouts($conn, $user_id=NULL, $is_done){ // get control workouts list
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
        $returnval = array(); // Initialize an empty array to store the resulting workouts
        if ($result = $conn->query($sql)){ // Execute the SQL query
            foreach ($result as $item){
                array_push($returnval, new Control_Workout($conn, $item["id"])); //  For each resulting item, create a Control_Workout object and add it to the return array
            }
        }else{
            echo $conn->error;// Output any SQL errors
        }
        return $returnval; // Return the array of Control_Workout objects
    }
}
