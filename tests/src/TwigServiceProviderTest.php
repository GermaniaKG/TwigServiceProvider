<?php
namespace tests;

use Germania\TwigServiceProvider\TwigServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Twig\Loader\ChainLoader;
use Twig\Environment as TwigEnvironment;

class TwigServiceProviderTest extends \PHPUnit\Framework\TestCase
{
    public $template_dir;

    public function setUp() : void
    {
        $this->template_dir = join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'templates']);
    }


    public function testServiceProvider( ) : void
    {
        $sut = new TwigServiceProvider;
        $this->assertInstanceOf( ServiceProviderInterface::class, $sut);
    }


    /**
     * @dataProvider provideKeysAndTypes
     */
    public function testDataTypes( $key, $expected_type) : void
    {
        $sut = new TwigServiceProvider([
            'templates' => $this->template_dir
        ]);

        $dic = new Container;
        $dic->register( $sut);

        $result = $dic[ $key ];

        switch($expected_type):
            case "bool":
                $this->assertIsBool( $result );
                break;
            case "array":
                $this->assertIsArray( $result );
                break;
            case "iterable":
                $this->assertIsIterable( $result );
                break;
            case "string":
                $this->assertIsString( $result );
                break;
            case "resource":
                $this->assertIsResource( $result );
                break;
            case "callable":
                $this->assertIsCallable( $result );
                break;

            case "instance":
                if (class_exists($service)
                or interface_exists($service)):
                    $this->assertInstanceOf( $service, $result);
                    break;
                endif;

                $msg = sprintf("Expected type '%s' not supported in this test method", $service);
                throw new \UnexpectedValueException( $msg );

            default:
                if (class_exists($expected_type)
                or interface_exists($expected_type)):
                    $this->assertInstanceOf( $expected_type, $result);
                    break;
                endif;

                $msg = sprintf("Expected type '%s' not supported in this test method", $expected_type);
                throw new \UnexpectedValueException( $msg );
        endswitch;
    }



    /**
     * @dataProvider provideTwigClasses
     */
    public function testTwigLoaderChain( $key, $twig_class) : void
    {
        $sut = new TwigServiceProvider([
            'templates' => $this->template_dir
        ]);

        $dic = new Container;
        $dic->register( $sut);

        $result = $dic[ $key ];
        $this->assertInstanceOf( $twig_class, $result );
    }


    public function provideTwigClasses() : array
    {
        return array(
            [ 'Twig.LoaderChain',   ChainLoader::class ],
            [ 'Twig',               TwigEnvironment::class ]
        );
    }
    public function provideKeysAndTypes() : array
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

