<?php
/**
 * Configuration of Project
 */

define('PREFERRED_CONTROLLER', 'Spielzimmer');
define('TYPE_SKIP_PREVIOUS',    3201);
define('TYPE_PLAY_PAUSE',       3202);
define('TYPE_SKIP_NEXT',        3203);
define('TYPE_REPEAT',           3204);
define('TYPE_SONOS_PLAYLIST',   3301);
define('TYPE_RADIO_STREAM',     3302);

define('FONT_NAME', '/usr/share/fonts/truetype/freefont/FreeSans.ttf');

/*
 * System configuration, you probably so not want to change anything in here
 */

define('TMP_PATH', '/tmp');
define('PATH_EMPTY_ART', 'img/flowers.jpg');

define('IMG_WIDTH', 225);
define('IMG_HEIGHT', 250);

define('JS_STATE_PLAYING', 'playing');
define('JS_STATE_PAUSED', 'paused');
