<?php

$open = mysqli_connect("db_host", "db_user_name", "db_user_password", "db_name");
if (!$open) {
    echo json_encode(["result" => NULL, "error" => "An error occurred while connecting to the database."]);
}
