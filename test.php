<?php
/**
 * Created by PhpStorm.
 * User: SuxyShellSnarf
 * Date: 2019-04-03
 * Time: 23:30
 */

$db = new PDO("mysql:host=localhost;dbname=EdgeAuto", "edgeauto", "edgeauto19!");

ini_set('memory_limit', '-1');

$x = 90;
$decrement = 0.3;
$y = 180;
$latitude = array();
$longitude = array();
/*
while ($x >= -90) {
    $package = array(
        "lat" => $x
    );
    $latitude[] = $package;
    $x = $x - $decrement;
}

while ($y >= -180) {
    $package = array(
        "lng" => $y
    );
    $longitude[] = $package;

    $y = $y - $decrement;

}

$coordinates = array();
$latcounter = 0;

echo count($latitude);
echo count($longitude);

while ($latcounter < count($latitude) - 1) {
    $lngcounter = 0;
    while ($lngcounter < count($longitude) - 1) {
        $package = array(
            "upperlat" => $latitude[$latcounter]["lat"],
            "lowerlat" => $latitude[$latcounter + 1]["lat"],
            "upperlng" => $longitude[$lngcounter]["lng"],
            "lowerlng" => $longitude[$lngcounter + 1]["lng"]
        );
        $string = "insert into location1 (upperlat, lowerlat, upperlng, lowerlng) values (:upperlat, :lowerlat, :upperlng, :lowerlng)";
        $stmt = $db->prepare($string);
        $stmt->execute($package);
        echo $string;
        $coordinates[] = $package;
        $lngcounter++;
    }
    $latcounter++;
}
*/
$package = array(
    "latitude" => 42.67831,
    "longitude" => -83.21423
);

$sql = "select location_id from location where upperlat >= :latitude and lowerlat <= :latitude and upperlng >= :longitude and lowerlng <= :longitude";
$stmt = $db->prepare($sql);
$stmt->execute($package);
$location_id = $stmt->fetch(PDO::FETCH_ASSOC);
echo print_r($location_id);

$package = array(
    "latitude" => 42.67885,
    "longitude" => -83.20917
);

$sql = "select location_id from location where upperlat >= :latitude and lowerlat <= :latitude and upperlng >= :longitude and lowerlng <= :longitude";
$stmt = $db->prepare($sql);
$stmt->execute($package);
$location_id = $stmt->fetch(PDO::FETCH_ASSOC);
echo print_r($location_id);

$package = array(
    "latitude" => 42.67298,
    "longitude" => -83.21556
);

$sql = "select location_id from location where upperlat >= :latitude and lowerlat <= :latitude and upperlng >= :longitude and lowerlng <= :longitude";
$stmt = $db->prepare($sql);
$stmt->execute($package);
$location_id = $stmt->fetch(PDO::FETCH_ASSOC);
echo print_r($location_id);

$package = array(
    "latitude" => 42.67247,
    "longitude" => -83.20762
);

echo "Y\n";
$sql = "select location_id from location where upperlat >= :latitude and lowerlat < :latitude and upperlng >= :longitude and lowerlng < :longitude";
echo "Z\n";
$stmt = $db->prepare($sql);
echo "A\n";
$stmt->execute($package);
echo "B\n";
$location_id = $stmt->fetch(PDO::FETCH_ASSOC)["location_id"];
echo "\n" . $location_id . "\n";