<?php
/**
 *
 */

require_once "config.inc.php";
require_once "../vendor/autoload.php";

$playlistId = $_REQUEST['id'];

if(strlen($playlistId) > 0) {
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

    // now we have either found our preferred controller, or we have the last in the list,
    // in case our preferred controller is grouped someplace else
    if($_REQUEST['type'] == TYPE_PLAYLIST) {
        playFromPlaylist($controller, $playlistId);
    } elseif($_REQUEST['type'] == TYPE_RADIO_STREAM) {
        playFromRadioStream($controller, $playlistId);
    }


}


function playFromPlaylist(\duncan3dc\Sonos\Controller $controller, $playlistId) {
    $sonos = $controller->getNetwork();

    // Make sure the Controller is using a queue
    if (!$controller->isUsingQueue()) {
        $controller->useQueue();
    }

    // Pause, if currently playing
    if ($controller->getState() === \duncan3dc\Sonos\Controller::STATE_PLAYING) {
        $controller->pause();
    }
    $playlist = $sonos->getPlaylistById($playlistId);
    $tracks = $playlist->getTracks();
    $queue = $controller->getQueue();
    $queue->clear();
    $queue->addTracks($tracks);
    $controller->play();
}


function playFromRadioStream(\duncan3dc\Sonos\Controller $controller, $streamName) {
    $sonos = $controller->getNetwork();
    $radio = $sonos->getRadio();
    if ($show = $radio->getFavouriteStation($streamName)) {
        $controller->useStream($show)->play();
    }
}