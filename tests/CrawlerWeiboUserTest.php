<?php

use PHPUnit\Framework\TestCase;

require 'common.php';

/**
 * @covers CrawlerWeiboUser
 */
final class CrawlerWeiboUserTest extends TestCase
{
    public function testCanBeCreatedFromWeiboUser()
    {
        $this->assertInstanceOf(
            CrawlerWeiboUser::class,
            new CrawlerWeiboUser()
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage user ids required for weibo user
     */
    public function testIdsCannotBeNull()
    {
        $crawler = new CrawlerWeiboUser();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage user ids cannot be empty for weibo user
     */
    public function testIdsCannotBeEmpty()
    {
        $crawler = new CrawlerWeiboUser();
        $crawler->setConfig([
            'ids' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage numeric user ids expected for weibo user
     */
    public function testIdsMustBeNumeric()
    {
        $crawler = new CrawlerWeiboUser();
        $crawler->setConfig([
            'ids' => ['qiyiguanbo'],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for weibo user
     */
    public function testPageCannotBeZero()
    {
        $crawler = new CrawlerWeiboUser();
        $crawler->setConfig([
            'ids' => ['1731986465'],
            'page' => 0,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for weibo user
     */
    public function testPageCannotBeNegative()
    {
        $crawler = new CrawlerWeiboUser();
        $crawler->setConfig([
            'ids' => ['1731986465'],
            'page' => -1,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }


    public function testCanGetEnoughMessageWithPageConfig()
    {
        $crawler = new CrawlerWeiboUser();
        $crawler->setConfig([
            'ids' => ['1731986465'],
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
        $crawler = new CrawlerWeiboUser();
        $crawler->setConfig([
            'ids' => ['1731986465'],
            'page' => 1,
            'image_check' => 9
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(10,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithVideoConfig()
    {
        $crawler = new CrawlerWeiboUser();
        $crawler->setConfig([
            'ids' => ['1731986465'],
            'page' => 1,
            'video_check' => 1
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(10,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithKeywordcheckConfig()
    {
        $crawler = new CrawlerWeiboUser();
        $crawler->setConfig([
            'ids' => ['1731986465'],
            'page' => 1,
            'keyword_check' => ['#'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(10,count($crawler->getMessage()));
    }

}
