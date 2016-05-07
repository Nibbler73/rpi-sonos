<?php
/**
 */

require_once "config.inc.php";

$filename = TMP_PATH . $_SERVER['PATH_INFO'];

$imageString = file_get_contents($filename);

$mimetype = image_type_to_mime_type(IMAGETYPE_JPEG);

header("Content-Type: " . $mimetype);

echo $imageString;
