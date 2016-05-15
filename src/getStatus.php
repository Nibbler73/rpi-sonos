<?php
/**
 * Collect Status-Information of Sonos-Network
 */

require_once "config.inc.php";
require_once "../vendor/autoload.php";

// Find Network and our Controller
$sonos = new \duncan3dc\Sonos\Network;

$controllers = $sonos->getControllers();
$controller = null;

foreach ($controllers as $controller) {
    // See if we can find our preferred controller.
    if ($controller->name === PREFERRED_CONTROLLER) {
        // We have our preferred controller, so exit the loop
        break;
    }
}

// Collect Status Information
$result = array();

// Playing State
if ($controller->getState() == \duncan3dc\Sonos\Controller::STATE_PLAYING) {
    $result['playingState'] = JS_STATE_PLAYING;
} else {
    $result['playingState'] = JS_STATE_PAUSED;
}
// Repeat State
$result['repeat'] = $controller->getRepeat();

// Type of Music: e.g. Playlist or Radio-Stream
if ($controller->isUsingQueue()) {
    // We have a Queue, so probably it's a Playlist of some kind
    $result['type'] = TYPE_SONOS_PLAYLIST;
} else {
    // Not using Queue, so we assume it's a Radio-Stream
    $result['type'] = TYPE_RADIO_STREAM;
}

// Find out whether the ScreenSaver is currently active/Monitor switched off
// *** Note: For this to work, make the xAuthority-File of your pi-user readable
// sudo chgrp www-data .Xauthority
// sudo chmod g+r .Xauthority
try {
    $auth = "/home/pi/.Xauthority";
    putenv("DISPLAY=:0");
    putenv("XAUTHORITY={$auth}");
    $last_line = exec('/usr/bin/xset q', $xSetOutput);
    $result['monitorOn'] = strpos($last_line, "Monitor is Off") === false;
} catch(Exception $e) {
    echo $e->getMessage();
}

// return answer to JavaScript
header("Content-Type: application/json");
die( json_encode($result) );
