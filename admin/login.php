<?php
/**
 * 登录
**/
$verifycode = 1;//验证码开关

if(!function_exists("imagecreate") || !file_exists('code.php'))$verifycode=0;
include("../includes/common.php");
if(isset($_POST['user']) && isset($_POST['pass'])){
	if(!$_SESSION['pass_error'])$_SESSION['pass_error']=0;
	$user=daddslashes($_POST['user']);
	$pass=daddslashes($_POST['pass']);
	$code=daddslashes($_POST['code']);
	if ($verifycode==1 && (!$code || strtolower($code) != $_SESSION['vc_code'])) {
		unset($_SESSION['vc_code']);
		@header('Location: login.php?error='.urlencode('验证码错误！'));
		exit;
	}elseif($_SESSION['pass_error']>5) {
		@header('Location: login.php?error='.urlencode('登录失败次数过多，请稍后再试！'));
		exit;
	}elseif($user==$conf['admin_user'] && $pass==$conf['admin_pwd']) {
		$session=md5($user.$pass.$password_hash);
		setcookie("admin_session", $session, time() + 604800);
		@header('Location: ./');
		exit;
	}else {
		$_SESSION['pass_error']++;
		@header('Location: login.php?error='.urlencode('用户名或密码不正确！'));
		exit;
	}
}elseif(isset($_GET['logout'])){
	setcookie("admin_session", "", time() - 604800);
	@header('Location: login.php?msg='.urlencode('您已成功注销本次登陆！'));
	exit;
}elseif($islogin==1){
	@header('Location: ./');
	exit;
}
$title='用户登录';
include './head.php';
$err = isset($_GET['error']) ? $_GET['error'] : '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<style>
.login-wrap{min-height:calc(100vh - 56px);display:flex;align-items:center;justify-content:center;padding:32px 16px;}
.login-card{width:100%;max-width:380px;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);padding:32px;}
.login-head{text-align:center;margin-bottom:24px;}
.login-head i{font-size:40px;color:var(--accent,#0078d4);}
.login-head h1{font-size:20px;font-weight:600;margin:12px 0 4px;color:#242424;}
.login-head p{color:#6b7280;margin:0;font-size:13px;}
.login-msg{display:block;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:13px;border:1px solid;}
.alert-error{background:#fde7e9;color:#a4262c;border-color:#f1707b;}
.alert-success{background:#dff6dd;color:#107c10;border-color:#9ad19a;}
.login-form{display:flex;flex-direction:column;gap:14px;}
.login-form fluent-text-field{width:100%;}
.login-code{display:flex;gap:8px;align-items:center;}
.login-code fluent-text-field{flex:1;}
.login-code img{border-radius:4px;cursor:pointer;height:40px;}
.login-submit{width:100%;margin-top:4px;}
.login-submit::part(control){height:44px;font-size:15px;}
</style>
<div class="login-wrap">
  <div class="login-card">
    <div class="login-head">
      <i class="fa fa-cube"></i>
      <h1>外链网盘管理中心</h1>
      <p>用户登录</p>
    </div>
    <?php if($err){?>
      <div class="login-msg alert-error"><i class="fa fa-exclamation-circle"></i> <?=$err?></div>
    <?php }?>
    <?php if($msg){?>
      <div class="login-msg alert-success"><i class="fa fa-check-circle"></i> <?=$msg?></div>
    <?php }?>
    <form action="./login.php" method="post" class="login-form">
      <fluent-text-field name="user" placeholder="用户名" autocomplete="username" required></fluent-text-field>
      <fluent-text-field type="password" name="pass" placeholder="密码" autocomplete="current-password" required></fluent-text-field>
      <?php if($verifycode==1){?>
      <div class="login-code">
        <fluent-text-field name="code" placeholder="输入验证码" autocomplete="off" required></fluent-text-field>
        <img src="./code.php?r=<?=time()?>" onclick="this.src='./code.php?r='+Math.random();" title="点击更换验证码" alt="验证码">
      </div>
      <?php }?>
      <fluent-button type="submit" appearance="accent" class="login-submit">登录</fluent-button>
    </form>
  </div>
</div>
<?php include './footer.php';?>
