<?php
namespace tests;

use Germania\TwigServiceProvider\TwigServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use \Twig_Loader_Chain;
use \Twig_Environment;

class TwigServiceProviderTest extends \PHPUnit\Framework\TestCase
{
    public $template_dir;

    public function setUp()
    {
        $this->template_dir = join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'templates']);
    }


    public function testServiceProvider( )
    {
        $sut = new TwigServiceProvider;
        $this->assertInstanceOf( ServiceProviderInterface::class, $sut);
    }


    /**
     * @dataProvider provideKeysAndTypes
     */
    public function testDataTypes( $key, $result_type)
    {
        $sut = new TwigServiceProvider([
            'templates' => $this->template_dir
        ]);

        $dic = new Container;
        $dic->register( $sut);

        $result = $dic[ $key ];
        $this->assertInternalType( $result_type, $result);
    }


    /**
     * @dataProvider provideTwigClasses
     */
    public function testTwigLoaderChain( $key, $twig_class)
    {
        $sut = new TwigServiceProvider([
            'templates' => $this->template_dir
        ]);

        $dic = new Container;
        $dic->register( $sut);

        $result = $dic[ $key ];
        $this->assertInstanceOf( $twig_class, $result );
    }


    public function provideTwigClasses()
    {
        return array(
            [ 'Twig.LoaderChain',   Twig_Loader_Chain::class ],
            [ 'Twig',               Twig_Environment::class ]
        );
    }
    public function provideKeysAndTypes()
    {
        return array(
            [ 'Twig.Config',        'array' ],
            [ 'Twig.TemplatePaths', 'array' ],
            [ 'Twig.Loaders',       'array' ],
            [ 'Twig.Options',       'array' ],
            [ 'Twig.Globals',       'array' ],
            [ 'Twig.Filters',       'array' ],
            [ 'Twig.Tests',         'array' ],
            [ 'Twig.Functions',     'array' ],
            [ 'Twig.Extensions',    'array' ],
            [ 'Twig.CachePath',     'string' ]
        );
    }


}

