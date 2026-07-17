<?php
define('IN_BLOG',true);
session_start();
// 判断是否未安装，不存在配置文件直接跳转安装页面
if (!file_exists('config.php')){
    header("Location: install.php");
    exit;
}
require 'config.php';
require 'functions.php';
force_https();
$page = intval($_GET['p']??1);
$ps=8;
$off = ($page-1)*$ps;
$art_list = $pdo->query("SELECT * FROM blog_article WHERE is_show=1 ORDER BY add_time DESC limit {$off},{$ps}")->fetchAll();

// 查询加入我们数据 joinus
$joinus_list = $pdo->query("SELECT * FROM joinus WHERE is_show=1 ORDER BY sort ASC,id DESC")->fetchAll();

// =========新增：查询社交链接 sociallinks=========
$sociallinks_list = $pdo->query("SELECT * FROM sociallinks WHERE is_show=1 ORDER BY sort ASC,id DESC")->fetchAll();

// 查询ICP备案号
$icp_row = $pdo->query("SELECT * FROM icprecordno LIMIT 1")->fetch();
$icp_no = !empty($icp_row['no']) ? $icp_row['no'] : '';

// =========新增：读取博客名称与logo配置 nameandlogo表=========
$site_row = $pdo->query("SELECT * FROM nameandlogo LIMIT 1")->fetch();
$blog_name = !empty($site_row['blog_name']) ? $site_row['blog_name'] : '个人博客';
$logo_url  = !empty($site_row['logo_url'])  ? $site_row['logo_url']  : '';

//====================【新增侧边栏数据查询】====================
//Sidebar表：头像、简介、公告
$sidebar_row = $pdo->query("SELECT * FROM Sidebar LIMIT 1")->fetch();
$avatar_url = !empty($sidebar_row['avatar_url']) ? $sidebar_row['avatar_url'] : '';
$introduction = !empty($sidebar_row['introduction']) ? $sidebar_row['introduction'] : '暂无简介';
$bulletinboard = !empty($sidebar_row['bulletinboard']) ? $sidebar_row['bulletinboard'] : '暂无公告';

//分类列表
$cate_list = $pdo->query("SELECT * FROM category WHERE is_show=1 ORDER BY sort ASC,id DESC")->fetchAll();
//标签云
$tag_list = $pdo->query("SELECT * FROM tag WHERE is_show=1 ORDER BY sort ASC,id DESC")->fetchAll();
//============================================================
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=xss_echo($blog_name)?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery-3.6.0.min.js"></script>
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
}
.header-bar{
    background:
        linear-gradient(110deg,#159b46,#18b852,#26ce60);
    background-size: 200% 200%;
    animation: gradientFlow 8s ease infinite;
    color:#fff;
    padding:24px 0;
    box-shadow:
        0 4px 20px rgba(22,163,74,0.25),
        0 2px 8px rgba(22,163,74,0.12);
    position:sticky;
    top:0;
    z-index:99;
    backdrop-filter: blur(8px);
}
@keyframes gradientFlow {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
.header-bar h3{
    font-weight:600;
    letter-spacing:2px;
    text-shadow:0 2px 12px rgba(0,0,0,0.18);
    position:relative;
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
/*logo图片样式*/
.site-logo{
    height:42px;
    max-width:220px;
    object-fit:contain;
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

/*加入我们板块样式*/
.joinus-block{
    background:#ffffff;
    border-radius:24px;
    padding:40px 30px;
    margin-bottom:50px;
    box-shadow:0 4px 24px rgba(22,163,74,0.1);
}
.joinus-title{
    font-size:1.8rem;
    font-weight:600;
    color:#1e293b;
    margin-bottom:26px;
    position:relative;
    padding-bottom:12px;
}
.joinus-title:after{
    content:"";
    width:70px;
    height:4px;
    background:linear-gradient(90deg,#16a34a,#22c55e);
    position:absolute;
    left:0;
    bottom:0;
    border-radius:4px;
}
.join-item{
    padding:20px 18px;
    border:1px solid #e8f3ec;
    border-radius:18px;
    height:100%;
    transition:0.35s;
}
.join-item:hover{
    background:#f0fbf4;
    transform:translateY(-6px);
    box-shadow:0 8px 20px rgba(22,163,74,0.12);
}
.join-item h4{
    color:#16a34a;
    font-size:1.15rem;
    margin-bottom:12px;
}
.join-item p{
    color:#546378;
    line-height:1.7;
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
@keyframes footerLine {
    0%{background-position:0%}
    100%{background-position:220%}
}
/* =========新增社交链接样式========= */
.social-link-wrap{
    margin:16px 0;
}
.social-link-item{
    display:inline-block;
    margin:0 10px;
    padding:7px 16px;
    border:1px solid #cbd5e1;
    border-radius:50px;
    color:#475569;
    text-decoration:none;
    transition:0.3s ease;
}
.social-link-item:hover{
    border-color:#16a34a;
    color:#16a34a;
    background-color:#f0fbf4;
    transform:translateY(-3px);
}

/*全局滚动条美化*/
::-webkit-scrollbar{
    width:8px;
}
::-webkit-scrollbar-track{
    background:#f1f5f9;
}
::-webkit-scrollbar-thumb{
    background:linear-gradient(180deg,#16a34a,#22c55e);
    border-radius:10px;
}
::-webkit-scrollbar-thumb:hover{
    background:#15803d;
}
.icp-text a{
    color:#64748b;
    text-decoration:none;
}
.icp-text a:hover{
    color:#16a34a;
}

/*====================【新增侧边栏CSS】====================*/
.sidebar-card{
    background:#fff;
    border-radius:22px;
    padding:26px 22px;
    margin-bottom:30px;
    box-shadow:0 3px 16px rgba(22,163,74,0.09);
}
.sidebar-title{
    font-size:1.15rem;
    font-weight:600;
    color:#1e293b;
    padding-bottom:10px;
    margin-bottom:18px;
    border-bottom:2px solid #e8f3ec;
    position:relative;
}
.sidebar-title::after{
    content:"";
    width:45px;
    height:2px;
    background:#16a34a;
    position:absolute;
    left:0;
    bottom:-2px;
}
.avatar-box{
    text-align:center;
    margin-bottom:16px;
}
.avatar-img{
    width:110px;
    height:110px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #22c55e;
    padding:3px;
}
.intro-text{
    color:#475569;
    line-height:1.75;
    font-size:0.95rem;
}
.cate-item{
    display:block;
    padding:9px 0;
    border-bottom:1px dashed #edf2f7;
    color:#475569;
    text-decoration:none;
    transition:0.28s;
}
.cate-item:hover{
    color:#16a34a;
    padding-left:8px;
}
.tag-item{
    display:inline-block;
    padding:5px 12px;
    margin:4px 3px;
    border:1px solid #cbd5e1;
    border-radius:50px;
    font-size:0.86rem;
    color:#475569;
    text-decoration:none;
    transition:0.3s;
}
.tag-item:hover{
    background:#f0fbf4;
    border-color:#16a34a;
    color:#16a34a;
}
.bulletin-text{
    color:#475569;
    line-height:1.7;
    font-size:0.95rem;
}

/*=====日历组件样式=====*/
.calendar-box{
    width:100%;
}
.cal-head{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:12px;
}
.cal-head h5{
    margin:0;
    color:#16a34a;
}
.cal-week{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:4px;
    margin-bottom:8px;
}
.cal-week span{
    text-align:center;
    font-size:13px;
    color:#64748b;
    padding:4px 0;
}
.cal-day-wrap{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:4px;
}
.cal-day{
    text-align:center;
    font-size:14px;
    padding:7px 0;
    border-radius:8px;
    color:#475569;
    cursor:default;
}
.cal-day.other-month{
    color:#cbd5e1;
}
.cal-day.today{
    background:#16a34a;
    color:#fff;
    font-weight:bold;
}

/*=====天气组件样式=====*/
.weather-item{
    display:flex;
    align-items:center;
    justify-content:space-between;
}
.weather-temp{
    font-size:2rem;
    color:#1e293b;
    font-weight:500;
}
.weather-info{
    text-align:right;
    color:#475569;
    font-size:0.92rem;
    line-height:1.6;
}
.loading-text{
    color:#94a3b8;
    text-align:center;
    padding:15px 0;
}
    </style>
</head>
<body>
<div class="header-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <?php if(!empty($logo_url)):?>
            <img src="<?=xss_echo($logo_url)?>" alt="<?=xss_echo($blog_name)?>" class="site-logo">
        <?php else:?>
            <h3 class="m-0"><?=xss_echo($blog_name)?></h3>
        <?php endif;?>
        <div>
            <?php if(is_admin()):?>
                <a href="admin.php" class="text-white">后台管理</a>
            <?php else:?>
                <a href="login.php" class="text-white">管理员登录</a>
            <?php endif;?>
        </div>
    </div>
</div>
<div class="container mt-5">
    <!-- 加入我们 板块 -->
    <?php if(!empty($joinus_list)):?>
    <div class="joinus-block">
        <h2 class="joinus-title">加入我们</h2>
        <div class="row g-4">
            <?php foreach($joinus_list as $j):?>
            <div class="col-md-6 col-lg-4">
                <div class="join-item">
                    <h4><?=xss_echo($j['title'])?></h4>
                    <p><?=xss_echo($j['content'])?></p>
                </div>
            </div>
            <?php endforeach;?>
        </div>
    </div>
    <?php endif;?>

    <!-- =========【主区域+侧边栏布局】========= -->
    <div class="row">
        <!--左侧文章列表区域-->
        <div class="col-lg-8">
            <div class="row">
                <?php foreach($art_list as $a):?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow">
                        <div class="card-body">
                            <h5 class="card-title"><a href="article.php?id=<?=$a['id']?>"><?=xss_echo($a['title'])?></a></h5>
                            <p class="card-text text-muted small">发布时间：<?=xss_echo(date('Y‑m‑d',$a['add_time']))?></p>
                            <a href="article.php?id=<?=$a['id']?>" class="btn btn-outline-success btn-sm">阅读全文</a>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>

        <!--右侧侧边栏-->
        <div class="col-lg-4">
            <!--博主信息卡片-->
            <div class="sidebar-card">
                <h4 class="sidebar-title">关于博主</h4>
                <div class="avatar-box">
                    <?php if(!empty($avatar_url)): ?>
                        <img class="avatar-img" src="<?=xss_echo($avatar_url)?>" alt="博主头像">
                    <?php else: ?>
                        <img class="avatar-img" src="" alt="暂无头像">
                    <?php endif; ?>
                </div>
                <p class="intro-text"><?=xss_echo($introduction)?></p>
            </div>

            <!--日历组件卡片-->
            <div class="sidebar-card">
                <h4 class="sidebar-title">日历</h4>
                <div class="calendar-box">
                    <div class="cal-head">
                        <h5 id="calYM"></h5>
                        <div>
                            <button class="btn btn-sm btn-outline-success" id="prevMonth">&lt;</button>
                            <button class="btn btn-sm btn-outline-success" id="nextMonth">&gt;</button>
                        </div>
                    </div>
                    <div class="cal-week">
                        <span>日</span><span>一</span><span>二</span><span>三</span><span>四</span><span>五</span><span>六</span>
                    </div>
                    <div class="cal-day-wrap" id="calDays"></div>
                </div>
            </div>

            <!--天气组件卡片-->
            <div class="sidebar-card">
                <h4 class="sidebar-title">实时天气</h4>
                <div id="weatherBox">
                    <div class="loading-text">天气加载中…</div>
                </div>
            </div>

            <!--公告栏卡片-->
            <div class="sidebar-card">
                <h4 class="sidebar-title">站点公告</h4>
                <div class="bulletin-text">
                    <?=xss_echo($bulletinboard)?>
                </div>
            </div>

            <!--分类列表卡片-->
            <?php if(!empty($cate_list)):?>
            <div class="sidebar-card">
                <h4 class="sidebar-title">文章分类</h4>
                <?php foreach($cate_list as $cate):?>
                <a href="category.php?id=<?=$cate['id']?>" class="cate-item">
                    <?=xss_echo($cate['name'])?>
                </a>
                <?php endforeach;?>
            </div>
            <?php endif;?>

            <!--标签云卡片-->
            <?php if(!empty($tag_list)):?>
            <div class="sidebar-card">
                <h4 class="sidebar-title">标签云</h4>
                <?php foreach($tag_list as $tag):?>
                <a href="tag.php?id=<?=$tag['id']?>" class="tag-item">
                    <?=xss_echo($tag['name'])?>
                </a>
                <?php endforeach;?>
            </div>
            <?php endif;?>
        </div>
    </div>
</div>
<footer class="mt-5 py-4 bg-light text-center border-top">
    <div>
        <span class="text-muted"><?=xss_echo($blog_name)?> © <?php echo date('Y');?></span>
    </div>
    <!-- =========新增社交链接输出区域========= -->
    <?php if(!empty($sociallinks_list)):?>
    <div class="social-link-wrap">
        <span>社交链接：</span>
        <?php foreach($sociallinks_list as $link):?>
        <a target="_blank" href="<?=xss_echo($link['url'])?>" class="social-link-item">
            <?=xss_echo($link['name'])?>
        </a>
        <?php endforeach;?>
    </div>
    <?php endif;?>

    <?php if(!empty($icp_no)):?>
    <div class="mt-2 icp-text">
        <a href="https://beian.miit.gov.cn/" target="_blank"><?=xss_echo($icp_no)?></a>
    </div>
    <?php endif;?>
</footer>

<script>
// ==================日历JS==================
let nowDate = new Date();
let showYear = nowDate.getFullYear();
let showMonth = nowDate.getMonth();

function renderCalendar(y,m){
    const firstDay = new Date(y,m,1);
    const lastDay = new Date(y,m+1,0);
    const firstWeek = firstDay.getDay();
    const totalDay = lastDay.getDate();
    const today = new Date();
    const tY = today.getFullYear();
    const tM = today.getMonth();
    const tD = today.getDate();

    $("#calYM").text(y + "年" + (m+1) + "月");
    let html = "";
    //上月剩余天数
    const lastMonthDay = new Date(y,m,0).getDate();
    for(let i = firstWeek-1;i>=0;i--){
        html += `<div class="cal-day other-month">${lastMonthDay-i}</div>`;
    }
    //当月日期
    for(let d=1;d<=totalDay;d++){
        let cls = "cal-day";
        if(tY===y && tM===m && tD===d){
            cls += " today";
        }
        html += `<div class="${cls}">${d}</div>`;
    }
    //下月填充
    const totalRender = firstWeek + totalDay;
    const nextCount = 42 - totalRender;
    for(let n=1;n<=nextCount;n++){
        html += `<div class="cal-day other-month">${n}</div>`;
    }
    $("#calDays").html(html);
}
renderCalendar(showYear,showMonth);
$("#prevMonth").click(function(){
    showMonth--;
    if(showMonth<0){
        showMonth=11;
        showYear--;
    }
    renderCalendar(showYear,showMonth);
})
$("#nextMonth").click(function(){
    showMonth++;
    if(showMonth>11){
        showMonth=0;
        showYear++;
    }
    renderCalendar(showYear,showMonth);
})

// ==================天气JS（免费公开接口）==================
$(function(){
    $.getJSON("https://api.qweather.com/v7/ip?key=HE16030722111417427478d58059d2808",function(res){
        if(res.code === "200"){
            let cityId = res.location[0].adm2;
            let cityName = res.location[0].name;
            $.getJSON(`https://api.qweather.com/v7/weather/now?location=${cityId}&key=HE16030722111417427478d58059d2808`,function(w){
                if(w.code === "200"){
                    let temp = w.now.temp;
                    let text = w.now.text;
                    let wind = w.now.windDir + w.now.windScale + "级";
                    $("#weatherBox").html(`
                        <div class="weather-item">
                            <div class="weather-temp">${temp}°C</div>
                            <div class="weather-info">
                                <div>${cityName}</div>
                                <div>${text}</div>
                                <div>${wind}</div>
                            </div>
                        </div>
                    `)
                }else{
                    $("#weatherBox").html("<div class='loading-text'>天气获取失败</div>")
                }
            })
        }else{
            $("#weatherBox").html("<div class='loading-text'>天气获取失败</div>")
        }
    }).fail(function(){
        $("#weatherBox").html("<div class='loading-text'>网络异常，无法获取天气</div>")
    })
})
</script>
</body>
</html>
