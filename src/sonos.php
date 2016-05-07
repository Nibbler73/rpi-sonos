<?php
/**
 *
 * Ajax handler, returns a list of Favorites and their Icons
 *
 *
 */

require_once "config.inc.php";
require_once "../vendor/autoload.php";

// use duncan3dc\Sonos\Tracks\Track;

$sonos = new \duncan3dc\Sonos\Network;

$controllers = $sonos->getControllers();

echo "<p>Controllers:</p>";
foreach ($controllers as $controller) {
    echo $controller->name . " (" . $controller->room . ")<br/>\n";
    echo "\tState: " . $controller->getState() . "<br/>\n";
    //$controller->play();
    echo "<pre>\n";
    print_r($controller->getMediaInfo());
    echo "</pre><br/>\n";
    echo "<pre>\n";
    print_r($controller->getMode());
    echo "</pre><br/>\n";
}


$playlists = $sonos->getPlaylists();

echo "<p>Playlists:</p>";
foreach ($playlists as $playlist) {
    echo $playlist->getName() . "<br/>\n";
    echo $playlist->getId() . "<br/>\n";
    foreach ($playlist->getTracks() as $track) {
//        echo "* {$track->artist} - {$track->title} // {$track->albumArt} <br/>\n";
    }
}


$radio = $sonos->getRadio();

echo "<p>Radios:</p>";
foreach ($radio->getFavouriteStations() as $favouriteStation) {
    echo $favouriteStation->getName() . "<br/>\n";
    echo $favouriteStation->getTitle() . "<br/>\n";
    echo $favouriteStation->getUri() . "<br/>\n";
}


