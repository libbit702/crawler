<?php

use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . "/../class.bilibili.collection.php");

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

    public function testIdsCannotBeNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("collection ids required for bilibili collection");

        $crawler = new CrawlerBilibiliCollection();
        $crawler->setConfig([
         'ids' => null
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testIdsCannotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("collection ids cannot be empty for bilibili collection");

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
