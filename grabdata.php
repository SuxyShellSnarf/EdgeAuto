<?php
/**
 * Created by PhpStorm.
 * User: SuxyShellSnarf
 * Date: 4/8/2019
 * Time: 1:20 AM
 */

$db = new PDO("mysql:host=localhost;dbname=EdgeAuto", "edgeauto", "edgeauto19!");

$file = fopen("session4.csv", "w");

$sql = "select * from message where session_id = 4;";
$stmt = $db->prepare($sql);
$stmt->execute();
$results = array();
while($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $string = $data["message_id"] . "," . $data["created"] . "," . $data["arb_id"] . "," . $data["message"] . "," . $data["latitude"] . "," . $data["longitude"] . "," . $data["cantime"] . "," . $data["session_id"];
    fputcsv($file, $data);
}