<?php

use PHPUnit\Framework\TestCase;

require 'common.php';

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

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage keywords required for bilibili search
     */
    public function testKeywordsCannotBeNull()
    {
        $crawler = new CrawlerBilibiliSearch();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage keywords cannot be empty for bilibili search
     */
    public function testKeywordsCannotBeEmpty()
    {
        $crawler = new CrawlerBilibiliSearch();
        $crawler->setConfig([
            'keywords' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for bilibili search
     */
    public function testPageCannotBeZero()
    {
        $crawler = new CrawlerBilibiliSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 0,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for bilibili search
     */
    public function testPageCannotBeNegative()
    {
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
