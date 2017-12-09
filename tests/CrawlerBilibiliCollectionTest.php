<?php

use PHPUnit\Framework\TestCase;

require 'common.php';

/**
 * @covers CrawlerBilibiliCollection
 */
final class CrawlerBilibiliCollectionTest extends TestCase
{
    public function testCanBeCreatedFromBilibiliCollection()
    {
        $this->assertInstanceOf(
            CrawlerBilibiliCollection::class,
            new CrawlerBilibiliCollection()
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage collection ids required for bilibili collection
     */
    public function testIdsCannotBeNull()
    {
        $crawler = new CrawlerBilibiliCollection();
        $crawler->setConfig([
         'ids' => null
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage collection ids cannot be empty for bilibili collection
     */
    public function testIdsCannotBeEmpty()
    {
        $crawler = new CrawlerBilibiliCollection();
        $crawler->setConfig([
            'ids' => []
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testCanGetEnoughMessage()
    {
        $crawler = new CrawlerBilibiliCollection();
        $crawler->setConfig([
            'ids' => ['av12410350'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertGreaterThanOrEqual(15,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('url_author',$value);
            $this->assertArrayHasKey('link',$value);
            $this->assertArrayHasKey('title',$value);
            $this->assertArrayHasKey('pics',$value);
        }
    }
}
