<?php

$app['name'] = 'My app';
$app['version'] = '0.1';

// Local
date_default_timezone_set("UTC");
$app['locale'] = 'en';
$app['session.default_locale'] = $app['locale'];
$app['translator.messages'] = array(
    'fr' => __DIR__.'/../resources/locales/fr.yml',
);

$app['basepath'] = realpath(__DIR__.'/../../');
$app['bower.basepath'] = $app['basepath'].'/bower_components';
$app['vendor.path'] = $app['basepath'].'/vendor';

// Cache
$app['cache.path'] = __DIR__ . '/../cache';

// Http cache
$app['http_cache.cache_dir'] = $app['cache.path'] . '/http';

// Twig cache
$app['twig.options.cache'] = $app['cache.path'] . '/twig';

// Assetic
$app['assetic.enabled']              = true;
$app['assetic.path_to_cache']        = $app['cache.path'] . '/assetic' ;
$app['assetic.path_to_web']          = __DIR__ . '/../../web/assets';
$app['assetic.path_to_node']       = trim(`which nodejs`, "\n");
$app['assetic.node_paths']       = explode(PATH_SEPARATOR,'/usr/lib/nodejs:/usr/lib/node_modules:/usr/share/javascript');
//$app['assetic.path_to_yui_compressor']       = $app['vendor.path'].'/bin/yuicompressor.jar';
$app['assetic.path_to_uglifyjs']       = $app['bower.basepath'].'/uglify-js/bin/uglifyjs';

$app['assetic.input.path_to_assets'] = __DIR__ . '/../assets';

$app['assetic.input.path_to_css']       = $app['assetic.input.path_to_assets'] . '/less/style.less';
$app['assetic.output.path_to_css']      = 'css/styles.css';
$app['assetic.input.path_to_js']        = array(
    // --- jQuery
    $app['bower.basepath'].'/jquery/jquery.js',
    // --- Bootstrap
    $app['bower.basepath'].'/bootstrap/js/affix.js',
    $app['bower.basepath'].'/bootstrap/js/alert.js',
    $app['bower.basepath'].'/bootstrap/js/button.js',
    $app['bower.basepath'].'/bootstrap/js/carousel.js',
    $app['bower.basepath'].'/bootstrap/js/collapse.js',
    $app['bower.basepath'].'/bootstrap/js/dropdown.js',
    $app['bower.basepath'].'/bootstrap/js/modal.js',
    $app['bower.basepath'].'/bootstrap/js/scrollspy.js',
    $app['bower.basepath'].'/bootstrap/js/tooltip.js',
    $app['bower.basepath'].'/bootstrap/js/transition.js',
    // has to be put after tooltip.js
    $app['bower.basepath'].'/bootstrap/js/popover.js',
    // --- Knockout
    $app['bower.basepath'].'/knockout/index.js',
    // --- autosize textareas
    $app['bower.basepath'].'/jquery-autosize/jquery.autosize.min.js',
    // --- Custom scripts
    $app['assetic.input.path_to_assets'] . '/js/script.js',
);
$app['assetic.output.path_to_js']       = 'js/scripts.js';

// NOTE: configure the database access in runtime-conf.xml

// User
$app['security.users'] = array('username' => array('ROLE_USER', 'password'));
