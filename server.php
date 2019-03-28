<?php
$host = "34.73.219.7";
$port = 8001;

$db = new PDO("mysql:host=localhost;dbname=edgeauto", "root", "Bandit08");

set_time_limit(0);

ob_implicit_flush();

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
socket_listen($socket, 3) or die("Could not set up socket listener");
$clients = array($socket);

while(true) {
     $read = $clients;
     $write = null;
     $except = null;

     if (socket_select($read, $write, $except, 0) < 1) {
         continue;
     }

     if (in_array($socket, $read)) {
         $clients[] = $newsocket = socket_accept($socket);
         socket_getpeername($newsocket, $ip, $host);
         socket_write($newsocket, "There are " . (count($clients) - 1) . "client(s) connected to the server\n");
         echo "New client connected: {$ip}\n";
         $key = array_search($socket, $read);
         unset($read[$key]);
    }

    foreach ($read as $read_socket) {
        $data = @socket_read($read_socket, 4096, PHP_BINARY_READ);
        if ($data === false) {
             $key = array_search($read_socket, $clients);
             unset($clients[$key]);
             echo "client disconnected.\n";
             continue;
        }

        $data = trim($data);

        if (!empty($data)) {
             echo " send {$data}\n";

             $message = explode(";", $data);

             $package = [
                 "message" => $message[0],
                 "user_id" => $message[1]
             ];

             $sql = "INSERT INTO messages (message, user_id) values (:message, :user_id)";
             $stmt = $db->prepare($sql);
             $stmt->execute($package);
                echo "Clients: " . print_r($clients, true);
             echo "Package: " . print_r($package, true);

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

socket_close($socket);