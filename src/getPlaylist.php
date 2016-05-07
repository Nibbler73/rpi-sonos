<?php
/**
 *
 */

require_once "config.inc.php";
require_once "../vendor/autoload.php";

$sonos = new \duncan3dc\Sonos\Network;

$playlists = $sonos->getPlaylists();

$jsonPlaylists = array();

foreach ($playlists as $playlist) {

    $albumArt = false;
    foreach ($playlist->getTracks() as $track) {
        if(strlen($track->albumArt) > 0) {
            $albumArt = $track->albumArt;
            break;
        }
    }

    $jsonPlaylists['items'][] = getJsonPlaylistItem($playlist->getId(), $playlist->getName(), $albumArt);
}

die( json_encode($jsonPlaylists) );



function getJsonPlaylistItem($id, $name, $albumArtUrl) {
    if($albumArtUrl === false) {
        $source = imagecreatefromjpeg(PATH_EMPTY_ART);
    } else {
        $source = imagecreatefromjpeg($albumArtUrl);
    }
    $width = imagesx($source);
    $height = imagesy($source);
    $dest   = imagecreatetruecolor(IMG_WIDTH,IMG_HEIGHT);
    imagecopyresized($dest, $source, 0, 0, 0, 0, IMG_WIDTH,IMG_HEIGHT, $width,$height);

    if($albumArtUrl === false) {
        $black = ImageColorAllocate($dest, 0, 0, 0);
        $white = ImageColorAllocate($dest, 255, 255, 255);
        ImageTTFText($dest, 20, 315, 10, 20, $white, FONT_NAME, "Bild fehlt - leer?");
    }
    imagejpeg($dest, TMP_PATH."/".$id);

    imagedestroy($source);imagedestroy($dest);

    $jsonPlaylist = array(
        'id' => $id,
        'name' => $name,
        'albumArt' => "getCover.php/".$id,
    );
    return $jsonPlaylist;
}