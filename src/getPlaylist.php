<?php
/**
 *
 */

require_once "config.inc.php";
require_once "../vendor/autoload.php";

$sonos = new \duncan3dc\Sonos\Network;


$jsonPlaylists = array();

// Fetch Sonos-Playlists
$playlists = $sonos->getPlaylists();
foreach ($playlists as $playlist) {
    // Get Album-Cover from 1st track
    $albumArt = false;
    foreach ($playlist->getTracks() as $track) {
        if(strlen($track->albumArt) > 0) {
            $albumArt = $track->albumArt;
            break;
        }
    }
    $jsonPlaylists['items'][] = getJsonPlaylistItem($playlist->getId(), $playlist->getName(), $albumArt, TYPE_PLAYLIST);
}

// Fetch Sonos Radio-Stations
$radio = $sonos->getRadio();
foreach ($radio->getFavouriteStations() as $favouriteStation) {
    $jsonPlaylists['items'][] = getJsonPlaylistItem($favouriteStation->getName(), $favouriteStation->getTitle(), false, TYPE_RADIO_STREAM);
}


die( json_encode($jsonPlaylists) );



function getJsonPlaylistItem($id, $name, $albumArtUrl, $type) {
    if($albumArtUrl === false) {
        $dest = imagecreatefromjpeg(PATH_EMPTY_ART);
    } else {
        $source = imagecreatefromjpeg($albumArtUrl);
        $width = imagesx($source);
        $height = imagesy($source);
        $dest   = imagecreatetruecolor(IMG_WIDTH,IMG_HEIGHT);
        imagecopyresized($dest, $source, 0, 0, 0, 0, IMG_WIDTH,IMG_HEIGHT, $width,$height);
    }

    if($albumArtUrl === false) {
        $black = ImageColorAllocate($dest, 0, 0, 0);
        $white = ImageColorAllocate($dest, 255, 255, 255);
        ImageTTFText($dest, 20, 315, 10, 20, $black, FONT_NAME, $name);
    }
    imagejpeg($dest, TMP_PATH."/".$id);

    if(isset($source)) {
        imagedestroy($source);
    }
    imagedestroy($dest);

    $jsonPlaylist = array(
        'id' => $id,
        'name' => $name,
        'type' => $type,
        'albumArt' => "getCover.php/".$id,
    );
    return $jsonPlaylist;
}