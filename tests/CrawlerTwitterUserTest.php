<?php

use PHPUnit\Framework\TestCase;

require 'common.php';

/**
 * @covers CrawlerTwitterUser
 */
final class CrawlerTwitterUserTest extends TestCase
{
    public function testCanBeCreatedFromTwitterUser()
    {
        $this->assertInstanceOf(
            CrawlerTwitterUser::Class,
            new CrawlerTwitterUser()
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage user ids required for twitter user
     */
    public function testKeywordsCannotBeNull()
    {
        $crawler = new CrawlerTwitterUser();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage user ids cannot be empty for twitter user
     */
    public function testKeywordsCannotBeEmpty()
    {
        $crawler = new CrawlerTwitterUser();
        $crawler->setConfig([
            'ids' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for twitter user
     */
    public function testPageCannotBeZero()
    {
        $crawler = new CrawlerTwitterUser();
        $crawler->setConfig([
            'ids' => ['KARD_Official'],
            'page' => 0,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for twitter user
     */
    public function testPageCannotBeNegative()
    {
        $crawler = new CrawlerTwitterUser();
        $crawler->setConfig([
            'ids' => ['KARD_Official'],
            'page' => -1,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testCanGetEnoughMessageWithPageConfig()
    {
        $crawler = new CrawlerTwitterUser();
        $crawler->setConfig([
            'ids' => ['KARD_Official'],
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
        $crawler = new CrawlerTwitterUser();
        $crawler->setConfig([
            'ids' => ['KARD_Official'],
            'page' => 1,
            'image_check' => 9
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(20,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithVideoConfig()
    {
        $crawler = new CrawlerTwitterUser();
        $crawler->setConfig([
            'ids' => ['5REDVELVET'],
            'page' => 1,
            'video_check' => 1
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(20,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithKeywordcheckConfig()
    {
        $crawler = new CrawlerTwitterUser();
        $crawler->setConfig([
            'ids' => ['KARD_Official'],
            'page' => 1,
            'keyword_check' => ['DOTA'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(20,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithPublicTimecheckConfig()
    {
        $crawler = new CrawlerTwitterUser();
        $crawler->setConfig([
            'ids' => ['KARD_Official'],
            'page' => 1,
            'public_time_check' => date('Y-m-d', time() - 86400),
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(20,count($crawler->getMessage()));
    }

}
