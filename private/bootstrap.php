<?php

/* Defines the constants of the application */

define('PATH_PRIVATE', __DIR__ . '/');
define('PATH_LIB', PATH_PRIVATE . 'lib/');
define('PATH_CONFIG', PATH_PRIVATE . 'config/');
define('PATH_DOC', PATH_PRIVATE . 'doc/');
define('PATH_CONTROLLERS', PATH_PRIVATE . 'controllers/');
define('PATH_TEMPLATES', PATH_PRIVATE . 'templates/');

define('PATH_PUBLIC', __DIR__ . '/../public/');

/* -- Loads all librairies in PATH_LIB (no autoloader for this simple app) -- */
if (!$rDir = opendir(PATH_LIB)){ 
    throw new Exception('Failed to open directory for librairies');
}
while (($sFile = readdir($rDir)) !== false) {
    if ($sFile != '.' AND $sFile != '..'){
        require_once PATH_LIB. $sFile;
    };    
}
closedir($rDir);
/* end of loading */

/* basically dispatches the request */
$sRequest_uri = $_SERVER['REQUEST_URI'];
$sRequest_uri = str_replace('index.php', '', $sRequest_uri);
// 'Page not found' for all requests with no correspondant controller
if (!file_exists(PATH_CONTROLLERS.$sRequest_uri.'.php')){
    include (PATH_TEMPLATES.'notFound.html');
    die;
}

// include controller
include_once PATH_CONTROLLERS.$sRequest_uri.'.php';

// render the corresponding template
include_once PATH_TEMPLATES.$sRequest_uri.'.html';






