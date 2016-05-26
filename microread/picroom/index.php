<?php
require_once ("../../config.php");
global $USER;
global $DB;
$bgimg = $DB->get_record_sql('select indexbg_url from mdl_pic_indexbg where id=1');
$searchs = $DB->get_records_sql('select * from mdl_pic_recommended_search');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>图库首页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/image-index.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
	</head>
	<body>
		<div class="wrap" style="background-image: url(<?php echo $bgimg->indexbg_url;?>)">
			<!-- 顶部导航 -->
			<?php
				require_once ("../common/pic_head_login.php");//微阅登录导航栏：首页、微阅、微课、、、、
			?>

			<!-- 主体 -->
			<div class="main">
				<div class="home_search">
					<a href="#" class="logo">
						<img src="../img/logo.png" alt="港城慕课" />
					</a>
					<form action="image-search.php" method="get" id="search-form">
						<input type="text" name="word" id="search-word">
						<input type="submit" value="图库搜索" id="search-submit">
					</form>
				</div>

				<div class="img-box">
					<ul class="list-unstyled">
						<?php
							foreach($searchs as $search){
								echo '<li>
									<a href="image-search.php?word='.$search->name.'" class="img-block">
										<img src="'.$search->picurl.'" alt="" />
										<div class="img-label">'.$search->name.'</div>
									</a>
								</li>';
							}
						?>
					</ul>
				</div>
			</div>
			<!-- 主体 end -->
			<!-- 顶部导航 end -->
		</div>

	</body>
</html>
