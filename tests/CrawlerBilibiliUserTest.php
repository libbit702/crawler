<?php

use PHPUnit\Framework\TestCase;

require 'common.php';

/**
 * @covers CrawlerBilibiliUser
 */
final class CrawlerBilibiliUserTest extends TestCase
{
    public function testCanBeCreatedFromBilibiliUser()
    {
        $this->assertInstanceOf(
            CrawlerBilibiliUser::class,
            new CrawlerBilibiliUser()
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage user ids required for bilibili user
     */
    public function testIdsCannotBeNull()
    {
        $crawler = new CrawlerBilibiliUser();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage user ids cannot be empty for bilibili user
     */
    public function testIdsCannotBeEmpty()
    {
        $crawler = new CrawlerBilibiliUser();
        $crawler->setConfig([
            'ids' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage numeric user ids expected for bilibili user
     */
    public function testIdsMustBeNumeric()
    {
        $crawler = new CrawlerBilibiliUser();
        $crawler->setConfig([
            'ids' => ['a120371896'],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for bilibili user
     */
    public function testPageCannotBeZero()
    {
        $crawler = new CrawlerBilibiliUser();
        $crawler->setConfig([
            'ids' => ['120371896'],
            'page' => 0,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid page setting for bilibili user
     */
    public function testPageCannotBeNegative()
    {
        $crawler = new CrawlerBilibiliUser();
        $crawler->setConfig([
            'ids' => ['120371896'],
            'page' => -1,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testCanGetEnoughMessageWithPageConfig()
    {
        $crawler = new CrawlerBilibiliUser();
        $crawler->setConfig([
            'ids' => ['120371896'],
            'page' => 2,
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertGreaterThanOrEqual(40,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('url_author',$value);
            $this->assertArrayHasKey('pic',$value);
            $this->assertArrayHasKey('created_at_time',$value);
            $this->assertArrayHasKey('title',$value);
            $this->assertArrayHasKey('author',$value);
            $this->assertArrayHasKey('aid',$value);
            $this->assertArrayHasKey('play',$value);
        }
    }

}
