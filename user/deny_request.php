<?php
include "../templates/func.php";
include "../templates/settings.php";

if (empty($_GET["id"]) || $_GET["id"] == ""){
    header("Location: requests.php");
}

$sql = "DELETE FROM requests WHERE id=".$_GET["id"]." AND receiver=".$user_data->get_id();
$conn->query($sql);
header("Location: requests.php");
