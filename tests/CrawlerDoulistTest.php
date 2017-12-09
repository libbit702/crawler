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

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage doulist ids required for douban list
     */
    public function testIdsCannotBeNull()
    {
        $crawler = new CrawlerDoulist();
        $crawler->setConfig([
         'ids' => null
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage doulist ids cannot be empty for douban list
     */
    public function testIdsCannotBeEmpty()
    {
        $crawler = new CrawlerDoulist();
        $crawler->setConfig([
            'ids' => []
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for douban list
     */
    public function testPageCannotBeZero()
    {
        $crawler = new CrawlerDoulist();
        $crawler->setConfig([
            'ids' => ['2943106'],
            'page' => 0,
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for douban list
     */
    public function testPageCannotBeNegative()
    {
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
