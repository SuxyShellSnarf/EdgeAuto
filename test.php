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
$decrement = 0.5;
$y = 180;
$latitude = array();
$longitude = array();

while ($x >= -90) {
    $package = array(
        "lat" => $x
    );
    //echo print_r($package, true);
    $latitude[] = $package;
    $x = $x - $decrement;
}

$minutes = 0;

while ($y >= -180) {
    $package = array(
        "lng" => $y
    );
    //echo print_r($package, true);
    $longitude[] = $package;

    $y = $y - $decrement;

}
/*
echo print_r($latitude, true);
echo print_r($longitude, true);

echo print_r($latitude[0], true);
echo print_r($latitude[1800], true);
echo print_r($longitude[0], true);
echo print_r($longitude[3600], true);
*/
/*
$counter = 0;
foreach ($latitude as $lat) {
    echo $counter . "\n";
    $counter++;
    foreach ($lat as $l => $v) {
        echo $l . " : " . $v . "\n";
    }
}

$counter = 0;
foreach ($longitude as $lng) {
    echo $counter . "\n";
    $counter++;
    foreach ($lng as $l => $v) {
        echo $l . " : " . $v . "\n";
    }
}*/

$coordinates = array();

echo count($latitude);
echo count($longitude);

$latcounter = 0;
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

echo count($coordinates);

/*
$counter = 0;
while ($counter < count($coordinates)) {
    $string = "insert into location (upperlat_degree, upperlat_minutes, lowerlat_degree, lowerlat_minutes, upperlng_degree, upperlng_minutes, lowerlng_degree, lowerlng_minutes) values (" . $coordinates[$counter]["upperlat_degree"] . ", " . $coordinates[$counter]["upperlat_minutes"] . ", " . $coordinates[$counter]["lowerlat_degree"] . ", " . $coordinates[$counter]["lowerlat_minutes"] . ", " . $coordinates[$counter]["upperlng_degree"] . ", " . $coordinates[$counter]["upperlng_minutes"] . ", " . $coordinates[$counter]["lowerlng_degree"] . ", " . $coordinates[$counter]["lowerlng_minutes"] . ")\n";
    echo $string;
}
