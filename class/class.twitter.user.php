<?php

require_once('class.base.php');

class CrawlerTwitterUser extends CrawlerBase {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * 网络IO，执行抓取
	 */
	public function doCrawl() {
		if (!isset($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("user ids required for twitter user");
		}

		if (empty($this->crawl_config['ids'])) {
			throw new InvalidArgumentException("user ids cannot be empty for twitter user");
		}

		$page = $this->crawl_config['page'];
		if ($page <= 0) {
			throw new InvalidArgumentException("invalid page setting for twitter user");
		}

		$this->snoopy->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";
		
		$this->snoopy->use_gzip = false; // Comment this line if you have php built with zlib

		foreach ($this->crawl_config['ids'] as $ei) {
			for ($i=1; $i <= $page; $i++) { 
				$crawl_url = 'https://twitter.com/i/profiles/show/'.$ei.'/timeline/tweets?include_available_features=1&include_entities=1'; 

				if ($i > 1) {
					$crawl_url .= '&max_position='.$min_position;
				} else {
					$min_position_url = 'https://twitter.com/' . $ei;
					$this->snoopy->fetch($min_position_url);
					$min_position_result = $this->snoopy->results;
					if ($min_position_result) {
						$min_position_content = str_get_html($min_position_result);
						$min_position_node = $min_position_content->find('.stream-container', 0);
						if($min_position_node){
							$min_position = $min_position_node->getAttribute('data-min-position');
						}
					}
				}

				$this->log("开始请求地址:$crawl_url");

				$this->snoopy->fetch($crawl_url);
				
				if ($this->snoopy->results === null) {
					continue;
				}

				$crawl_result = $this->snoopy->results;

				$this->log("请求返回结果:$crawl_result");

				$result = json_decode($crawl_result, true);

				if (!is_array($result)) {
					continue;
				}

				$content = str_get_html($result['items_html']);
				$item_results = $content->find('li.js-stream-item .js-stream-tweet');

				if (isset($result['min_position'])) {
					$min_position = $result['min_position'];
				}
				
				if ($item_results) {
					foreach ($item_results as $br) {
						$obj = array();
						$obj['item_id'] = $br->getAttribute('data-item-id');
						$obj['link'] = 'https://twitter.com'.$br->getAttribute('data-permalink-path');
						$text = $br->find('.js-tweet-text', 0);
						if ($text) {
							$obj['text'] = $text->innertext();
						}
						$obj['title'] = '';
						$img = $br->find('.js-adaptive-photo');
						$obj['pics'] = array();
						if ($img) {
							foreach ($img as $ig) {
								$obj['pics'][] = $ig->getAttribute('data-image-url');
							}
						}
						$obj['author'] = $br->getAttribute('data-screen-name');
						
						$created_at = $br->find('.js-short-timestamp', 0);
						if ($created_at) {
							$obj['created_at_time'] = date('Y-m-d H:i',$created_at->getAttribute('data-time'));
						}

						$like_num = $br->find('.ProfileTweet-action--favorite .ProfileTweet-actionCount', 0);
						if ($like_num) {
							$obj['like_num'] = ($like_num->getAttribute('data-tweet-stat-count'));
						}

						$player = $br->find('.PlayableMedia-player', 0);
						if ($player) {
							$video_iframe_url = 'https://twitter.com/i/videos/tweet/'.$obj['item_id'].'?embed_source=clientlib&player_id=0&rpc_init=1';
							$this->snoopy->fetch($video_iframe_url);
							$video_result = $this->snoopy->results;
							if ($video_result) {
								$video_content = str_get_html($video_result);
								$video_node = $video_content->find('#playerContainer', 0);
								if ($video_node) {
									$video_data_config = htmlspecialchars_decode($video_node->getAttribute('data-config'));
									$video_data_config = json_decode($video_data_config, true);
									$obj['videos'] = $video_data_config['video_url'];
								}
							}
						} else {
							$player = $br->find('.card-type-player', 0);
							if ($player) {
								$video_iframe_url = $player->getAttribute('data-card-url');
								$this->snoopy->fetch($video_iframe_url);
								$video_result = $this->snoopy->results;
								if ($video_result) {
									$video_content = str_get_html($video_result);
									$video_node = $video_content->find('title', 0);
									if ($video_node) {
										$video_url = trim($video_node->innertext());
										if (strpos($video_url, 'youtu.be') !== false) {
											$video_url = str_replace(array('youtu.be/','?a'), array('www.youtube.com/watch?v=',''), $video_url);
										}
										$obj['videos'] = $video_url;
									}
								}
							}
						}
						
						$this->crawl_messages[] = $obj;
					}
				} else {
					break;
				}
			}
		}
	}

	public function doKeywordCheck() {
		if (isset($this->crawl_config['keyword_check'])) {
			$keyword_filter = $this->crawl_config['keyword_check'];
			if (!empty($keyword_filter)) {
				foreach ($this->crawl_messages as $ssk => $ssc) {
					foreach ($keyword_filter as $kf) {
						if (mb_strpos($ssc['text'], $kf) !== false) {
							$this->log('twitter正文筛出关键字匹配成功，删除数据:' . print_r($ssc, true));
							unset($this->crawl_messages[$ssk]);
						} 
					}
				}
			}
		}
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
		if (isset($this->crawl_config['image_check'])) {
			foreach ($this->crawl_messages as $ssk => $ssc) {
				//如果是视频，不检测图片限制
				if (isset($ssc['videos'])) {
					continue;
				}

				if (count($ssc['pics']) < $this->crawl_config['image_check']) {
					$this->log('不满足图片设置，删除数据:' . print_r($ssc, true));
					unset($this->crawl_messages[$ssk]);
				}
			}
		}
	}

	public function doVideoCheck() {
		if (isset($this->crawl_config['video_check'])) {
			foreach ($this->crawl_messages as $ssk => $ssc) {
				if (!isset($ssc['videos'])) {
					$this->log('不满足视频设置，删除数据:' . print_r($ssc, true));
					unset($this->crawl_messages[$ssk]);
				}
			}
		}
	}

	public function doMessage() {
		$this->log('抓取结果:' . print_r($this->crawl_messages, true));
	}
}