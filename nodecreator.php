<?php
/**
 * Created by PhpStorm.
 * User: SuxyShellSnarf
 * Date: 2019-04-02
 * Time: 22:56
 */

$coordinates[] = array(
    array(
        "lat_degrees" => "42",
        "lat_minutes" => "68",
        "lng_degrees" => "-83",
        "lng_minutes" => "22"
    ), array(
        "lat_degrees" => "42",
        "lat_minutes" => "68",
        "lng_degrees" => "-83",
        "lng_minutes" => "21"
    ), array(
        "lat_degrees" => "42",
        "lat_minutes" => "68",
        "lng_degrees" => "-83",
        "lng_minutes" => "20"
    )
);

$coordinates[] = array(
    array(
        "lat_degrees" => "42",
        "lat_minutes" => "67",
        "lng_degrees" => "-83",
        "lng_minutes" => "22"
    ), array(
        "lat_degrees" => "42",
        "lat_minutes" => "67",
        "lng_degrees" => "-83",
        "lng_minutes" => "21"
    ), array(
        "lat_degrees" => "42",
        "lat_minutes" => "67",
        "lng_degrees" => "-83",
        "lng_minutes" => "20"
    )
);

$coordinates[] = array(
    array(
        "lat_degrees" => "42",
        "lat_minutes" => "67",
        "lng_degrees" => "-83",
        "lng_minutes" => "22"
    ), array(
        "lat_degrees" => "42",
        "lat_minutes" => "67",
        "lng_degrees" => "-83",
        "lng_minutes" => "21"
    ), array(
        "lat_degrees" => "42",
        "lat_minutes" => "67",
        "lng_degrees" => "-83",
        "lng_minutes" => "20"
    )
);

foreach ($coordinates as $lat) {
    foreach ($lat as $coord => $v) {
        echo $coord["lat_degrees"] . "." . $coord["lat_minutes"] . "\n";
        echo $coord["lng_degrees"] . "." . $coord["lng_minutes"] . "\n";
    }
}