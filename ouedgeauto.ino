// This #include statement was automatically added by the Particle IDE.
#include <carloop.h>
#include <string>

//carloop object
Carloop<CarloopRevision2> carloop;

// The TinyGPS++ object
TinyGPSPlus gps;

// The TCPClient object
TCPClient client;
byte server[] = { 34, 73, 219, 7 };
byte server2[] = { 192, 168, 1, 109 };

bool toggle = false;

//Setup the carloop
void setup() {
    //Declare the timezone for messages
    Time.zone(-4);

    //Connect to the hotspot for use in car
    WiFi.on();
    WiFiCredentials credentials("Samsung Galaxy Note8 5908", WPA2);
    credentials.setPassword("12345678");
    credentials.setCipher(WLAN_CIPHER_AES);
    WiFi.setCredentials(credentials);
    WiFi.connect();

    //Connect to the TCP Client to start doing some work!
    client.connect(server, 8001);
    waitFor(WiFi.ready, 500);
    
    carloop.begin();
}

//Create a backlog and a counter for it to keep track of the message order.
int counter = 0;
String backlog[500];

void loop() {
    //Just test stuff for the backlog
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
    }

    //Update the carloop and determine if we have a valid GPS signal
    carloop.update();
    bool gpsValid = carloop.gps().location.isValid();

    //Proceed if you have a valid GPS
    if (gpsValid) {
        //Grab the gps coordinates from the GPS attachment
        float lat = carloop.gps().location.lat();
        float lng = carloop.gps().location.lng();
        String gps = "";
        String package = "";
        
        gps.concat(lat);
        gps.concat(";");
        gps.concat(lng);
        gps.concat(";");
        
        CANMessage message;
        String Canmessage = "";

        //Receive the CANbus message from the carloop
        if (carloop.can().receive(message)) {
            package.concat(message.id);
            package.concat(";");
            for (int i = 0; i < message.len; i++) {
                Canmessage.concat(message.data[i]);
            }
            package.concat(";");
        }

        //Add all of the other information to the package
        package.concat(gps);
        package.concat(";");
        package.concat(Time.timeStr());

        //Its best if we have a client to be connected to, this will be phased out
        if (client.connected()) {
            if (counter > 0) {
                int tCounter = 0;
                while (tCounter < counter) {
                    
                }
            }
            
            while (counter > 0) {
                counter--;
                Particle.publish(backlog[counter]);
            }
            client.write(package);
            Particle.publish(package, PUBLIC);
            delay(200);
        } else {
            backlog[counter] = package; 
            counter++;
        }
    } else {
        //Particle.publish("INVALID~GPS", PUBLIC);      You failed if you came here
    }

    //This will be used to read information from the edge nodes aka THE IP
    String response = "";
    while (client.available()) {
        char c = client.read();
        response.concat(c);
    }
    
    /* Other test stuff, this will allow us to flip between two separate on two separate machines, GOOD NEWS!
    if (counter == 10) {
        if (!toggle) {
            client.stop();
            client.connect(server2, 8001);
        } else {
            client.stop();
            client.connect(server, 8001);
        }
        toggle = !toggle;
    }*/
    delay(100);
}