<?php

use Assetic\Asset\AssetCache;
use Assetic\Asset\GlobAsset;
use Assetic\Cache\FilesystemCache;
use Assetic\Filter\LessFilter;
use Assetic\Filter\Yui\CssCompressorFilter;
use Assetic\Filter\Yui\JsCompressorFilter;
use Propel\Silex\PropelServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use SilexAssetic\AsseticServiceProvider;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Whoops\Provider\Silex\WhoopsServiceProvider;

if ($app['debug']) {
    $app->register(new WhoopsServiceProvider);
}

$app->register(new HttpCacheServiceProvider());

$app->register(new SessionServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new UrlGeneratorServiceProvider());

$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/',
            'form'    => array(
                'login_path'         => '/login',
                'username_parameter' => 'form[username]',
                'password_parameter' => 'form[password]',
            ),
            'logout'    => true,
            'anonymous' => true,
            'users'     => $app['security.users'],
        ),
    ),
));

$app['security.encoder.digest'] = $app->share(function ($app) {
    return new PlaintextPasswordEncoder();
});

$app->register(new TranslationServiceProvider());
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__.'/../resources/locales/fr.yml', 'fr');

    return $translator;
}));

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../resources/log/app.log',
    'monolog.name'    => 'app',
    'monolog.level'   => 300 // = Logger::WARNING
));

$app->register(new TwigServiceProvider(), array(
    'twig.options'        => array(
        'cache'            => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
        'strict_variables' => true
    ),
    'twig.form.templates' => array('form_div_layout.html.twig', 'common/form_div_layout.html.twig'),
    'twig.path'           => array(__DIR__ . '/../resources/views')
));

//did you run 'bin/propel main' and 'bin/propel insert-sql'? If not, please read README.md
$app->register(new PropelServiceProvider(), array(
    'propel.config_file' => __DIR__ . '/../resources/generated/propel-config/propel-conf.php',
    'propel.model_path' => __DIR__ . '/',
));

//if ($app['debug'] && isset($app['cache.path'])) {
//    $app->register(new ServiceControllerServiceProvider());
//    $app->register(new WebProfilerServiceProvider(), array(
//        'profiler.cache_dir' => $app['cache.path'].'/profiler',
//    ));
//    
//    $app->register(new PropelWebProfilerServiceProvider());
//}

if (isset($app['assetic.enabled']) && $app['assetic.enabled']) {
    $app->register(new AsseticServiceProvider(), array(
        'assetic.options' => array(
            'debug'            => $app['debug'],
            'auto_dump_assets' => $app['debug'],
        )
    ));
//    var_dump ( $app['assetic.path_to_yui_compressor']  ); die;
    $app['assetic.filter_manager'] = $app->share(
        $app->extend('assetic.filter_manager', function($fm, $app) {
            $fm->set('less', new LessFilter($app['assetic.path_to_node'], $app['assetic.node_paths']));
            $fm->set('yui_css', new CssCompressorFilter(
                $app['assetic.path_to_yui_compressor']
            ));
            $fm->set('yui_js', new JsCompressorFilter(
                $app['assetic.path_to_yui_compressor']
            ));
            return $fm;
        })
    );
        
   $app['assetic.asset_manager'] = $app->share(
        $app->extend('assetic.asset_manager', function($am, $app) {
            $jsFilters = $app['debug'] ? array() : [$app['assetic.filter_manager']->get('yui_js')];
            $cssFilters = [$app['assetic.filter_manager']->get('less')];
            if (!$app['debug']) $cssFilters[] = $app['assetic.filter_manager']->get('yui_css');

            // --- website
            $am->set('styles', new AssetCache(
                new GlobAsset(
                $app['assetic.input.path_to_css'], $cssFilters
                ), new FilesystemCache($app['assetic.path_to_cache'])
            ));
            $am->get('styles')->setTargetPath('css/styles.css');
            // --- javascript
            $scripts = $am->set('scripts', new AssetCache(
                new GlobAsset($app['assetic.input.path_to_js'], $jsFilters), new FilesystemCache($app['assetic.path_to_cache'])
            ));
            $am->get('scripts')->setTargetPath($app['assetic.output.path_to_js']);

            return $am;
        })
    );

}

return $app;
