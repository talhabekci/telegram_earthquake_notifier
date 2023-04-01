<?php
require 'config.php';

function send_msg($message) {

    $token = "YOUR_API_TOKEN";

    $data = [
        "text" => $message,
        "chat_id" => "telegram_chat_id",
    ];

    $result = file_get_contents('https://api.telegram.org/bot' . $token . '/sendMessage?' . http_build_query($data) . '');

    return json_decode($result, true);
}

function request() {
    if (!$init = curl_init()) {
        return ["result" => null, "error" => ["code" => null, "message" => "Initalize a cURL session"]];
    }

    $options = [
        CURLOPT_URL => 'https://api.orhanaydogdu.com.tr/deprem/kandilli/live',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ];

    if (!curl_setopt_array($init, $options)) {
        return ["result" => null, "error" => ["code" => null, "message" => "Set multiple options for a cURL transfer"]];
    }

    if (!$exec = curl_exec($init)) {
        echo curl_error($init);
        return ["result" => null, "error" => ["code" => null, "message" => "Perform a cURL session"]];
    }
    curl_close($init);
    return json_decode($exec, true);
}

$all_eq = request();

foreach ($all_eq["result"] as $eq) {
    $data = $eq["title"];
    $data .= $eq["date"];
    $data .= $eq["geojson"]["coordinates"][0];
    $data .= $eq["geojson"]["coordinates"][1];
    $data .= $eq["mag"];
    $data .= $eq["depth"];
    $data .= $eq["location_properties"]["closestCity"]["name"];
    $data .= $eq["location_properties"]["closestCity"]["cityCode"];
    $data .= $eq["location_properties"]["closestCity"]["distance"];
    $data .= $eq["location_properties"]["closestCity"]["population"];
    $data .= $eq["date_time"];
    $data .= $eq["location_tz"];

    $hash = hash("sha256", $data);

    $result = mysqli_query($open, "SELECT * FROM `eq_hash` WHERE `hash` = '" . $hash . "' ");
    if (!$result) {
        exit(json_encode(["result" => null, "error" => "An error occured while selecting data from database."]));
    }

    if (mysqli_num_rows($result) >= 1) {
        exit(json_encode(["result" => NULL, "error" => "There is no new earthquake."]));
    }

    $title = $eq["title"];
    $date = $eq["date"];
    $magnitude = $eq["mag"];
    $depth = $eq["depth"];
    $lat = $eq["geojson"]["coordinates"][0];
    $lng = $eq["geojson"]["coordinates"][1];

    $maps = "https://www.google.com/maps?q=" . $lng . "," . $lat . "&ll=" . $lng . "," . $lat . "&z=8";

    $message = "NEW EARTHQUAKE ! \n";
    $message .= "Location: " . $title . ' - ' . $maps . "\n";
    $message .= "Date - Time: " .  $date . "\n";
    $message .= "Magnitude: " .  $magnitude . "\n";
    $message .= "Depth: " . $depth;

    $result = mysqli_query($open, "INSERT INTO `eq_hash` (`hash`, `message`, `date`) VALUES ('" . $hash . "', '" . $message . "', '" . $date . "') ");
    if (!$result) {
        exit(json_encode(["result" => null, "error" => "An error occured while inserting data to database."]));
    }

    $last_id = mysqli_insert_id($open);

    $result = mysqli_query($open, "SELECT `id`, `message` FROM `eq_hash` WHERE `id` = '" . $last_id . "' ORDER BY `date` ASC ");
    if (!$result) {
        exit(json_encode(["result" => null, "error" => "An error occured while selecting data from database."]));
    }

    $eq_message = mysqli_fetch_assoc($result);

    $send_msg = send_msg($eq_message["message"]);

    if ($send_msg["ok"] == true) {

        $result = mysqli_query($open, "INSERT INTO `telegram_msg` (`message_id`, `date`, `text`, `status`, `error`) VALUES ('" . $send_msg["result"]["message_id"] . "', '" . $date . "', '" . $send_msg["result"]["text"] . "', '" . $send_msg["ok"] . "', NULL) ");
        if (!$result) {
            exit(json_encode(["result" => null, "error" => "An error occured while inserting data to database."]));
        }
    } else {

        $result = mysqli_query($open, "INSERT INTO `errors` (`message_id`, `message`) VALUES ('" . $eq_message["id"] . "', 'An error occurred while sening message.') ");
        if (!$result) {
            exit(json_encode(["result" => null, "error" => "An error occured while inserting data to database."]));
        }
    }
}
