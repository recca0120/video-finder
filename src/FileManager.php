<?php

namespace Recca0120\VideoFinder;

use Illuminate\Filesystem\Filesystem;

class FileManager
{
    private $videoFinder;

    private $files;

    private $client;

    public function __construct(VideoFinder $videoFinder, Filesystem $files, HttpClient $client)
    {
        $this->videoFinder = $videoFinder;
        $this->client = $client;
        $this->files = $files;
    }

    public function move(File $source, $target)
    {
        $number = $source->number();
        $video = $this->videoFinder->find($number);
        $videoDirectory = $this->makeDirectory($target.'/'.$number);
        $video = $this->download($video, $videoDirectory);
        $filename = $videoDirectory.'/'.$source->name();

        $this->files->put(
            $videoDirectory.'/data.json',
            json_encode($video->toArray())
        );

        $this->files->move($source->getPath(), $filename);

        return $filename;
    }

    private function download(Video $video, $directory)
    {
        $extension = strtolower($this->files->extension($video->cover));
        $saveTo = $directory.'/'.$video->number.'.'.$extension;
        $video->cover = $this->files->basename($this->client->download($video->cover, $saveTo));

        $directory = $this->makeDirectory($directory.'/screenshots');
        $video->screenshots = array_map(function ($screenshot) use ($directory) {
            $extension = strtolower($this->files->extension($screenshot));
            $saveTo = $directory.'/'.$this->files->name($screenshot).'.'.$extension;

            return $this->files->basename($this->client->download($screenshot, $saveTo));
        }, $video->screenshots);

        return $video;
    }

    private function makeDirectory($directory)
    {
        if ($this->files->isDirectory($directory) === false) {
            $this->files->makeDirectory($directory, 0777, true);
        }

        return $directory;
    }
}
