<?php

use PHPUnit\Framework\TestCase;

require 'common.php';

/**
 * @covers CrawlerWeiboTopic
 */
final class CrawlerWeiboTopicTest extends TestCase
{
    public function testCanBeCreatedFromWeiboTopic()
    {
        $this->assertInstanceOf(
            CrawlerWeiboTopic::class,
            new CrawlerWeiboTopic()
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage topic ids required for weibo topic
     */
    public function testIdsCannotBeNull()
    {
        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage topic ids cannot be empty for weibo topic
     */
    public function testIdsCannotBeEmpty()
    {
        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
            'ids' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for weibo topic
     */
    public function testPageCannotBeZero()
    {
        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
            'ids' => ['10080819f7ff3e4d2c90e22c54592c2e6dd950'],
            'page' => 0,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for weibo topic
     */
    public function testPageCannotBeNegative()
    {
        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
            'ids' => ['10080819f7ff3e4d2c90e22c54592c2e6dd950'],
            'page' => -1,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testCanGetEnoughMessageWithPageConfig()
    {
        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
            'ids' => ['10080819f7ff3e4d2c90e22c54592c2e6dd950'],
            'page' => 2,
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
        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
            'ids' => ['10080819f7ff3e4d2c90e22c54592c2e6dd950'],
            'page' => 1,
            'image_check' => 9
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(10,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithVideoConfig()
    {
        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
            'ids' => ['10080819f7ff3e4d2c90e22c54592c2e6dd950'],
            'page' => 1,
            'video_check' => 1
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(10,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithKeywordcheckConfig()
    {
        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
            'ids' => ['10080819f7ff3e4d2c90e22c54592c2e6dd950'],
            'page' => 1,
            'keyword_check' => ['#'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(10,count($crawler->getMessage()));
    }

}
