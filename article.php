<?php
define('IN_BLOG',true);
session_start();
require 'config.php';
require 'functions.php';
force_https();
$id = intval($_GET['id']??0);
$art = $pdo->query("SELECT * FROM blog_article WHERE id={$id} AND is_show=1")->fetch();
if(!$art){
    echo '文章不存在';
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh‑CN">
<head>
    <meta charset="UTF‑8">
    <meta name="viewport" content="width=device‑width, initial‑scale=1.0">
    <title><?=xss_echo($art['title'])?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery‑3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    *{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body{
    background:
        radial-gradient(circle at 10% 20%, rgba(34,197,94,0.06) 0%,transparent 40%),
        radial-gradient(circle at 90% 85%, rgba(22,163,74,0.07) 0%,transparent 45%),
        linear-gradient(135deg,#f7f9fc 0%,#edf2f7 100%);
    background-attachment: fixed;
    min-height: 100vh;
    font-family:"PingFang SC","Microsoft YaHei",sans-serif;
    overflow-x: hidden;
    color:#222;
    transition: background 0.45s ease, color 0.45s ease;
}
/* ========== 深色模式全局样式 纯黑主题 ========== */
body.dark-mode{
    background:#000000 !important;
    background-image:none !important;
    color:#ffffff !important;
}
body.dark-mode *{
    color:#ffffff !important;
}
body.dark-mode .card{
    background-color:#000000 !important;
    box-shadow:0 0 18px rgba(34,197,94,0.18);
}
body.dark-mode .header-bar{
    background:#000000;
    background:linear-gradient(110deg,#111,#1a1a1a,#111);
}
body.dark-mode footer{
    background:#000000 !important;
    border-top:1px solid #222222 !important;
}
body.dark-mode .btn-outline-success{
    border-color:#ffffff;
    background-color:#000000;
    color:#fff !important;
}
body.dark-mode .btn-outline-success:hover{
    background:#222222;
    border-color:#22c55e;
}
body.dark-mode ::-webkit-scrollbar-track{
    background:#111111;
}
body.dark-mode .article-time{
    color:#cccccc !important;
}
/* ====== 新增海量全局动画定义 ====== */
@keyframes gradientFlow {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
@keyframes footerLine {
    0%{background-position:0%}
    100%{background-position:220%}
}
/*页面整体入场动画*/
@keyframes pageFadeIn {
    0%{opacity:0;transform:translateY(40px) scale(0.97);}
    100%{opacity:1;transform:translateY(0) scale(1);}
}
/*标题呼吸放大+微光闪烁*/
@keyframes titleBreathe {
    0%{transform:scale(1);text-shadow:0 0 4px rgba(22,163,74,0.2);}
    50%{transform:scale(1.018);text-shadow:0 0 18px rgba(22,163,74,0.38);}
    100%{transform:scale(1);text-shadow:0 0 4px rgba(22,163,74,0.2);}
}
/*时间标签上下浮动*/
@keyframes floatUpDown {
    0%{transform:translateY(0px);}
    50%{transform:translateY(-7px);}
    100%{transform:translateY(0px);}
}
/*分割线扫光动画*/
@keyframes lineSweep {
    0%{background-position:-300px 0;}
    100%{background-position:600px 0;}
}
/*卡片外圈光晕脉冲扩散*/
@keyframes cardGlowPulse {
    0%{box-shadow:0 0 0 0px rgba(34,197,94,0.12);}
    70%{box-shadow:0 0 0 16px rgba(34,197,94,0);}
    100%{box-shadow:0 0 0 0px rgba(34,197,94,0);}
}
/*返回按钮悬浮弹跳*/
@keyframes btnBounce {
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-4px);}
}
/*背景圆点缓慢漂浮粒子*/
@keyframes particleDrift1{
    0%{transform:translate(0,0) rotate(0deg);opacity:0.04;}
    50%{transform:translate(120px,-160px)rotate(180deg);opacity:0.09;}
    100%{transform:translate(-60px,-280px)rotate(360deg);opacity:0.04;}
}
@keyframes particleDrift2{
    0%{transform:translate(0,0) rotate(0deg);opacity:0.05;}
    50%{transform:translate(-140px,-100px)rotate(-180deg);opacity:0.1;}
    100%{transform:translate(80px,-220px)rotate(-360deg);opacity:0.05;}
}
/*边角微光划过动画*/
@keyframes cornerLightSweep{
    0%{transform:translate(-100%,-100%);}
    100%{transform:translate(100%,100%);}
}
/*正文逐段渐入*/
@keyframes contentFadeIn{
    0%{opacity:0;transform:translateY(24px);filter:blur(3px);}
    100%{opacity:1;transform:translateY(0);filter:blur(0px);}
}
/*滚动条滑块流光*/
@keyframes scrollbarShine{
    0%{filter:brightness(1);}
    50%{filter:brightness(1.35);}
    100%{filter:brightness(1);}
}
/*头部栏左右轻微摇摆微动*/
@keyframes headerShakeTiny{
    0%,100%{transform:skewX(0deg);}
    50%{transform:skewX(0.3deg);}
}

/*背景漂浮粒子装饰层*/
body::before{
    content:"";
    position:fixed;
    width:260px;height:260px;
    border-radius:50%;
    background:rgba(34,197,94,0.13);
    top:12%;left:6%;
    filter:blur(70px);
    z-index:-2;
    animation:particleDrift1 22s ease-in-out infinite;
}
body.dark-mode::before{
    opacity:0.22;
}
body::after{
    content:"";
    position:fixed;
    width:320px;height:320px;
    border-radius:50%;
    background:rgba(22,163,74,0.11);
    bottom:10%;right:4%;
    filter:blur(85px);
    z-index:-2;
    animation:particleDrift2 28s ease-in-out infinite;
}
body.dark-mode::after{
    opacity:0.22;
}

.header-bar{
    background:#28a745;
    background:linear-gradient(110deg,#21963c,#28a745,#30b950);
    background-size: 200% 200%;
    animation: gradientFlow 8s ease infinite,headerShakeTiny 7s ease-in-out infinite;
    color:#fff;
    padding:18px 0;
    box-shadow:
        0 4px 20px rgba(40,167,69,0.25),
        0 2px 8px rgba(40,167,69,0.12);
    position:sticky;
    top:0;
    z-index:99;
    backdrop-filter: blur(8px);
}
.header-bar h3{
    font-weight:600;
    letter-spacing:2px;
    text-shadow:0 2px 12px rgba(0,0,0,0.18);
    position:relative;
    animation:titleBreathe 5.2s ease-in-out infinite;
}
.header-bar h3::after{
    content:"";
    position:absolute;
    width:0%;
    height:3px;
    background:#ffffff;
    left:0;
    bottom:-6px;
    border-radius:3px;
    transition:0.4s ease;
}
.header-bar:hover h3::after{
    width:100%;
}
/*深色模式切换按钮*/
.dark-toggle{
    position:absolute;
    right:140px;
    top:50%;
    transform:translateY(-50%);
    border:1px solid #fff;
    padding:6px 14px;
    border-radius:30px;
    cursor:pointer;
    font-size:14px;
    transition:0.3s;
}
.dark-toggle:hover{
    background:rgba(255,255,255,0.2);
}
/*字体缩放按钮*/
.font-toggle-group{
    position:absolute;
    right:24px;
    top:50%;
    transform:translateY(-50%);
    display:flex;
    gap:6px;
}
.font-btn{
    border:1px solid #fff;
    color:#fff;
    padding:4px 10px;
    border-radius:30px;
    cursor:pointer;
    font-size:14px;
    background:transparent;
    transition:0.3s;
}
.font-btn:hover{
    background:rgba(255,255,255,0.2);
}
.font-percent{
    color:#fff;
    line-height:28px;
    font-size:13px;
    min-width:42px;
    text-align:center;
}

.header-bar a{
    padding:8px 18px;
    border-radius:50px;
    border:1px solid rgba(255,255,255,0.45);
    transition:all 0.32s ease;
    position:relative;
    overflow:hidden;
}
.header-bar a::before{
    content:"";
    position:absolute;
    width:120%;
    height:120%;
    background:rgba(255,255,255,0.15);
    top:110%;
    left:-10%;
    border-radius:50%;
    transition:0.35s ease;
    z-index:0;
}
.header-bar a:hover::before{
    top:-10%;
}
.header-bar a:hover{
    background:rgba(255,255,255,0.22);
    border-color:#ffffff;
    box-shadow:0 0 16px rgba(255,255,255,0.25);
}
.card{
    border:none;
    border-radius:22px;
    overflow:hidden;
    transition: all 0.42s cubic‑bezier(0.22, 1, 0.27, 1);
    box-shadow:
        0 2px 12px rgba(34,197,94,0.04),
        0 6px 22px rgba(34,197,94,0.09);
    background:#ffffff;
    position:relative;
    /*卡片入场动画*/
    animation:pageFadeIn 1.1s ease-out 0.22s forwards,cardGlowPulse 7.5s infinite ease-in-out;
    opacity:0;
}
/*卡片左上角微光装饰条*/
.card::before{
    content:"";
    position:absolute;
    width:140px;
    height:140px;
    background:radial-gradient(circle,rgba(34,197,94,0.08),transparent 70%);
    top:-60px;
    right:-60px;
    border-radius:50%;
    z-index:0;
    animation:floatUpDown 9s ease-in-out infinite reverse;
}
/*卡片斜向扫光*/
.card::after{
    content:"";
    position:absolute;
    width:100%;
    height:100%;
    top:0;
    left:0;
    background:linear-gradient(135deg,transparent 40%,rgba(255,255,255,0.32),transparent 60%);
    animation:cornerLightSweep 4.8s ease-in-out infinite;
    z-index:0;
}
.card:hover{
    transform: translateY(-12px) scale(1.015);
    box-shadow:
        0 8px 30px rgba(34,197,94,0.11),
        0 18px 50px rgba(22,163,74,0.20);
}
.card-body{
    padding:32px 26px;
    position:relative;
    z-index:1;
}
.card-title{
    font-size:1.28rem;
    font-weight:600;
    margin-bottom:14px;
    line-height:1.58;
}
.card-title a{
    color:#1e293b;
    text-decoration:none;
    transition:color 0.32s ease,text-shadow 0.32s ease;
}
.card-title a:hover{
    color:#16a34a;
    text-shadow:0 0 8px rgba(34,197,94,0.18);
}
.card-text{
    margin-bottom:22px;
    font-size:0.94rem;
    letter-spacing:0.5px;
    color:#475569;
    line-height:1.75;
}
.btn-outline-success{
    border‑radius:50px;
    padding:8px 24px;
    border-width:1.5px;
    transition:all 0.35s ease;
    position:relative;
    overflow:hidden;
    z-index:1;
    animation:btnBounce 3.4s ease-in-out infinite;
}
.btn-outline-success::before{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(90deg,#16a34a,#22c55e);
    opacity:0;
    transition:0.35s ease;
    z-index:-1;
}
.btn-outline-success:hover{
    color:#fff;
    transform:translateY(-2px);
    box-shadow:0 6px 18px rgba(22,163,74,0.32);
}
.btn-outline-success:hover::before{
    opacity:1;
}
footer{
    margin‑top:80px !important;
    padding:36px 0 !important;
    background:#ffffff !important;
    border‑top:1px solid #e2e8f0 !important;
    box‑shadow:0 ‑4px 20px rgba(0,0,0,0.04);
    font‑size:0.95rem;
    color:#64748b;
    position:relative;
}
footer::before{
    content:"";
    position:absolute;
    width:100%;
    height:4px;
    left:0;
    top:0;
    background:linear-gradient(90deg,#16a34a,#22c55e,#16a34a);
    background-size:220% 100%;
    animation:footerLine 6s linear infinite;
}
/*全局滚动条美化+流光动画*/
::-webkit-scrollbar{
    width:8px;
}
::-webkit-scrollbar-track{
    background:#f1f5f9;
}
::-webkit-scrollbar-thumb{
    background:linear-gradient(180deg,#16a34a,#22c55e);
    border-radius:10px;
    animation:scrollbarShine 2.8s ease infinite;
}
::-webkit-scrollbar-thumb:hover{
    background:#15803d;
}

/*文章标题动画*/
.article-title{
    animation:titleBreathe 4.6s ease-in-out infinite;
}
/*发布时间浮动动画*/
.article-time{
    animation:floatUpDown 3.8s ease-in-out infinite;
    color:#666;
}
/*分割线扫光特效*/
hr{
    border:0;
    height:2px;
    background:linear-gradient(90deg,transparent,#28a745,transparent);
    background-size:300px 100%;
    animation:lineSweep 3.2s linear infinite;
    margin:26px 0;
}
body.dark-mode hr{
    background:linear-gradient(90deg,transparent,#28a745,transparent);
}
/*文章正文延迟淡入动画*/
.article-content{
    animation:contentFadeIn 1.4s ease-out 0.7s forwards;
    opacity:0;
}
/*返回首页按钮入场延迟动画*/
.back-btn{
    opacity:0;
    animation:pageFadeIn 0.9s ease-out 0.1s forwards;
}

/*=====字体缩放控制区域=====*/
.article-wrap{
    transition: font-size 0.3s ease;
}
    </style>
</head>
<body>
<div class="header-bar">
    <div class="container position-relative">
        <h3 class="m‑0">我的个人博客</h3>
        <div class="dark-toggle" id="darkToggle">🌙 深色模式</div>
        <div class="font-toggle-group">
            <button class="font-btn" id="fontMinus">A‑</button>
            <span class="font-percent" id="fontPercent">100%</span>
            <button class="font-btn" id="fontPlus">A+</button>
        </div>
    </div>
</div>
<div class="container mt‑4" style="max‑width:860px;">
    <a href="index.php" class="btn btn‑sm btn‑outline‑success mb‑3 back-btn">← 返回首页</a>
    <div class="card shadow p‑4 article-wrap">
        <h2 class="text‑success article-title"><?=xss_echo($art['title'])?></h2>
        <div class="text‑muted my‑3 article-time">发布时间：<?=xss_echo(date('Y‑m‑d H:i',$art['add_time']))?></div>
        <hr>
        <div class="article-content" style="line‑height:1.8;font‑size:16px;">
            <?=nl2br(xss_echo($art['content']))?>
        </div>
    </div>
</div>

<script>
//读取本地存储的深色模式状态
const bodyDom = document.body;
const toggleBtn = document.getElementById('darkToggle');
let isDark = localStorage.getItem('blogDarkMode') === '1';

function setDarkMode(openDark){
    if(openDark){
        bodyDom.classList.add('dark-mode');
        toggleBtn.innerText = '☀️ 浅色模式';
        localStorage.setItem('blogDarkMode','1');
    }else{
        bodyDom.classList.remove('dark-mode');
        toggleBtn.innerText = '🌙 深色模式';
        localStorage.setItem('blogDarkMode','0');
    }
}
//页面初始化加载主题
setDarkMode(isDark);
//点击切换
toggleBtn.addEventListener('click',()=>{
    isDark = !isDark;
    setDarkMode(isDark);
})


// =========== 字体缩放逻辑 50%‑200% 步长10% ===========
const articleWrap = document.querySelector('.article-wrap');
const fontPercentText = document.getElementById('fontPercent');
const fontMinusBtn = document.getElementById('fontMinus');
const fontPlusBtn = document.getElementById('fontPlus');
const MIN_SCALE = 50;
const MAX_SCALE = 200;
const STEP = 10;

let fontSizeScale = parseInt(localStorage.getItem('blogFontScale')) || 100;

function applyFontScale(){
    articleWrap.style.fontSize = fontSizeScale + '%';
    fontPercentText.innerText = fontSizeScale + '%';
    localStorage.setItem('blogFontScale',String(fontSizeScale));
}
//初始化加载字号
applyFontScale();

fontMinusBtn.addEventListener('click',()=>{
    if(fontSizeScale > MIN_SCALE){
        fontSizeScale -= STEP;
        applyFontScale();
    }
})
fontPlusBtn.addEventListener('click',()=>{
    if(fontSizeScale < MAX_SCALE){
        fontSizeScale += STEP;
        applyFontScale();
    }
})
</script>
</body>
</html>
