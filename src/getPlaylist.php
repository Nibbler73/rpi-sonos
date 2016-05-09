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
    if($albumArt !== false) {
        $jsonPlaylists['items'][] = getJsonPlaylistItem($playlist->getId(), $playlist->getName(), $albumArt, TYPE_SONOS_PLAYLIST);
    }
}

// Fetch Sonos Radio-Stations
$radio = $sonos->getRadio();
foreach ($radio->getFavouriteStations() as $favouriteStation) {
    $jsonPlaylists['items'][] = getJsonPlaylistItem($favouriteStation->getName(), $favouriteStation->getName(), false, TYPE_RADIO_STREAM);
}
foreach ($radio->getFavouriteShows() as $favouriteStation) {
    $jsonPlaylists['items'][] = getJsonPlaylistItem($favouriteStation->getName(), $favouriteStation->getName(), false, TYPE_RADIO_STREAM);
}

// I guess we are playing something now
$result['playingState'] = JS_STATE_PLAYING;

die( json_encode($jsonPlaylists) );

/*
 * Helper Function for Text
 * Source: https://wmh.github.io/hunbook/examples/gd-imagettftext.html
 */
function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {
    for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
        for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
            $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
    return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
}


/**
 * @param $id
 * @param $name
 * @param $albumArtUrl
 * @param $type
 * @return array
 */

function getJsonPlaylistItem($id, $name, $albumArtUrl, $type) {
    if($albumArtUrl === false) {
        $dest = imagecreatefromjpeg(PATH_EMPTY_ART);
    } else {
        $source = imagecreatefromjpeg($albumArtUrl);
        $width  = imagesx($source);
        $height = imagesy($source);
        $dest   = imagecreatetruecolor(IMG_WIDTH,IMG_HEIGHT);
        imagecopyresized($dest, $source, 0, 0, 0, 0, IMG_WIDTH,IMG_HEIGHT, $width,$height);
    }

    if($albumArtUrl === false) {
        // https://stackoverflow.com/questions/15982732/php-gd-align-text-center-horizontally-and-decrease-font-size-to-keep-it-inside
        // find font-size for $txtWidth = 80% of $img_width...
        $fontSize = 20;
        $txtMaxWidth = intval(0.8 * IMG_WIDTH);

        do {
            $fontSize++;
            $p = imagettfbbox($fontSize, $angle=335, FONT_NAME, $name);
            $txtWidth=$p[2]-$p[0];

        } while ($txtWidth <= $txtMaxWidth && $fontSize < 28);

        // now center the text
        $y = IMG_HEIGHT * 0.9; // baseline of text at 90% of $img_height
        $x = (IMG_WIDTH - $txtWidth) / 2;

        $black = ImageColorAllocate($dest, 0, 0, 0);
        $white = ImageColorAllocate($dest, 255, 255, 255);
        imagettfstroketext($dest, $fontSize, $angle=335, $x, $y=80, $white, $black, FONT_NAME, $name, $px=2);
        //ImageTTFText($dest, 20, 315, 10, 20, $black, FONT_NAME, $name);
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