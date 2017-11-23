<?php

use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . "/../class.vlive.php");

/**
 * @covers CrawlerVlive
 */
final class CrawlerVliveTest extends TestCase
{
    public function testCanBeCreatedFromVlive()
    {
        $this->assertInstanceOf(
            CrawlerVlive::class,
            new CrawlerVlive()
        );
    }

    public function testCanGetEnoughMessageWithoutDateConfig()
    {
        $crawler = new CrawlerVlive();
        $crawler->setConfig([]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertGreaterThanOrEqual(10,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('link',$value);
            $this->assertArrayHasKey('title',$value);
            $this->assertArrayHasKey('created_at_time',$value);
            $this->assertArrayHasKey('time',$value);
            $this->assertArrayHasKey('author',$value);
            $this->assertArrayHasKey('pics',$value);
        }
    }

    public function testCanGetFixedMessageWithDateConfig()
    {
        $crawler = new CrawlerVlive();
        $crawler->setConfig([
            'date' => '20170924',
            'country' => 'CN',
            'offset' => '-60'
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertEquals(48,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('link',$value);
            $this->assertArrayHasKey('title',$value);
            $this->assertArrayHasKey('created_at_time',$value);
            $this->assertArrayHasKey('time',$value);
            $this->assertArrayHasKey('author',$value);
            $this->assertArrayHasKey('pics',$value);
        }
    }

    public function testCannotGetEnoughMessageWithPublicTimeCheckConfig()
    {
        $crawler = new CrawlerVlive();
        $crawler->setConfig([
            'date' => '20170924',
            'country' => 'CN',
            'offset' => '-60',
            'public_time_check' => '2017-09-24 23:01'
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertEquals(2,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('link',$value);
            $this->assertArrayHasKey('title',$value);
            $this->assertArrayHasKey('created_at_time',$value);
            $this->assertArrayHasKey('time',$value);
            $this->assertArrayHasKey('author',$value);
            $this->assertArrayHasKey('pics',$value);
        }
    }
}
