<?php
//First initialize the database information
//$host = "10.142.0.4";
//$host = "10.142.0.7";
$port = 8001;

$db = new PDO("mysql:host=localhost;dbname=EdgeAuto", "edgeauto", "edgeauto19!");

set_time_limit(0);

ob_implicit_flush();

//Create the original socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
socket_listen($socket, 3) or die("Could not set up socket listener");

//This will allow us to handle multiple clients at the same time
$clients = array($socket);

while(true) {
     $read = $clients;
     $write = null;
     $except = null;

     if (socket_select($read, $write, $except, 0) < 1) {
         continue;
     }

     //If there is a new socket, declare it
     if (in_array($socket, $read)) {
         $clients[] = $newsocket = socket_accept($socket);
         socket_getpeername($newsocket, $ip, $host);
         socket_write($newsocket, "There are " . (count($clients) - 1) . "client(s) connected to the server\n");
         echo "New client connected: {$ip}\n";
         $key = array_search($socket, $read);
         unset($read[$key]);
    }

     //Loop through all of the available sockets
    foreach ($read as $read_socket) {
        $data = @socket_read($read_socket, 4096, PHP_BINARY_READ);

        //Iterate through all of the connected lines and determine if any have been disconnected and remove them
        if ($data === "") {
             $key = array_search($read_socket, $clients);
             unset($clients[$key]);
             echo "client disconnected.\n";
             continue;
        }

        $data = trim($data);

        //If there is data, proceed
        if (!empty($data)) {
             echo "Data sent {$data}\n";

             //Break down the message into parts : user_id, message, lat, lng
             $message = explode(";", $data);

             $canbusdump = explode(",", $message[0]);


             /**
             $lat = explode(".", $message[2]);
             $lng = explode(".", $message[3]);

             $gps_lat = array(
                 "gps_degree" => $lat[0],
                 "gps_minute" => substr($lat[1], 0, 2),
                 "gps_decimals" => substr($lab[1], 2)
             );

             $gps_lng = array(
                "gps_degree" => $lng[0],
                "gps_minute" => substr($lng[1], 0, 2),
                "gps_decimals" => substr($lng[1], 2)
             );
              if ($lng < -83.220059) {
                 echo "Out of range";
             } else if ($lng < -83.206406) {
                 if ($lat < 42.665945) {
                     echo "Out of range";
                 } else if ($lat < 42.673340) {
                     echo "Success!";
                 } else if ($lat < 42.679222) {
                     echo "Success!";
                 } else {
                     echo "Out of range";
                 }
             } else {
                 echo "Out of range";
             }*/
            if (count($canbusdump) == 1) {
                $canbus = array(
                    "cantime" => $canbusdump[0],
                    "user_id" => $message[1]
                );
                $sql = "INSERT INTO message (cantime, user_id) values (:cantime, :user_id)";
            } else if (count($canbusdump) == 2) {
                $canbus = array(
                    "arb_id" => $canbusdump[0],
                    "message" => $canbusdump[1],
                    "user_id" => $message[1]
                );
                $sql = "INSERT INTO message (arb_id, message, user_id) values (:arb_id, :message, :user_id)";
            } else if (count($canbusdump) == 3) {
                $canbus = array(
                    "arb_id" => $canbusdump[0],
                    "message" => $canbusdump[1],
                    "cantime" => date("Y-m-d H:i:s", $canbusdump[2]),
                    "user_id" => $message[1]
                );
                $sql = "INSERT INTO message (arb_id, message, cantime, user_id) values (:arb_id, :message, :cantime, :user_id)";
            } else {
                $canbus = array(
                    "arb_id" => $canbusdump[0],
                    "message" => $canbusdump[1],
                    "latitude" => $canbusdump[2],
                    "longitude" => $canbusdump[3],
                    "cantime" => date("Y-m-d H:i:s", $canbusdump[4]),
                    "user_id" => $message[1]
                );
                $sql = "INSERT INTO message (arb_id, message, latitude, longitude, cantime, user_id) values (:arb_id, :message, :latitude, :longitude, :cantime, :user_id)";
            }

             //Add this information!
             $stmt = $db->prepare($sql);
             $stmt->execute($canbus);
             echo "Package: " . print_r($canbusdump, true);

             //Respond
             foreach ($clients as $send_socket) {
                 if ($send_socket == $socket) {
                     continue;
                 }
                echo "SEND SOCKET: " . $send_socket . "\n";
                 socket_write($send_socket, $data);
             }
         }
     }
}

//We are done here
socket_close($socket);