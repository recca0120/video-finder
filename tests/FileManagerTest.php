<?php

namespace Recca0120\VideoFinder\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\VideoFinder\File;
use Recca0120\VideoFinder\Video;
use Illuminate\Filesystem\Filesystem;
use Recca0120\VideoFinder\HttpClient;
use Recca0120\VideoFinder\FileManager;
use Recca0120\VideoFinder\VideoFinder;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class FileManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function test_move_file()
    {
        $video = new Video(json_decode(file_get_contents(__DIR__.'/fixtures/video.json'), true));

        $videoFinder = m::mock(VideoFinder::class);
        $videoFinder->shouldReceive('find')->andReturn($video);

        $files = m::mock(Filesystem::class);
        $files->shouldReceive('isDirectory')->andReturn(false);
        $files->shouldReceive('makeDirectory');
        $files->shouldReceive('extension')->andReturnUsing(function ($filename) {
            return pathinfo($filename, PATHINFO_EXTENSION);
        });
        $files->shouldReceive('basename')->andReturnUsing(function ($filename) {
            return pathinfo($filename, PATHINFO_BASENAME);
        });
        $files->shouldReceive('name')->andReturnUsing(function ($filename) {
            return pathinfo($filename, PATHINFO_FILENAME);
        });
        $files->shouldReceive('put');
        $files->shouldReceive('move');

        $client = m::mock(HttpClient::class);
        $client->shouldReceive('download');

        $file = new File('SNIS-027 - 宇都宮しをん、イキます。 (ブルーレイ).mp4');

        $fileManager = new FileManager($videoFinder, $files, $client);

        $fileManager->move($file, __DIR__);
    }
}
