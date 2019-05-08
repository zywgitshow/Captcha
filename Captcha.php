<?php 

class Captcha{
	protected $length;//验证码长度
	protected $type;//验证码类型 0：数字，1：字母，2：字母数字混合
	protected $width;//宽
	protected $height;//高
	protected $code;//验证码字符串
	protected $image;//图像
    /**
     * [验证码构造函数]
     * @access public
     * @param integer $length [验证码长度]
     * @param integer $type   [验证码类型 0：数字，1：字母，2：字母数字混合]
     * @param integer $width  [验证码宽度]
     * @param integer $height [验证码高度]
     * @return   void
     */
	public function __construct($length=4,$type=2,$width=100,$height=30){
		//判断服务器环境是否安装了GD库
		if (!extension_loaded('gd')) {
			if (!dl('gd.so')) {
				exit("未加载GD扩展");
			}
		}
		$this->length=$length;
		$this->type=$type;
		$this->width=$width;
		$this->height=$height;
		$this->code=$this->createCode();
	}
	// 获取$this->code
	public function __get($name){
		if ($name=='code') {
			return $this->code;
		}
		return false;

	}
	/**
	 * 生成验证码字符串
	 * 
	 * @return [String] [返回验证码字符串]
	 */
	protected function createCode(){
		switch ($this->type) {
			// 0：数字，
			case 0:
				$code=$this->getNumCode();
				break;
			// 1：字母，
			case 1:
				$code=$this->getCharCode();
				break;
			// 2：字母数字混合
			case 2:
				$code=$this->getMixCode();
				break;
			
			default:
				$code=$this->getMixCode();
				break;
		}
		return	$code;
	}
	
	/**
	 * @return [String] [获得数字验证码]
	 */
	protected function getNumCode(){
		$str=join('',range(0,9));
		$code=substr(str_shuffle($str),0,$this->length);
		return $code;
	}
	/**
	 * [getCharCode 获得字母验证码]
	 * @return [String] [获得字母验证码]
	 */
	protected function getCharCode(){
		$str=join('',range('a','z'));
		$str.=strtoupper($str);

		$code=substr(str_shuffle($str),0,$this->length);
		return $code;
	}
	// 获得数字字母混合验证码
	protected function getMixCode(){
		$num=join('',range(0,9));
		$char=join('',range('a','z'));
		$str=$num.$char.strtoupper($char);
		$code=substr(str_shuffle($str),0,$this->length);
		return $code;
	}
	// 创建画布
	protected function createImage(){
		$this->image=imagecreatetruecolor($this->width, $this->height);
	}
	// 填充背景
	protected function   fillBg(){
		imagefill($this->image, 0, 0, $this->lightColor());
	}
	// 浅色
	protected function  lightColor(){
		return imagecolorallocate($this->image, mt_rand(130,255), mt_rand(130,255), mt_rand(130,255));
	}
	// 深色
	protected function  darkColor(){
		return imagecolorallocate($this->image, mt_rand(0,120), mt_rand(0,120), mt_rand(0,120));
	}
	// 将验证码字符串添加到图像
	protected function drawCode(){
		$fontsize = 15;
		$width=ceil($this->width/$this->length);
		for ($i=0; $i <$this->length ; $i++) { 
			//设置坐标
		    // $x = ($i*$width)+rand(5,10);
		    // $y = rand(5,10);
		    $x = rand($i*$width+1,($i+1)*$width-10);
		    $y = rand(1,$this->height-15);
		    imagestring($this->image, $fontsize, $x, $y, $this->code[$i],
		     $this->darkColor());
		}
	}
	// 画干扰点
	protected function drawPoint(){
		for ($i=0; $i < 200; $i++) { 
			$x=mt_rand(1,$this->width-1);
			$y=mt_rand(1,$this->height-1);
			imagesetpixel($this->image, $x, $y, $this->lightColor());
		}

	}
	// 画干扰线
	protected function drawLine(){
		for ($i=0; $i < 3; $i++) { 
			$x1=mt_rand(1,$this->width-1);
			$y1=mt_rand(1,$this->height-1);
			$x2=mt_rand(1,$this->width-1);
			$y2=mt_rand(1,$this->height-1);
			imageline($this->image, $x1, $y1, $x2, $y2, $this->lightColor());
		}

	}

	// 输出图像
	protected function showImage(){
		header('Content-Type:image/png');
		// imagepng($this->image,'code.png');
		imagepng($this->image);
	}
	//生成图像验证码 
	public function captcha(){
		// 创建画布
		$this->createImage();
		// 填充背景
		$this->fillBg();
		// 将验证码字符串添加到图像
		$this->drawCode();
		// 画干扰点
		$this->drawPoint();
		// 画干扰线
		$this->drawLine();
		// 输出图像
		$this->showImage();
	
	}
	// 验证
	 public function check(){
	 	$code=strtolower($this->code);
	 	if ($code==strtoupper($_POST['code'])) {
	 		return true;
	 	}
	 	return false;
	 }

	// 销毁验证码图片
	public function __destruct(){
		imagedestroy($this->image);
	}




}


?>