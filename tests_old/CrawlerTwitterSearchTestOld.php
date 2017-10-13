<?php

// use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . "/../class.twitter.search.php");

/**
 * @covers CrawlerTwitterSearch
 * 国外服务器只有php5.4,所以使用了PHPUnit4.8编写了测试用例
 */
class CrawlerTwitterSearchTestOld extends PHPUnit_Framework_TestCase
{
    public function testCanBeCreatedFromTwitterSearch()
    {
        $this->assertInstanceOf(
            'CrawlerTwitterSearch',
            new CrawlerTwitterSearch()
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage keywords required for twitter search
     */
    public function testKeywordsCannotBeNull()
    {
        $crawler = new CrawlerTwitterSearch();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage keywords cannot be empty for twitter search
     */
    public function testKeywordsCannotBeEmpty()
    {
        $crawler = new CrawlerTwitterSearch();
        $crawler->setConfig([
            'keywords' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for twitter search
     */
    public function testPageCannotBeZero()
    {
        $crawler = new CrawlerTwitterSearch();
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
     * @expectedExceptionMessage invalid page setting for twitter search
     */
    public function testPageCannotBeNegative()
    {
        $crawler = new CrawlerTwitterSearch();
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
        $crawler = new CrawlerTwitterSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 1,
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertEquals(20,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('item_id',$value);
            $this->assertArrayHasKey('link',$value);
            $this->assertArrayHasKey('text',$value);
            $this->assertArrayHasKey('author',$value);
            $this->assertArrayHasKey('created_at_time',$value);
        }
    }

    public function testCannotGetEnoughMessageWithImageConfig()
    {
        $crawler = new CrawlerTwitterSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 1,
            'image_check' => 9
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(20,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithVideoConfig()
    {
        $crawler = new CrawlerTwitterSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 1,
            'video_check' => 1
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(20,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithKeywordcheckConfig()
    {
        $crawler = new CrawlerTwitterSearch();
        $crawler->setConfig([
            'keywords' => ['DOTA2'],
            'page' => 1,
            'keyword_check' => ['DOTA'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(20,count($crawler->getMessage()));
    }

}
