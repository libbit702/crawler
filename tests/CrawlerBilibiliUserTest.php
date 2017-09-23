<?php

use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . "/../class.bilibili.user.php");

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

    public function testIdsCannotBeNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("user ids required for bilibili user");

        $crawler = new CrawlerBilibiliUser();
        $crawler->setConfig([
         'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testIdsCannotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("user ids cannot be empty for bilibili user");

        $crawler = new CrawlerBilibiliUser();
        $crawler->setConfig([
            'ids' => [],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testIdsMustBeNumeric()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("numeric user ids expected for bilibili user");

        $crawler = new CrawlerBilibiliUser();
        $crawler->setConfig([
            'ids' => ['a120371896'],
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testPageCannotBeZero()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid page setting for bilibili user");

        $crawler = new CrawlerBilibiliUser();
        $crawler->setConfig([
            'ids' => ['120371896'],
            'page' => 0,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testPageCannotBeNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid page setting for bilibili user");

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
