<?php
/**
 *
 */

require_once "config.inc.php";
require_once "../vendor/autoload.php";


$sonos = new \duncan3dc\Sonos\Network;

$controllers = $sonos->getControllers();

foreach ($controllers as $controller) {
    // See if we can find our preferred controller.
    if($controller->name === PREFERRED_CONTROLLER) {
        // We have our preferred controller, so exit the loop
        break;
    }
}

// now we have either found our preferred controller, or we have the last in the list,
// in case our preferred controller is grouped someplace else

if($controller->getState() !== \duncan3dc\Sonos\Controller::STATE_PLAYING) {
    $controller->play();
} else {
    $controller->pause();
}