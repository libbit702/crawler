<?php

require_once('class.base.php');

class CrawlerVlive extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";

		$crawl_url = 'http://www.vlive.tv/upcoming';

		$date_config = date('Ymd');
		if (isset($this->crawl_config['date'])) {
			$crawl_url .= '?d='.$this->crawl_config['date'];
			$date_config = $this->crawl_config['date'];
		}

		$this->log("开始请求地址:$crawl_url");

		if (isset($this->crawl_config['country'])) {
			$this->snoopy->cookies['userCountry'] = $this->crawl_config['country'];
		}

		if (isset($this->crawl_config['offset'])) {
			$this->snoopy->cookies['timezoneOffset'] = $this->crawl_config['offset'];
		}

		$this->snoopy->fetch($crawl_url);

		if ($this->snoopy->results === false) {
			return;
		}

		$content = str_get_html($this->snoopy->results);

		$item_results = $content->find('.upcoming_list li');

		$time_now = date('Ymd H:i A');

		if ($item_results) {
			foreach ($item_results as $br) {
				$obj = array();
				$videoSeq = $br->getAttribute('data-seq');

				$link_node = $br->find('a._title', 0);
				$obj['link'] = 'http://www.vlive.tv'.$link_node->getAttribute('href');
				$obj['title'] = $link_node->innertext();

				$time_node = $br->find('.time', 0);
				$obj['time'] = $date_config . ' ' . $time_node->innertext();
				$obj['created_at_time'] = date('Y-m-d H:i', strtotime($obj['time']));

				$img_node = $br->find('a._videoThumb img', 0); 
				$obj['pics'] = array();
				if ($img_node) {
					$obj['pics'][] = $img_node->getAttribute('src');
				}
				$author_node = $br->find('em.name', 0);
				if ($author_node) {
					$obj['author'] = $author_node->innertext();
				}

				if (strtotime($obj['time']) < strtotime($time_now)) {
					$status_url = $obj['link'];
					$this->snoopy->fetch($status_url);
					preg_match('#vlive.video.init\(([\S\s]*?)\);#', $this->snoopy->results, $matches);
					if (isset($matches[1])) {
						list(,,,,,$videoid,$key,) = explode(',', $matches[1]);
						$videoid = trim(str_replace('"', '', $videoid));
						$key = trim(str_replace('"', '', $key));
						$video_info_url = sprintf('http://global.apis.naver.com/rmcnmv/rmcnmv/vod_play_videoInfo.json?key=%s&pid=&sid=2024&ver=2.0&devt=html5_pc&doct=json&ptc=http&cpt=vtt&cpl=zh_CN&lc=zh_CN&videoId=%s&cc=CN', $key, $videoid);
						$obj['videos'] = $video_info_url;
					}
				}
				$this->crawl_messages[] = $obj;
			}
		}
	}

	public function doKeywordCheck() {
		//B站Up主数据不检测关键字
	}

	public function doPublicTimeCheck() {
		if (isset($this->crawl_config['public_time_check'])) {
			foreach ($this->crawl_messages as $ssk => $ssc) {
				if ($ssc['created_at_time'] < $this->crawl_config['public_time_check']) {
					$this->log('不满足发布时间限制，删除数据:' . print_r($ssc, true));
					unset($this->crawl_messages[$ssk]);
				}
			}
		}
	}

	public function doImageCheck() {
		//B站Up主数据不检测图片
	}

	public function doVideoCheck() {
		//B站Up主数据不检测视频
	}

	/**
	 * 经过过滤后的数据，可以做后续处理
	 */
	public function doMessage() {
		// print_r($this->crawl_messages);
	}
}