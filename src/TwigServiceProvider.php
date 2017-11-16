<?php
namespace Germania\TwigServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use \Twig_Loader_Filesystem;
use \Twig_Loader_Array;
use \Twig_Loader_Chain;
use \Twig_Environment;
use \Twig_Extension_Debug;



class TwigServiceProvider implements ServiceProviderInterface
{

    public $config = array(
        // For Twig Loader
        'templates' => 'templates',

        // Twig Options
        'cache' => 'cache',
        'auto_reload' => true,
        'autoescape'  => false,
        'debug' => false
    );


    /**
     * @param array $config Configuration
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
                new Twig_Loader_Filesystem( $templates_paths ),
                new Twig_Loader_Array( [] )
            ];
        };




        /**
         * @return array
         */
        $dic['Twig.Options'] = function( $dic ) {
            $template_config = $dic['Twig.Config'];
            $cache_path      = $dic['Twig.CachePath'];

            return [
                'cache'       => $cache_path,
                'auto_reload' => $template_config['auto_reload'],
                'autoescape'  => $template_config['autoescape'],
                'debug'       => $template_config['debug']
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
            return [
                new Twig_Extension_Debug
            ];
        };






        /**
         * @return Twig_Loader_Chain
         */
        $dic['Twig.LoaderChain'] = function($dic) {
            $loaders = $dic['Twig.Loaders'];
            return new Twig_Loader_Chain( $loaders );
        };


        /**
         * @return Twig_Environment
         */
        $dic['Twig'] = function( $dic ) {

            // ---- 1. Instantiate Twig -----
            $twig_loader_chain = $dic['Twig.LoaderChain'];
            $twig_options      = $dic['Twig.Options'];
            $twig = new Twig_Environment($twig_loader_chain, $twig_options);


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

