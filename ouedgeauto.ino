// This #include statement was automatically added by the Particle IDE.
#include <carloop.h>
#include <string>
#include <ctime>

//carloop object
Carloop<CarloopRevision2> carloop;

// The TinyGPS++ object
TinyGPSPlus gps;

TCPClient client;
byte mappingServer[] = { 35, 237, 136, 160 };
byte server[4] = {};
String currentServer = "";
bool mapped = false;
int counter = 0;
int xcounter = 0;
int session_id = -1;
String backlog[500];
bool available = false;

// Let's get this thing going!
void setup() {
    Particle.publish("Initialize", PUBLIC);
    Time.zone(-4);

    // Connect to a hotspot(aka WiFi).
    WiFi.on();
    WiFiCredentials credentials("Samsung Galaxy Note8 5908", WPA2);
    credentials.setPassword("12345678");
    credentials.setCipher(WLAN_CIPHER_AES);
    WiFi.setCredentials(credentials);
    WiFi.connect();
    waitFor(WiFi.ready, 1000);

    // Make sure you are connected to the mapping node.
    while (!client.connected()) {
        client.connect(mappingServer, 8001);
    }

    // Tell the mapping node a little bit about yourself.
    String pack = "";
    pack.concat(session_id);
    pack.concat(";");
    client.write(pack);

    // Wait until they write back.
    String response = "";
    while (!client.available()) {
        //Wait until you get a response back
    }
    while (client.available()) {
        char c = client.read();
        response.concat(c);
    }
    Particle.publish(response, PUBLIC);
    session_id = response.toInt();

    // Now we know who you are.
    carloop.begin();
    Particle.publish("BEGIN", PUBLIC);
}

// Keep going round and round.
void loop() {
    carloop.update();
    bool gpsValid = carloop.gps().location.isValid();

    // Are you actually somewhere or nowhere?
    if (gpsValid) {
        float lat = carloop.gps().location.lat();
        float lng = carloop.gps().location.lng();

        if (mapped) {
            String gps = "";
            String package = "";

            gps.concat(lat);
            gps.concat(",");
            gps.concat(lng);

            CANMessage message;
            String Canmessage = "";

            if (carloop.can().receive(message)) {
                package.concat(message.id);
                package.concat(",");
                for (int i = 0; i < message.len; i++) {
                    Canmessage.concat(message.data[i]);
                }
                package.concat(Canmessage);
                package.concat(",");
            }
            package.concat(gps);
            package.concat(",");
            package.concat(millis());
            package.concat(",");
            package.concat(session_id);
            package.concat(";");

            if (client.connected()) {
                client.write(package);
            } else {
                while (!client.connected()) {
                    client.connect(server, 8001);
                }
                client.write(package);
            }

            // If someone is talking to you, you might want to listen.
            String temp = "";
            String response = "";
            byte tempServer[4] = {};

            while (client.available()) {
                char c = client.read();
                if (c == '.') {
                    unsigned int value = response.toInt();
                    tempServer[counter] = value;
                    temp.concat(response);
                    temp.concat(".");
                    response = "";
                    counter++;
                } else if (c == ';') {
                    unsigned int value = response.toInt();
                    tempServer[counter] = value;
                    temp.concat(response);
                } else {
                    response.concat(c);
                }
                available = true;
            }

            // If you have new information, go with it.
            if (available && currentServer.compareTo(temp) != 0) {
                currentServer = temp;
                counter = 0;
                while (counter < 4) {
                    server[counter] = tempServer[counter];
                    counter++;
                }
                Particle.publish("WHY THE FUCK YOU COME HERE YOU INBRED", PUBLIC);
                client.stop();
                client.connect(server, 8001);
                available = false;
            }
        } else {
            // If the carloop hasn't been mapped, gather your documentation and figure out where you go.
            // Collect the gps data.
            String gps = "";
            gps.concat(lat);
            gps.concat(",");
            gps.concat(lng);
            gps.concat(";");

            client.write(gps);

            String response = "";
            counter = 0;

            //Wait until the server responds.
            while (!client.available()) {
                //Wait until you get a response back
            }
            // Great! You have a response, now construct it.
            while (client.available()) {
                char c = client.read();
                if (c == '.') {
                    unsigned int value = response.toInt();
                    server[counter] = value;
                    currentServer.concat(response);
                    currentServer.concat(response);
                    response = "";
                    counter++;
                } else if (c == ';') {
                    unsigned int value = response.toInt();
                    server[counter] = value;
                    currentServer.concat(response);
                } else {
                    response.concat(c);
                }
            }

            // Stop talking to the mapping node and move onto the next node. You are now mapped!
            client.stop();
            client.connect(server, 8001);
            mapped = true;
        }
    } else {
        // Notify the console and wait until GPS is proper.
        Particle.publish("INVALID~GPS", PUBLIC);
        delay(1000);
    }
    xcounter++;
}