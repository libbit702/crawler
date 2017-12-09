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
		if (!is_array($config)) {
			throw new Exception("crawl config required as non-empty array");
		}
		$default = array(
			'page' => 1,
			'debug' => false,
			'url_handler' => 'snoopy'
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
		$this->log( __METHOD__ );
	}

	/**
	 * 点赞检测
	 *
	 * @access public
	 */
	public function doLikeCheck() {
		$this->log( __METHOD__ );
	}

	/**
	 * 内容发布时间检测
	 *
	 * @access public
	 */
	public function doPublicTimeCheck() {
		$this->log( __METHOD__ );
	}

	/**
	 * 图片检测
	 *
	 * @access public
	 */
	public function doImageCheck() {
		$this->log( __METHOD__ );
	}

	/**
	 * 视频检测
	 *
	 * @access public
	 */
	public function doVideoCheck() {
		$this->log( __METHOD__ );
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

	/**
	 * Send a GET requst using cURL 
	 * @param string $url to request 
	 * @param array $options for cURL 
	 * @return string 
	 * 
	 * @access protected
	 */
	protected function curl_get($url, array $options = array()) 
	{    
	    $defaults = array( 
	        CURLOPT_URL => $url, 
	        CURLOPT_HEADER => 0, 
	        CURLOPT_RETURNTRANSFER => TRUE, 
	        CURLOPT_TIMEOUT => 30
	    ); 

	    $ch = curl_init(); 
	    curl_setopt_array($ch, ($options + $defaults)); 
	    if( ! $result = curl_exec($ch)) 
	    { 
	        trigger_error(curl_error($ch)); 
	    } 
	    curl_close($ch); 
	    return $result; 
	} 

	/** 
	* Send a POST requst using cURL 
	* @param string $url to request 
	* @param array $post values to send 
	* @param array $options for cURL
	* @return string 
	* 
	* @access protected
	*/ 
	protected function curl_post($url, $post = array(), $options = array()) 
	{ 
		$defaults = array( 
			CURLOPT_POST => 1, 
			CURLOPT_HEADER => 0, 
			CURLOPT_URL => $url, 
			CURLOPT_FRESH_CONNECT => 1, 
			CURLOPT_RETURNTRANSFER => 1, 
			CURLOPT_FORBID_REUSE => 1, 
			CURLOPT_TIMEOUT => 3, 
			CURLOPT_CONNECTTIMEOUT =>2
		); 

		$ch = curl_init($url); 
		curl_setopt_array($ch, $options+$defaults);

		if($post){
			curl_setopt($ch, CURLOPT_POSTFIELDS, ($post));
		}

		if( ! $result = curl_exec($ch)) 
		{  
			trigger_error(curl_error($ch)); 
		} 
		curl_close($ch); 
		return $result; 
	} 
}