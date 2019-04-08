<?php
/**
 * Created by PhpStorm.
 * User: SuxyShellSnarf
 * Date: 4/8/2019
 * Time: 1:20 AM
 */

$db = new PDO("mysql:host=localhost;dbname=EdgeAuto", "edgeauto", "edgeauto19!");

$sql = "select * from message where session_id = 1;";
$stmt = $db->prepare($sql);
$stmt->execute();
while($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo print_r($data);
}