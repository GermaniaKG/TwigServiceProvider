<?php
namespace Germania\TwigServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Twig\Loader\FilesystemLoader;
use Twig\Loader\ChainLoader;
use Twig\Environment as TwigEnvironment;



class TwigServiceProvider implements ServiceProviderInterface
{

    public $config = array(
        // For Twig Filesystem Loader
        // See https://twig.symfony.com/doc/2.x/api.html#twig-loader-filesystem
        'templates' => 'templates',

        // Twig Options
        // See https://twig.symfony.com/doc/2.x/api.html#environment-options
        'debug' => false,
        'cache' => 'cache',
        'auto_reload' => true,
        'autoescape'  => false,
        'strict_variables' => false
    );


    /**
     * @param array $config Configuration array.
     */
    public function __construct( array $config = array())
    {
        $this->config = array_merge($this->config, $config);
    }


    /**
     * @implements ServiceProviderInterface
     */
    public function register(Container $dic)
    {

        /**
         * @return array
         */
        $dic['Twig.Config'] = function( $dic ) {
            return $this->config;
        };


        /**
         * @return string
         */
        $dic['Twig.CachePath'] = function( $dic ) {
            $template_config = $dic['Twig.Config'];
            return $template_config['cache'];
        };


        /**
         * @return array
         */
        $dic['Twig.TemplatePaths'] = function( $dic ) {
            $template_config = $dic['Twig.Config'];
            $templates_paths = $template_config['templates'];

            if (is_string($templates_paths)) {
                $templates_paths = array( $templates_paths );
            }

            return $templates_paths;
        };


        /**
         * @return array
         */
        $dic['Twig.Loaders'] = function( $dic ) {
            $templates_paths = $dic['Twig.TemplatePaths'];

            return [
                new FilesystemLoader( $templates_paths )
            ];
        };




        /**
         * @return array
         */
        $dic['Twig.Options'] = function( $dic ) {
            $template_config = $dic['Twig.Config'];
            $cache_path      = $dic['Twig.CachePath'];

            return [
                'cache'            => $cache_path,
                'auto_reload'      => $template_config['auto_reload'],
                'autoescape'       => $template_config['autoescape'],
                'debug'            => $template_config['debug'],
                'strict_variables' => $template_config['strict_variables']
            ];
        };



        /**
         * @return array
         */
        $dic['Twig.Globals'] = function( $dic ) {
            return array();
        };


        /**
         * @return array
         */
        $dic['Twig.Filters'] = function( $dic ) {
            return array();
        };


        /**
         * @return array
         */
        $dic['Twig.Tests'] = function( $dic ) {
            return array();
        };

        /**
         * @return array
         */
        $dic['Twig.Functions'] = function( $dic ) {
            return array();
        };

        /**
         * @return array
         */
        $dic['Twig.Extensions'] = function($dic) {
            return array();
        };






        /**
         * @return ChainLoader
         */
        $dic['Twig.LoaderChain'] = function($dic) {
            $loaders = $dic['Twig.Loaders'];
            return new ChainLoader( $loaders );
        };


        /**
         * @return \Twig\Environment
         */
        $dic['Twig'] = function( $dic ) {
            return $dic[TwigEnvironment::class];
        };


        /**
         * @return \Twig\Environment
         */
        $dic[TwigEnvironment::class] = function( $dic ) {

            // ---- 1. Instantiate Twig -----
            $twig_loader_chain = $dic['Twig.LoaderChain'];
            $twig_options      = $dic['Twig.Options'];
            $twig = new TwigEnvironment($twig_loader_chain, $twig_options);


            // ---- 2. Configure Extras -----

            // Add Twig_Extensions
            $extensions = $dic['Twig.Extensions'];
            foreach( $extensions as $ext ):
                $twig->addExtension( $ext );
            endforeach;


            // Add Template Globals for Twig
            $globals = $dic['Twig.Globals'];
            foreach( $globals as $name => $value ):
                $twig->addGlobal( $name, $value );
            endforeach;


            // Add Twig_Filters
            $filters = $dic['Twig.Filters'];
            foreach( $filters as $filter ):
                $twig->addFilter( $filter );
            endforeach;


            // Add Tests
            $tests = $dic['Twig.Tests'];
            foreach( $tests as $test ):
                $twig->addTest( $test );
            endforeach;


            // Add Functions
            $functions = $dic['Twig.Functions'];
            foreach( $functions as $fn ):
                $twig->addFunction( $fn );
            endforeach;


            // ---- 3. Return Twig_Environment
            return $twig;
        };

    }
}

