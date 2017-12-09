<?php

use PHPUnit\Framework\TestCase;

require 'common.php';

/**
 * @covers CrawlerYinyuetaiSearch
 */
final class CrawlerYinyuetaiSearchTest extends TestCase
{
    public function testCanBeCreatedFromYinyuetaiSearch()
    {
        $this->assertInstanceOf(
            CrawlerYinyuetaiSearch::class,
            new CrawlerYinyuetaiSearch()
        );
    }

    public function testKeywordsCannotBeNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("keywords required for yinyuetai search");

        $crawler = new CrawlerYinyuetaiSearch();
        $crawler->setConfig([
         'keyword_check' => ['防弹少年团'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testKeywordsCannotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("keywords cannot be empty for yinyuetai search");

        $crawler = new CrawlerYinyuetaiSearch();
        $crawler->setConfig([
            'keywords' => [],
            'keyword_check' => ['防弹少年团'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testPageCannotBeZero()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid page setting for yinyuetai search");

        $crawler = new CrawlerYinyuetaiSearch();
        $crawler->setConfig([
            'keywords' => ['防弹少年团'],
            'page' => 0,
            'keyword_check' => ['魔兽争霸'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testPageCannotBeNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid page setting for yinyuetai search");

        $crawler = new CrawlerYinyuetaiSearch();
        $crawler->setConfig([
            'keywords' => ['防弹少年团'],
            'page' => -1,
            'keyword_check' => ['DOTA'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();
    }

    public function testCanGetEnoughMessageWithPageConfig()
    {
        $crawler = new CrawlerYinyuetaiSearch();
        $crawler->setConfig([
            'keywords' => ['防弹少年团'],
            'keyword_check' => [],
            'page' => 2,
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertGreaterThanOrEqual(20,count($crawler->getMessage()));
        foreach ($crawler->getMessage() as $key => $value) {
            $this->assertArrayHasKey('id',$value);
            $this->assertArrayHasKey('duration',$value);
            $this->assertArrayHasKey('pics',$value);
            $this->assertArrayHasKey('value',$value);
            $this->assertArrayHasKey('artists',$value);
            $this->assertArrayHasKey('title',$value);
        }
    }

    public function testCannotGetEnoughMessageWithKeywordcheckConfig()
    {
        $crawler = new CrawlerYinyuetaiSearch();
        $crawler->setConfig([
            'keywords' => ['防弹少年团'],
            'page' => 2,
            'keyword_check' => ['DNA'],
        ]);
        $crawler->prepareCrawl();
        $crawler->executeCrawl();

        $this->assertLessThanOrEqual(20,count($crawler->getMessage()));
    }

}
