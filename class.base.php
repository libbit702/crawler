<?php

require_once("lib/simple_html_dom.php");
require_once("lib/php-hooks.php");
require_once("lib/Snoopy.class.php");

abstract class CrawlerBase 
{
	protected static $_hooks = null;
	protected $snoopy = null;
	protected $crawl_messages = array();
	protected $crawl_config = array();

	public function __construct() {
		self::$_hooks = new Hooks();
		$this->snoopy = new Snoopy();
 		mb_internal_encoding('UTF-8');

	}
	
	public function __destruct() {
		$this->snoopy = null;
		$this->crawl_messages = array();
		self::$_hooks->remove_all_actions('Run_Crawl_Setup');
	}
	
	/**
	 * 设置抓取配置
	 *
	 * @access public
	 */
	public function setConfig($config) {
		if (!is_array($config) || count($config) == 0) {
			throw new Exception("crawl config required as non-empty array");
		}
		$default = array(
			'page' => 1,
			'debug' => false,
		);
		$this->crawl_config = array_merge($default, $config);
	}

	/**
	 * 预设的抓取流程
	 *
	 * @access public
	 */
	public function prepareCrawl() {

		self::$_hooks->add_action('Run_Crawl_Setup', array($this, 'doCrawl'));

		// 筛除关键词设置
		if (isset($this->crawl_config['keyword_check'])) {
			self::$_hooks->add_action('Run_Crawl_Setup', array($this, 'doKeywordCheck'));
		}
		
		// 内容发布时间设置
		if (isset($this->crawl_config['public_time_check'])) {
			self::$_hooks->add_action('Run_Crawl_Setup', array($this, 'doPublicTimeCheck'));
		}
	
		// 点赞数限制
		if (isset($this->crawl_config['like_check'])) {
			self::$_hooks->add_action('Run_Crawl_Setup', array($this, 'doLikeCheck'));
		}

		// 图片个数限制
		if (isset($this->crawl_config['image_check'])) {
			self::$_hooks->add_action('Run_Crawl_Setup', array($this, 'doImageCheck'));
		}

		// 视频个数限制
		if (isset($this->crawl_config['video_check'])) {
			self::$_hooks->add_action('Run_Crawl_Setup', array($this, 'doVideoCheck'));
		}

		self::$_hooks->add_action('Run_Crawl_Setup', array($this, 'doMessage'));	
	}

	/**
	 * 执行钩子
	 *
	 * @access public
	 */
	public function executeCrawl() {
		self::$_hooks->do_action('Run_Crawl_Setup');
	}

	/**
	 * 执行抓取
	 *
	 * @access public
	 */
	abstract public function doCrawl();

	/**
	 * 处理过滤后的抓取数据
	 *
	 * @access public
	 */
	abstract public function doMessage();

	/**
	 * 文本关键字检测
	 *
	 * @access public
	 */
	public function doKeywordCheck() {
		echo __METHOD__ . "\n";
	}

	/**
	 * 点赞检测
	 *
	 * @access public
	 */
	public function doLikeCheck() {
		echo __METHOD__ . "\n";
	}

	/**
	 * 内容发布时间检测
	 *
	 * @access public
	 */
	public function doPublicTimeCheck() {
		echo __METHOD__ . "\n";
	}

	/**
	 * 图片检测
	 *
	 * @access public
	 */
	public function doImageCheck() {
		echo __METHOD__ . "\n";
	}

	/**
	 * 视频检测
	 *
	 * @access public
	 */
	public function doVideoCheck() {
		echo __METHOD__ . "\n";
	}

	/**
	 * 打印日志信息
	 *
	 * @param string msg:需要打印的消息
	 * @access protected
	 */
	protected function log($msg) {
		if ($this->crawl_config['debug']) {
			echo sprintf("[%s]:%s\n", date('c'), $msg);
		}
	}

	/**
	 * 将微博的消息发布时间转换为时间戳
	 * 
	 * @param string str:微博发布时间
	 * @access protected
	 */
	protected function getWeiboMblogTime($str) {
		$real_time = null;
		$time_now = time();
		$date_today = date('Y-m-d');
		$year_now = date('Y');
		if (mb_strpos($str, '分钟') !== false) {
			$real_time = $time_now - intval($str) * 60;
		} else if (mb_strpos($str, '今天') !== false) {
			$real_time = strtotime($date_today . ' ' . trim(str_replace('今天', '', $str)));
		} else if (strpos($str, '-') !== false) {
			$real_time = strtotime($year_now . '-' . $str);
		} else {
			$real_time = strtotime($str);
		}
		return $real_time;
	}

	/**
	 * 返回抓取过滤后的数据
	 * 
	 * @access protected
	 */
	public function getMessage() {
		return $this->crawl_messages;
	}
}