<?php

namespace Recca0120\VideoFinder\Tests;

use stdClass;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\VideoFinder\Video;
use Recca0120\VideoFinder\HttpClient;
use Recca0120\VideoFinder\VideoFinder;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class VideoFinderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function test_find()
    {
        $number = 'EBOD-320';

        $response = m::mock(stdClass::class);
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $response->shouldReceive('getBody->getContents')->andReturn(
            file_get_contents(__DIR__.'/fixtures/javbus-'.$number.'.html')
        );

        $client = m::mock(HttpClient::class);
        $client->shouldReceive('get')->andReturn($response);

        $finder = new VideoFinder($client);

        $this->assertEquals(
            new Video(json_decode(file_get_contents(__DIR__.'/fixtures/video.json'), true)),
            $finder->find($number)
        );
    }
}
