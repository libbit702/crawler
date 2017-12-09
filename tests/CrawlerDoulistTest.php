<?php

use PHPUnit\Framework\TestCase;

require 'common.php';

/**
 * @covers CrawlerDoulist
 */
final class CrawlerDoulistTest extends TestCase
{
    public function testCanBeCreatedFromDoulist()
    {
        $this->assertInstanceOf(
            CrawlerDoulist::class,
            new CrawlerDoulist()
        );
    }

    public function testIdsCannotBeNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("doulist ids required for douban list");

        $crawler = new CrawlerDoulist();
        $crawler->setConfig([
         'ids' => null
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testIdsCannotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("doulist ids cannot be empty for douban list");

        $crawler = new CrawlerDoulist();
        $crawler->setConfig([
            'ids' => []
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testPageCannotBeZero()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid page setting for douban list");

        $crawler = new CrawlerDoulist();
        $crawler->setConfig([
            'ids' => ['2943106'],
            'page' => 0,
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testPageCannotBeNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid page setting for douban list");

        $crawler = new CrawlerDoulist();
        $crawler->setConfig([
            'ids' => ['2943106'],
            'page' => -1,
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testCanGetEnoughMessage()
    {
        $crawler = new CrawlerDoulist();
        $crawler->setConfig([
            'ids' => ['2943106'],
            'page' => 3,
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertGreaterThanOrEqual(75,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('meta',$value);
            $this->assertArrayHasKey('link',$value);
            $this->assertArrayHasKey('title',$value);
            $this->assertArrayHasKey('rank',$value);
        }
    }
}
