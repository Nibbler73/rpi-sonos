<?php
/**
 *
 */

require_once "config.inc.php";
require_once "../vendor/autoload.php";

$playlistId = $_REQUEST['id'];

$result = array();

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
    if($_REQUEST['type'] == TYPE_PLAY_PAUSE) {
        togglePlayPause($controller);
    } elseif($_REQUEST['type'] == TYPE_SKIP_NEXT) {
        toggleSkipNext($controller);
    } elseif($_REQUEST['type'] == TYPE_SKIP_PREVIOUS) {
        toggleSkipPrevious($controller);
    } elseif($_REQUEST['type'] == TYPE_REPEAT) {
        toggleRepeat($controller);
    } elseif($_REQUEST['type'] == TYPE_SONOS_PLAYLIST) {
        playFromPlaylist($controller, $playlistId);
    } elseif($_REQUEST['type'] == TYPE_RADIO_STREAM) {
        playFromRadioStream($controller, $playlistId);
    }


}

// Response should contain player's state
$result['type'] = $_REQUEST['type'];
// Give the controller a second to react to the input so far
sleep(1);
// Playing State
if ($controller->getState() == \duncan3dc\Sonos\Controller::STATE_PLAYING) {
    $result['playingState'] = JS_STATE_PLAYING;
} else {
    $result['playingState'] = JS_STATE_PAUSED;
}
// Repeat State
$result['repeat'] = $controller->getRepeat();

// return answer to JavaScript
die( json_encode($result) );


function toggleSkipNext(\duncan3dc\Sonos\Controller $controller) {
    $controller->next();
}
function toggleSkipPrevious(\duncan3dc\Sonos\Controller $controller) {
    if($controller->isUsingQueue()) {
        try {
            $controller->previous();
        } catch(Exception $e) {
            // previous() got an Exception, so try to seek instead
            $controller->seek(0);
        }
    }
}
function toggleRepeat(\duncan3dc\Sonos\Controller $controller) {
    $repeat = $controller->getRepeat();
    $controller->setRepeat($repeat === false);
}

function togglePlayPause(\duncan3dc\Sonos\Controller $controller) {
    if ($controller->getState() == \duncan3dc\Sonos\Controller::STATE_PLAYING) {
        $controller->pause();
    } else {
        $controller->play();
    }
}

function playFromPlaylist(\duncan3dc\Sonos\Controller $controller, $playlistId) {
    $sonos = $controller->getNetwork();

    // Make sure the Controller is using a queue
    if (!$controller->isUsingQueue()) {
        $controller->useQueue();
    }

    // Pause, if currently playing
    if ($controller->getState() == \duncan3dc\Sonos\Controller::STATE_PLAYING) {
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