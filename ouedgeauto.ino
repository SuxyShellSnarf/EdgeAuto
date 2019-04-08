// This #include statement was automatically added by the Particle IDE.
#include <carloop.h>
#include <string>
#include <ctime>

//carloop object
Carloop<CarloopRevision2> carloop;

// The TinyGPS++ object
TinyGPSPlus gps;

TCPClient client;
byte server[] = { 35, 185, 99, 21};
//byte server2[] = { 104, 196, 99, 233 };

bool toggle = false;

void setup() {
    Time.zone(-4);
    WiFi.on();
    WiFiCredentials credentials("Samsung Galaxy Note8 5908", WPA2);

    credentials.setPassword("12345678");
    credentials.setCipher(WLAN_CIPHER_AES);

    WiFi.setCredentials(credentials);
    WiFi.connect();

    client.connect(server, 8001);
    waitFor(WiFi.ready, 1000);

    carloop.begin();
}

int counter = 0;
String backlog[500];

void loop() {
    /*
    if (counter < 400) {
        backlog[counter] = counter;
        counter++;
    } else {
        int tCounter = 0;
        while (tCounter < counter){
            String package = "";
            package.concat(backlog[tCounter]);
            package.concat(";1");
            client.write(package);
            tCounter++;
            delay(200);
        }
        counter = 0;
    }*/
    carloop.update();

    /*
    if (client.connected()) {
        String package = "ID = ";
        CANMessage message;
        String Canmessage = "";

        if (carloop.can().receive(message)) {
            package.concat(message.id);
            package.concat(", MESSAGE = ");
            for (int i = 0; i < message.len; i++) {
                Canmessage.concat(message.data[i]);
            }
            package.concat(Canmessage);
            package.concat(", ");
            package.concat(", TIME = ");
            package.concat(Time.timeStr());
            package.concat(";1");
        }
        client.write(package);
        Particle.publish(package, PUBLIC);
    }*/

    bool gpsValid = carloop.gps().location.isValid();

    if (gpsValid) {
        float lat = carloop.gps().location.lat();
        float lng = carloop.gps().location.lng();
        /*if (lat < 42.839807) {
            client.stop();
            client.connect(server2, 8001);
        }*/
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
            /*if (counter > 0) {
                int tCounter = 0;
                while (tCounter < counter) {

                }
            }

            while (counter > 0) {
                counter--;
                Particle.publish(backlog[counter]);
            }*/
            client.write(package);
            Particle.publish(package, PUBLIC);
        } else {
            client.connect(server, 8001);
            client.write(package);
            Particle.publish(package, PUBLIC);
        }
    } /*else {
        //Particle.publish("INVALID~GPS", PUBLIC);

        CANMessage message;
        String Canmessage = "";
        String package = "";

        if (carloop.can().receive(message)) {
            package.concat(message.id);
            package.concat(",");
            for (int i = 0; i < message.len; i++) {
                Canmessage.concat(message.data[i]);
            }
            package.concat(Canmessage);
            package.concat(",");
        }
        package.concat(Time.timeStr());
        Particle.publish(Time.timeStr());
        package.concat(";1");
        client.write(package);*
    }/

    /*String response = "";
    while (client.available()) {
        char c = client.read();
        response.concat(c);
    }*/

    /*if (counter == 15) {
        if (!toggle) {
            client.stop();
            client.connect(server2, 8001);
            counter = 0;
        } else {
            client.stop();
            client.connect(server, 8001);
            counter = 0;
        }
        toggle = !toggle;
    } else {
        counter++;
    }*/
}