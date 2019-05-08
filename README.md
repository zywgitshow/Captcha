# php 验证吗类

# 安装
> git clone
# 使用
    引入 Captcha.php 文件
    $captcha=new Captcha();
    //生成验证码图片
    $img=$captcha->captcha();
    //获得验证码字符串
    $code=$captcha->code;