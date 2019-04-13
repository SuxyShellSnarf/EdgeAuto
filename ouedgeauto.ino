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
String tag = "";
bool mapped = false;
int counter = 0;
int session_id = -1;
String backlog[500];

void setup() {
    Time.zone(-4);
    WiFi.on();
    WiFiCredentials credentials("Lucas's iPhone", WPA2);

    credentials.setPassword("mrsaxy10");
    credentials.setCipher(WLAN_CIPHER_AES);

    WiFi.setCredentials(credentials);
    WiFi.connect();

    client.connect(mappingServer, 8001);
    String response = "";
    while (client.available()) {
        char c = client.read();
        response.concat(c);
    }
    waitFor(WiFi.ready, 1000);

    carloop.begin();
    Particle.publish("Begin", PUBLIC);
}

void loop() {
    carloop.update();
    bool gpsValid = carloop.gps().location.isValid();

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
            package.concat(";");

            if (client.connected()) {
                client.write(package);
                Particle.publish(package, PUBLIC);
            } else {
                client.connect(mappingServer, 8001);
                client.write(package);
                Particle.publish(package, PUBLIC);
            }
        } else {
            if (client.connected()) {

            } else {

            }
        }
    } else {
        //Particle.publish("INVALID~GPS", PUBLIC);

        if (session_id == -1) {
            String pack = "";
            pack.concat(session_id + ";");

            client.write(pack);
            delay(500);
            String response = "";
            while (client.available()) {
                char c = client.read();
                response.concat(c);
            }
            Particle.publish(response, PUBLIC);
            session_id = response.toInt();
        } else {
            String package = "42.8,-83.5";
            client.write(package);

            delay(500);

            String response = "";
            counter = 0;
            while (client.available()) {
                char c = client.read();
                if (c == '.') {
                    unsigned int value = response.toInt();
                    server[counter] = value;

                    response = "";
                    counter++;
                } else {
                    response.concat(c);
                }
            }

            delay(1000);
            Particle.publish(String(server[0]), PUBLIC);
            delay(1000);
            Particle.publish(String(server[1]), PUBLIC);
            delay(1000);
            Particle.publish(String(server[2]), PUBLIC);
            delay(1000);
            Particle.publish(String(server[3]), PUBLIC);


            client.stop();
            delay(4000);
            client.connect(server, 8001);
            if (client.connected()) {
                Particle.publish("WOO", PUBLIC);
            }
        }
    }

    /*if (!client.connected()) {
        client.connect(mappingServer, 8001);
    }*/
}