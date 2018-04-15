<?php

use PHPUnit\Framework\TestCase;

require 'common.php';

/**
 * @covers CrawlerInstagramUser
 */
final class CrawlerInstagramUserTest extends TestCase
{
    public function testCanBeCreatedFromInstagramUser()
    {
        $this->assertInstanceOf(
            CrawlerInstagramUser::Class,
            new CrawlerInstagramUser()
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage user ids required for instagram user
     */
    public function testKeywordsCannotBeNull()
    {
        $crawler = new CrawlerInstagramUser();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage user ids cannot be empty for instagram user
     */
    public function testKeywordsCannotBeEmpty()
    {
        $crawler = new CrawlerInstagramUser();
        $crawler->setConfig([
            'ids' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for instagram user
     */
    public function testPageCannotBeZero()
    {
        $crawler = new CrawlerInstagramUser();
        $crawler->setConfig([
            'ids' => ['redvelvet.smtown'],
            'page' => 0,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for instagram user
     */
    public function testPageCannotBeNegative()
    {
        $crawler = new CrawlerInstagramUser();
        $crawler->setConfig([
            'ids' => ['redvelvet.smtown'],
            'page' => -1,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testCanGetEnoughMessageWithPageConfig()
    {
        $crawler = new CrawlerInstagramUser();
        $crawler->setConfig([
            'ids' => ['redvelvet.smtown'],
            'page' => 2,
            // 'debug' => 1
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertEquals(24,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('link',$value);
            $this->assertArrayHasKey('owner',$value);
            $this->assertArrayHasKey('is_video',$value);
        }
    }

    public function testCannotGetEnoughMessageWithImageConfig()
    {
        $crawler = new CrawlerInstagramUser();
        $crawler->setConfig([
            'ids' => ['redvelvet.smtown'],
            'page' => 1,
            'image_check' => 9
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(12,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithVideoConfig()
    {
        $crawler = new CrawlerInstagramUser();
        $crawler->setConfig([
            'ids' => ['redvelvet.smtown'],
            'page' => 1,
            'video_check' => 1
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(12,count($crawler->getMessage()));
    }

    public function testCannotGetEnoughMessageWithKeywordcheckConfig()
    {
        $crawler = new CrawlerInstagramUser();
        $crawler->setConfig([
            'ids' => ['redvelvet.smtown'],
            'page' => 1,
            'keyword_check' => ['DOTA'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(12,count($crawler->getMessage()));
    }

}
