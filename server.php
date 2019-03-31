<?php
//First initialize the database information
$host = "34.73.219.7";
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
        if ($data === false) {
             $key = array_search($read_socket, $clients);
             unset($clients[$key]);
             echo "client disconnected.\n";
             continue;
        }

        $data = trim($data);

        //If there is data, proceed
        if (!empty($data)) {
             echo " send {$data}\n";

             //Break down the message into parts : user_id, message, lat, lng
             $message = explode(";", $data);

             $canbusdump = explode(",", $message[0]);

             $canbus = array(
                 "arb_id" => ""
             );

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
             /*if ($lng < -83.220059) {
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

             //Add this information!
             $sql = "INSERT INTO message (message, user_id) values (:message, :user_id)";
             $stmt = $db->prepare($sql);
             $stmt->execute($package);
             echo "Clients: " . print_r($clients, true);
             echo "Package: " . print_r($package, true);

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