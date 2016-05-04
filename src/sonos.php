<?php
/**
 * Created by PhpStorm.
 * User: hrvoje
 * Date: 04.05.16
 * Time: 22:13
 */

require_once "../vendor/autoload.php";

// use duncan3dc\Sonos\Tracks\Track;

$sonos = new \duncan3dc\Sonos\Network;
$controllers = $sonos->getControllers();

foreach ($controllers as $controller) {
    echo $controller->name . " (" . $controller->room . ")<br/>\n";
    echo "\tState: " . $controller->getState() . "<br/>\n";
    //$controller->play();
}


$playlists = $sonos->getPlaylists();

foreach ($playlists as $playlist) {
    echo $playlist->getName() . "<br/>\n";
    echo $playlist->getId() . "<br/>\n";
    foreach ($playlist->getTracks() as $track) {
        echo "* {$track->artist} - {$track->title} // {$track->albumArt} <br/>\n";
    }
}


