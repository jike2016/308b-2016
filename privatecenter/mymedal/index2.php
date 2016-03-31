<script>
$('.lockpage').hide();
</script>
<style>
	.kinds {margin-bottom: 5px;}
	.maininfo-box {background-color: #FFFFFF; height: 665px;}
	.classinfo-box {min-height: 615px; padding: 0px;overflow-y: scroll;}
	.footer-box {background-color: #F0F0F0;  border: 1px solid #ccc; border-top: 0px;}
	.footer { background-color: #F0F0F0; width: 60%; margin: auto; height: 48px; padding: 15px 0px;}
	.footer p {display: inline-block; color: #5E5E5E;}
	.footer .p-14-red {color: #C9302C;}
	.footer .right,.center {float: right;}
	.footer .right{margin-right: 60px;}
	.footer a {margin-right: 40px;}	
	table td {text-align: center;}
	table {margin-top: 0px;}
	table thead {background-color:#F0F0F0; padding: 5px;}
	table thead tr {height: 50px; color: #777777;}
	.table > thead > tr > td { padding: 4px;}
	.table > thead > tr > td, .table > tbody > tr > td { vertical-align: middle;}
	.td1 {width: 5%;}
	.td2 {width: 15%;}
	.td3 {width: 10%;}
	.td4 {width: 20%;}
	.td5 {width: 30%;}
	.td6 {width: 20%;}

	table tbody .td4 {text-align: left;}
	table tbody .td1 img{width: 60px;}
</style>

<script type="text/javascript">
	//上下页的跳转
	$('.pre-btn').click(function() {  //上一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());//获取当前页码
		//alert(page);
		page--;
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("mymedal/index.php?page="+page);
	});
	$('.next-btn').click(function() {  //下一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());
		page++;
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("mymedal/index.php?page="+page);
	});

</script>


<?php

require_once("../../config.php");
require_once("../../lib/badgeslib.php");
require_once("../../badges/renderer.php");


$page = optional_param('page', 1, PARAM_INT);
global $DB;
global $USER;

echo_medals($page);//输出勋章列表

/**START 输出勋章列表 */
function echo_medals($page){

	$offset=($page-1)*10;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。

	global $DB;
	global $USER;

	$userID = $USER->id;

	echo '<div class="maininfo-box">
			<div class="classinfo-box">
				<table class="table table-hover">
					<thead>
						<tr>
							<td class="td1">序号</td>
							<td class="td2">证书图</td>
							<td class="td3">名称</td>
							<td class="td4">描述</td>
							<td class="td5">授证规则</td>
							<td class="td6">授予日期</td>
						</tr>
					</thead>
					<tbody>';

	//获取用户的勋章记录
	$records = badges_get_badges(1, 0, 'name', 'DESC', 0, 100, $USER->id);//备注：这里的参数有些写死了
	$badges             = new badge_collection($records);
	$medalcount = $badges->badges;//勋章记录数组
	//START 为分页查询做准备，重新划分数组**********************
	$new_badges = array();
	$badgescount1 = 1;//计数,当前记录号,用于与$offset 比较
	$badgescount2 = 1;//计数,取的记录数量
	foreach($badges->badges as $badges){
		if($badgescount1 > $offset && $badgescount2 <=10){ //从$offset ，向后获取10 条
			$new_badges[$badges->id] = $badges;//将勋章数组添加到新数组
			$badgescount2++;//记录数自增
		}
		$badgescount1++;//记录号自增
	}
	$badges->badges = $new_badges;//将划分好的10条记录替换原来的数组
	//END 为分页查询做准备，重新划分数组 *********************

	$no = ($page-1)*10+1;//序号
	foreach ($badges->badges as $badge) {
		$imageurl = moodle_url::make_pluginfile_url(1, 'badges', 'badgeimage', $badge->id, '/', 'f1', false);//获取勋章图片url
		$imageurl->param('refresh', rand(1, 10000));
		$attributes = array('src' => $imageurl, 'alt' => s($badge->name), 'class' => 'activatebadge');
		$medalimage = html_writer::empty_tag('img', $attributes);//勋章图片标签
		$name = $badge->name;//获取名称
		$description = $badge->description;//获取描述
		$criteria = print_badge_criteria($badge);//规则
		$time = userdate($badge->timemodified,'%Y.%m.%d');////授予日期

		echo '<tr>
					<td>'.$no.'</td>
					<td class="td1">'.$medalimage.'</td>
					<td>'.$name.'</td>
					<td>'.$description.'</td>
					<td class="td4">'.$criteria.'</td>
					<td>'.$time.'</td>
				</tr>';

		$no++;
	}

	echo '</tbody>
			</table>
		</div>';

	echo_end($page,$medalcount);//输出上下页按钮

}
/**END 输出勋章列表 */


/**START 输出上下页按钮
 * 这里由于数据是调用系统函数获得，与其他页面的写法会有所不同！！！
 */
function echo_end($page,$medalcount){

	$total = count($medalcount);
	$pagenum=ceil($total/10);//总页数
	echo '
	<div class="footer-box">
		<div class="footer">';
	if($page==1&&($pagenum==1||$pagenum==0)){
		echo '
		<a class="" style="color:#777; text-decoration:none">上一页</a>
		<a class="" style="color:#777; text-decoration:none">下一页</a>';
	}
	elseif($page==1){//第一页
		echo '
		<a class="" style="color:#777; text-decoration:none">上一页</a>
		<a class="next-btn" href="#">下一页</a>';
	}
	elseif($page==$pagenum){//最后一页
		echo '
		<a class="pre-btn" href="#">上一页</a>
		<a class="" style="color:#777; text-decoration:none">下一页</a>';
	}
	else{
		echo '
		<a class="pre-btn" href="#">上一页</a>
		<a class="next-btn" href="#">下一页</a>';
	}
	if($pagenum==0){
		echo '
				<div class="center">
					<p>第</p>
					<p id="pageid" class="p-14-red">'.$pagenum.'</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>共</p>
					<p class="p-14-red">'.$pagenum.'</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>每页显示</p>
					<p class="p-14-red">10</p>
					<p>条</p>
				</div>
			</div>
		</div>
		</div>
		';
	}
	else{
		echo '
				<div class="center">
					<p>每页显示</p>
					<p class="p-14-red">10</p>
					<p>条</p>
				</div>
				<div class="right">
					<p>共</p>
					<p class="p-14-red">'.$pagenum.'</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>第</p>
					<p id="pageid" class="p-14-red">'.$page.'</p>
					<p>页</p>
				</div>
			</div>
		</div>
		</div>
		';
	}

}

/**END 输出上下页按钮 */


/**START 获取规则 （备注：此方法是从D:\WWW\moodle\badges\renderer.php 中复制来的,并有所改动）*/
function print_badge_criteria(badge $badge, $short = '') {
	$agg = $badge->get_aggregation_methods();
	if (empty($badge->criteria)) {
		return get_string('nocriteria', 'badges');
	}

	$overalldescr = '';
	$overall = $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL];
	if (!$short && !empty($overall->description)) {
//		$overalldescr = $this->output->box(
//			format_text($overall->description, $overall->descriptionformat, array('context' => $badge->get_context())),
//			'criteria-description'
//		);
		$overalldescr = format_text($overall->description, $overall->descriptionformat, array('context' => $badge->get_context()));

	}

	// Get the condition string.
	if (count($badge->criteria) == 2) {
		$condition = '';
		if (!$short) {
			$condition = get_string('criteria_descr', 'badges');
		}
	} else {
		$condition = get_string('criteria_descr_' . $short . BADGE_CRITERIA_TYPE_OVERALL, 'badges',
			core_text::strtoupper($agg[$badge->get_aggregation_method()]));
	}

	unset($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]);

	$items = array();
	// If only one criterion left, make sure its description goe to the top.
	if (count($badge->criteria) == 1) {
		$c = reset($badge->criteria);
		if (!$short && !empty($c->description)) {
//			$overalldescr = $this->output->box(
//				format_text($c->description, $c->descriptionformat, array('context' => $badge->get_context())),
//				'criteria-description'
//			);
			$overalldescr = format_text($c->description, $c->descriptionformat, array('context' => $badge->get_context()));
		}
		if (count($c->params) == 1) {
			$items[] = get_string('criteria_descr_single_' . $short . $c->criteriatype , 'badges') .
				$c->get_details($short);
		} else {
			$items[] = get_string('criteria_descr_' . $short . $c->criteriatype, 'badges',
					core_text::strtoupper($agg[$badge->get_aggregation_method($c->criteriatype)])) .
				$c->get_details($short);
		}
	} else {
		foreach ($badge->criteria as $type => $c) {
			$criteriadescr = '';
			if (!$short && !empty($c->description)) {
//				$criteriadescr = $this->output->box(
//					format_text($c->description, $c->descriptionformat, array('context' => $badge->get_context())),
//					'criteria-description'
//				);
				$criteriadescr = format_text($c->description, $c->descriptionformat, array('context' => $badge->get_context()));
			}
			if (count($c->params) == 1) {
				$items[] = get_string('criteria_descr_single_' . $short . $type , 'badges') .
					$c->get_details($short) . $criteriadescr;
			} else {
				$items[] = get_string('criteria_descr_' . $short . $type , 'badges',
						core_text::strtoupper($agg[$badge->get_aggregation_method($type)])) .
					$c->get_details($short) .
					$criteriadescr;
			}
		}
	}

	return $overalldescr . $condition . html_writer::alist($items, array(), 'ul');;
}
/**END 获取规则*/


?>

