<?php

use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . "/../class.weibo.topic.php");

/**
 * @covers CrawlerWeiboUser
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

    public function testIdsCannotBeNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("topic ids required for weibo topic");

        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testIdsCannotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("topic ids cannot be empty for weibo topic");

        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
            'ids' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testPageCannotBeZero()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid page setting for weibo topic");

        $crawler = new CrawlerWeiboTopic();
        $crawler->setConfig([
            'ids' => ['10080819f7ff3e4d2c90e22c54592c2e6dd950'],
            'page' => 0,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testPageCannotBeNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid page setting for weibo topic");

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
