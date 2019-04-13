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
        Particle.publish("INVALID~GPS", PUBLIC);

        String pack = "";
        pack.concat(session_id);
        pack.concat(";");

        client.write(pack);
        delay(2000);

        CANMessage message;
    }

    String response = "";
    while (client.available()) {
        char c = client.read();
        response.concat(c);
    }

    if (!client.connected()) {
        client.connect(mappingServer, 8001);
    }
    //delay(50);
}