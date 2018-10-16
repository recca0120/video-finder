<?php

namespace Recca0120\VideoFinder;

use Symfony\Component\DomCrawler\Crawler;

class VideoFinder
{
    private $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function find($number)
    {
        $response = $this->client->get(sprintf('https://www.javbus.in/%s', $number), [
            ':authority' => 'www.javbus.in',
            ':method' => 'GET',
            ':path' => '/'.$number,
            ':scheme' => 'https',
            'save-data' => 'on',
            'upgrade-insecure-requests' => '1',
        ]);

        if ($response->getStatusCode() !== 200) {
            if (strpos($number, '-') === false) {
                throw new NotFoundException(sprintf('[%s] video not found', $number));
            }

            return $this->find(str_replace('-', '', $number));
        }

        return new Video(
            $this->parseAttributes(new Crawler(
                $response->getBody()->getContents()
            ))
        );
    }

    private function parseAttributes(Crawler $crawler)
    {
        $map = [
            'number' => '識別碼',
            'publish_at' => '發行日期',
            'length' => '長度',
            'director' => '導演',
            'producer' => '製作商',
            'publisher' => '發行商',
            'series' => '系列',
            // 'categories' => '類別',
            // 'actors' => '演員',
        ];

        $attributes = [];
        foreach ($crawler->filter('.movie .info p') as $dom) {
            $text = trim($dom->nodeValue);
            foreach ($map as $key => $header) {
                if (strpos($text, $header) !== false) {
                    $attributes[$key] = trim(str_replace($header.': ', '', $text));

                    break;
                }
            }
        }

        return array_merge(
            $attributes,
            $this->parseCategoriesAndActors($crawler),
            [
                'title' => $this->parseTitle($crawler),
                'cover' => $this->parseCover($crawler),
                'screenshots' => $this->parseScreenShots($crawler),
            ]
        );
    }

    private function parseCategoriesAndActors(Crawler $crawler)
    {
        $nodes = $crawler->filter('.movie .info p .genre');

        $attributes = [
            'categories' => [],
            'actors' => [],
        ];
        foreach ($nodes as $i => $dom) {
            if ($dom->getAttribute('onmouseover') !== '') {
                $attributes['actors'][] = trim($dom->nodeValue);
            } else {
                $attributes['categories'][] = trim($dom->nodeValue);
            }
        }

        return $attributes;
    }

    private function parseTitle(Crawler $crawler)
    {
        return $crawler->filter('h3')->first()->text();
    }

    private function parseCover(Crawler $crawler)
    {
        return $crawler->filter('.movie .screencap img')->first()->attr('src');
    }

    private function parseScreenShots(Crawler $crawler)
    {
        $screenshots = [];

        $nodes = $crawler->filter('#sample-waterfall .photo-frame img');

        foreach ($nodes as $dom) {
            $screenshots[] = $dom->getAttribute('src');
        }

        return $screenshots;
    }
}
