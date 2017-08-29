<?php

use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . "/../class.weibo.search.php");

/**
 * @covers CrawlerWeiboSearch
 */
final class CrawlerWeiboSearchTest extends TestCase
{
    public function testCanBeCreatedFromWeiboSearch()
    {
        $this->assertInstanceOf(
            CrawlerWeiboSearch::class,
            new CrawlerWeiboSearch()
        );
    }

    public function testKeywordsCannotBeNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("keywords required for weibo search");

        $crawler = new CrawlerWeiboSearch();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testKeywordsCannotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("keywords cannot be empty for weibo search");

        $crawler = new CrawlerWeiboSearch();
        $crawler->setConfig([
            'keywords' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testPageCannotBeZero()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid page setting for weibo search");

        $crawler = new CrawlerWeiboSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 0,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testPageCannotBeNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid page setting for weibo search");

        $crawler = new CrawlerWeiboSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => -1,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testCanGetEnoughMessageWithPageConfig()
    {
        $crawler = new CrawlerWeiboSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 2,
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertGreaterThanOrEqual(20,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithImageConfig()
    {
        $crawler = new CrawlerWeiboSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 1,
            'image_check' => 9
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(10,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithVideoConfig()
    {
        $crawler = new CrawlerWeiboSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 1,
            'video_check' => 1
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(10,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithKeywordcheckConfig()
    {
        $crawler = new CrawlerWeiboSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 1,
            'keyword_check' => ['DOTA'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(10,count($crawler->getMessage()));
    }

}
