<?php

// use PHPUnit\Framework\TestCase;

require_once(dirname(__FILE__) . "/../class.youtube.php");

/**
 * @covers CrawlerYoutube
 */
class CrawlerYoutubeTestOld extends PHPUnit_Framework_TestCase
{
    public function testCanBeCreatedFromYoutube()
    {
        $this->assertInstanceOf(
            'CrawlerYoutube',
            new CrawlerYoutube()
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage ids required for youtube
     */
    public function testKeywordsCannotBeNull()
    {
        $crawler = new CrawlerYoutube();
        $crawler->setConfig([]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage ids cannot be empty for youtube
     */
    public function testKeywordsCannotBeEmpty()
    {
        $crawler = new CrawlerYoutube();
        $crawler->setConfig([
            'ids' => []
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testCanGetFixedMessageWithIds()
    {
        $crawler = new CrawlerYoutube();
        $crawler->setConfig([
            'ids' => ['Rnbo016gdRg','cdyY-C4KH1w'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertEquals(2,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('watch_time',$value);
            $this->assertArrayHasKey('title',$value);
            $this->assertArrayHasKey('youtube_view',$value);
            $this->assertArrayHasKey('created_at_time',$value);
            $this->assertArrayHasKey('url_author',$value);
            $this->assertArrayHasKey('link',$value);
        }
    }
}
