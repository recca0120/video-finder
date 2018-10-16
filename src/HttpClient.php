<?php

namespace Recca0120\VideoFinder;

use Exception;
use Http\Client\HttpAsyncClient;
use Http\Message\MessageFactory;
use Illuminate\Filesystem\Filesystem;

class HttpClient
{
    private $files;

    private $client;

    private $messageFactory;

    private $headers = [
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        // 'accept-encoding' => 'gzip, deflate, br',
        'accept-language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
        'cache-control' => 'no-cache',
        'pragma' => 'no-cache',
        'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
    ];

    public function __construct(Filesystem $files, HttpAsyncClient $client, MessageFactory $messageFactory)
    {
        $this->files = $files;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    public function get($uri, array $headers = [], $body = null)
    {
        return $this->request('GET', $uri, $headers, $body);
    }

    public function post($uri, array $headers = [], $body = null)
    {
        return $this->request('POST', $uri, $headers, $body);
    }

    public function download($source, $saveTo)
    {
        $response = $this->get($source);
        $contents = $response->getBody()->getContents();

        while (true) {
            try {
                $this->files->put($saveTo, $contents);
                break;
            } catch (Exception $e) {
                var_dump($e->getMessage());
                sleep(1);
            }
        }

        return $saveTo;
    }

    public function request($method, $uri, array $headers = [], $body = null)
    {
        return $this->client->sendRequest(
            $this->messageFactory->createRequest($method, $uri, array_merge($this->headers, $headers), $body)
        );
    }
}
