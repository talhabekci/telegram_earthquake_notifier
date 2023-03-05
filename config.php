<?php

$open = mysqli_connect("localhost", "root", "", "earthquake_notify");
if (!$open) {
    echo json_encode(["result" => NULL, "error" => "An error occurred while connecting to the database."]);
}
