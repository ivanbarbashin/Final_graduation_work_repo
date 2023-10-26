<?php
session_start();
if (isset($_SESSION["program"]) && isset($_GET["id"])){
    for ($i = 0; $i < 7; $i ++){
        if ($_SESSION["program"][$i] == $_GET["id"]){
            $_SESSION["program"][$i] = 0;
        }
    }
}

header("Location: c_program.php");
