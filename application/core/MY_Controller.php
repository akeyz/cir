<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class MY_Controller extends REST_Controller {

	function __construct(){
		parent::__construct();
		
		//加载常用的函数库
		$this->load_common_library();
		//加载常用的帮助类
		$this->load_common_helper();
		//加载常用的模型类
		$this->load_common_model();
	}
	
	/**
	 * 加载通用的函数库
	 */
	private function load_common_library(){
		$this->load->library(array('aes'));
	}
	
	/**
	 * 加载通用的帮助类
	 */
	private function load_common_helper(){
		$this->load->helper(array('language'));
	}
	
	private function load_common_model(){
		
	}

	/**
	 * 输出错误信息，格式array('code'=>0);
	 */
	public function respon_error_and_exit(){
		$error = array(
				'code'=>0,
				'message'=>'',
		);
		$this->response($error);
		exit();
	}
	
	public function response_and_exit($data){
		$aes_key = $this->generate_code();
		parent::response(array('content' => urlencode($aes_key.$this->aes->encode($data,$aes_key))));
		exit();
	}

	/**
	 * @return 解密加密的信息 Array类型
	 */
	public function decode_post_content()
	{
		$content = $this->post('content');
		if (isset($content)) {
			return $this->aes->decode(substr($content, 16), substr($content, 0, 16));
		} else {
			exit();
		}
	}

	/**
	 * @param  长度
	 * @return 生成随机code String类型
	 */
	public function generate_code($length = 16) 
	{  
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';  
		$code = '';
		for ($i=0; $i < $length; $i++) { 
			$code .= $chars[mt_rand(0, strlen($chars) - 1)];
		} 
		return $code;  
	}

	public function get_web_response($url,$timeout=60)
	{
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch,CURLOPT_ENCODING, "");
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_AUTOREFERER, true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch,CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch,CURLOPT_MAXREDIRS, 10);
		$content = curl_exec( $ch );
		$response = curl_getinfo( $ch );

		if ($response['http_code'] == '200') {
			return $content;
		}
		return NULL;
	}
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */