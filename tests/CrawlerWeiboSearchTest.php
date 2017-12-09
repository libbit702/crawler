<?php

use PHPUnit\Framework\TestCase;

require 'common.php';

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

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage keywords required for weibo search
     */
    public function testKeywordsCannotBeNull()
    {
        $crawler = new CrawlerWeiboSearch();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage keywords cannot be empty for weibo search
     */
    public function testKeywordsCannotBeEmpty()
    {
        $crawler = new CrawlerWeiboSearch();
        $crawler->setConfig([
            'keywords' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for weibo search
     */
    public function testPageCannotBeZero()
    {
        $crawler = new CrawlerWeiboSearch();
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
     * @expectedExceptionMessage invalid page setting for weibo search
     */
    public function testPageCannotBeNegative()
    {
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
            'keywords' => ['视频'],
            'page' => 2,
            'debug' => 1,
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertGreaterThanOrEqual(20,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('text',$value);
            $this->assertArrayHasKey('user',$value);
            $this->assertArrayHasKey('created_at',$value);
            $this->assertArrayHasKey('mid',$value);
            $this->assertArrayHasKey('bid',$value);
            $this->assertArrayHasKey('source',$value);
        }
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
