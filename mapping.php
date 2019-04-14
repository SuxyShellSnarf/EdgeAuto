<?php
//First initialize the database information
$host = "10.142.0.4";
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
         //socket_write($newsocket, "There are " . (count($clients) - 1) . "client(s) connected to the server\n");
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
             $message = array();
             //Break down the message into parts : user_id, message, lat, lng
             $message = explode(";", $data);

             if (count($message) > 0) {
                 $counter = 0;
                 while ($counter < count($message)) {
                     if ($message[$counter] == "-1") {
                         $sql = "insert into session () values ()";
                         $stmt = $db->prepare($sql);
                         $stmt->execute();
                         $id = $db->lastInsertId();

                         socket_write($read_socket, $id);
                     } else if ($message[$counter] != "") {
                         $gpsMapping = explode(",", $message[$counter]);
                         $gps = array(
                             "latitude" => $gpsMapping[0],
                             "longitude" => $gpsMapping[1]
                         );

                         $sql = "select node.ip_address from node left join location using (location_id) where location.upperlat >= :latitude and location.lowerlat < :latitude and location.upperlng >= :longitude and location.lowerlng < :longitude";
                         $stmt = $db->prepare($sql);
                         $stmt->execute($gps);
                         $ip_address = $stmt->fetch(PDO::FETCH_ASSOC)["ip_address"];
                         $ip_address .= ";";

                         socket_write($read_socket, $ip_address);
                     }

                     $counter++;
                 }
             }

             //Respond
             foreach ($clients as $send_socket) {
                 if ($send_socket == $socket) {
                     continue;
                 }
                echo "SEND SOCKET: " . $send_socket . "\n";
                 //socket_write($send_socket, $response);
             }
         }
     }
}

//We are done here
socket_close($socket);