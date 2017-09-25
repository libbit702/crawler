<?php

use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . "/../class.bilibili.search.php");

/**
 * @covers CrawlerBilibiliSearch
 */
final class CrawlerBilibiliSearchTest extends TestCase
{
    public function testCanBeCreatedFromBilibiliSearch()
    {
        $this->assertInstanceOf(
            CrawlerBilibiliSearch::class,
            new CrawlerBilibiliSearch()
        );
    }

    public function testKeywordsCannotBeNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("keywords required for bilibili search");

        $crawler = new CrawlerBilibiliSearch();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testKeywordsCannotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("keywords cannot be empty for bilibili search");

        $crawler = new CrawlerBilibiliSearch();
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
        $this->expectExceptionMessage("invalid page setting for bilibili search");

        $crawler = new CrawlerBilibiliSearch();
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
        $this->expectExceptionMessage("invalid page setting for bilibili search");

        $crawler = new CrawlerBilibiliSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => -1,
            'keyword_check' => ['DOTA'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testCanGetEnoughMessageWithPageConfig()
    {
        $crawler = new CrawlerBilibiliSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'keyword_check' => [],
            'page' => 2,
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertGreaterThanOrEqual(40,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('url_author',$value);
            $this->assertArrayHasKey('author_link',$value);
            $this->assertArrayHasKey('pics',$value);
            $this->assertArrayHasKey('playtime',$value);
            $this->assertArrayHasKey('link',$value);
            $this->assertArrayHasKey('title',$value);
        }
    }

    public function testCannotGetEnoughMessageWithKeywordcheckConfig()
    {
        $crawler = new CrawlerBilibiliSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 2,
            'keyword_check' => ['DOTA'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(40,count($crawler->getMessage()));
    }

}
