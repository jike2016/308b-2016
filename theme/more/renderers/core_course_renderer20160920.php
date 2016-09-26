<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_bootstrapbase
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//echo '11';exit;
require_once($CFG->dirroot . "/course/renderer.php");
require_once($CFG->dirroot . "/lib/dml/moodle_database.php");
require_once($CFG->dirroot . "/lib/coursecatlib.php");

class theme_more_core_course_renderer extends core_course_renderer {

	/**
	 * start 2016-03-02 徐东威
	 * 选课页面的入口，（这里替换原来的方法 course_info_box ）
	 * Renders course info box.
	 * @param stdClass|course_in_list $course
	 * @return string
	 */
	public function my_course_info_box(stdClass $course,$forms) {

		global $OUTPUT;
		$content = '';

		$message = '<span class="glyphicon glyphicon-exclamation-sign"></span>';//添加感叹号
		$message .= get_string('notenrollable', 'enrol');//获取提示信息

		$url = get_local_referer(false);
		$button = $OUTPUT->continue_button($url);
//		$button 的内容如下：
//		$button = "<div class=\"continuebutton\">
//					<form method=\"get\" action=\"http://localhost/moodle/course/index.php\">
//						<div>
//							<input type=\"submit\" value=\"继续\" />
//						</div>
//					</form>
//				</div>";

		//非自主选课，将moodle原本的按钮替换
		$newButton = '<button class="btn btn-primary">继续</button>';
		$newButtonForm = str_replace('<input type="submit" value="继续" />',$newButton,$button);

		//自主选课
		$joinBtnForm = '';
		foreach($forms as $form){
			$joinBtnForm = $form;
			//$message = strip_tags($joinBtnForm);//修改提示信息
			$message = '';//取消提示信息了

			//去掉表单中多余的信息
			$joinBtnFormArray = explode('fieldset',$joinBtnForm);
			$joinBtnForm = $joinBtnFormArray[0];
			$joinBtnForm .= 'input name="submitbutton" value="选课" type="submit" id="id_submitbutton" /></div></form></div>';
		}

		$content .='<!--主体内容 1月21日 郑栩基 -->
				<div class="main container">
					<div class="cooursehead">
						<a class="coursetitle" href="#"><span class="glyphicon glyphicon-tasks"></span> &nbsp;'.$course->fullname.'</a>
						<p class="choosetips">&nbsp;'.$message.'</p>
					</div>
					<div class="courseimg">
						<img '.$this->my_get_course_formatted_summary_pix(new course_in_list($course)).' width="450" height="266"/>
					</div>

					<div class="courseinfo">
						<p>
						'.strip_tags($course->summary).'
						</p>
					</div>
					<div style="clear:both;"></div>
					<div style="width: 100%; height: 20px;"></div>
					<div style="clear:both;"></div>
					<div class="choosebtnbox">
						<button class="btn btn-primary">继续</button>
					</div>
				</div>
				<!--主体内容 1月21日 郑栩基 end-->';

		//将修好的按钮再次替换
		if($joinBtnForm != ''){
			$content = str_replace('<button class="btn btn-primary">继续</button>',$joinBtnForm,$content);
		}else{
			$content = str_replace('<button class="btn btn-primary">继续</button>',$newButtonForm,$content);
		}


//		$content .= $this->output->box_start('generalbox info');
//		$chelper = new coursecat_helper();
//		$chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
//		$content .= $this->coursecat_coursebox($chelper, $course);
//		$content .= $this->output->box_end();

		return $content;

	}


	public function my_search_summary_pix($summary){
		$pix_start='src="';
		$pix_end='"';
		$start_position=strpos($summary,$pix_start);
		if($start_position==false){return '';}
		$end_position=strpos($summary,$pix_end,$start_position+5);
		$my_pix=substr($summary,$start_position,$end_position-$start_position+1);
		return $my_pix;
	}


	//START 获取summary中的图片 /** 2016-03-02 徐东威 */
	public function my_get_course_formatted_summary_pix($course) {
		global $CFG;
		require_once($CFG->libdir. '/filelib.php');

		//$options = (array)$options;
		$context = context_course::instance($course->id);
		//if (!isset($options['context'])) {
		// TODO see MDL-38521
		// option 1 (current), page context - no code required
		// option 2, system context
		// $options['context'] = context_system::instance();
		// option 3, course context:
		// $options['context'] = $context;
		// option 4, course category context:
		// $options['context'] = $context->get_parent_context();
		// }
		//$summary即摘要
		//echo $course->summary;exit;
		$my_course_summary_pix=$this->my_search_summary_pix($course->summary);
		$summary = file_rewrite_pluginfile_urls($my_course_summary_pix, 'pluginfile.php', $context->id, 'course', 'summary', null);
		$summary = format_text($summary, $course->summaryformat,null, $course->id);
		// if (!empty($this->searchcriteria['search'])) {
		// $summary = highlight($this->searchcriteria['search'], $summary);
		// }

		return $summary;
	}
	//END 获取summary中的图片
	//end 徐东威 2016-03-02

/**======= start 课程列表页 ================================================================== */
	/** start 课程列表页入口 */
	public function my_course_category($category){

		global $USER;
		/** Start 按照用户所属级别，筛选课程 */
		if($USER->id != 0 ){//如果不是访客身份，即已登录
			$this->set_userAllowCourse();
		}
		/** end 按照用户所属级别，筛选课程 */

		if(!isset($_GET["dep1categoryid"])){//方向
			$dep1categoryid = null;
		}
		else{
			/** Start 参数转换为整数,防注入  20160301 毛英东 */
			//原代码:$dep1categoryid = $_GET["dep1categoryid"];
			$dep1categoryid = intval($_GET["dep1categoryid"]);
			/** End */
		}
		if(!isset($_GET["dep2categoryid"])){//分类
			$dep2categoryid = null;
		}
		else{
			/** Start 参数转换为整数,防注入  20160301 毛英东 */
			//原代码:$dep2categoryid = $_GET["dep2categoryid"];
			$dep2categoryid = intval($_GET["dep2categoryid"]);
			/** End */
		}
		if(!isset($_GET["sortkind"])){//排序规则，1是最新，2是最热
			$sortkind = null;
		}
		else{
			/** Start 参数转换为整数,防注入  20160301 毛英东 */
			//原代码: $sortkind = $_GET["sortkind"];
			$sortkind = intval($_GET["sortkind"]);
			/** End */
		}

		//输出方向（顶级分类）
		$category_result = $this->my_course_category_subfirst($dep1categoryid);
		$output = $category_result->output;
		$current_category = $category_result->current_category;
		$output .= '<!--主板块-->
					<div class="main">
						<p class="allcourse">
							<a href="#">'.$current_category.'</a>
						</p>';
		//输出分类（所有二级分类）
		$output .=$this->my_course_category_subsecond($dep1categoryid,$dep2categoryid);
		///输出排序方式，增加日、周、月热门排序
		$output .= $this->my_print_sortkind($sortkind);
		//输出课程列表
		if($dep1categoryid==null&&$dep2categoryid==null){
//			$output .= $this->my_course_category_display(null,null,$sortkind);//全部
			$output .= $this->my_course_category_display(null,null,$sortkind,$dep1categoryid,$dep2categoryid);//全部
		}
		elseif($dep2categoryid==null){
//			$output .= $this->my_course_category_display($dep1categoryid,1,$sortkind);//第一层，要查询子类的课程
			$output .= $this->my_course_category_display($dep1categoryid,1,$sortkind,$dep1categoryid,$dep2categoryid);//第一层，要查询子类的课程
		}
		else{
//			$output .= $this->my_course_category_display($dep2categoryid,null,$sortkind);//只输出第二层
			$output .= $this->my_course_category_display($dep2categoryid,null,$sortkind,$dep1categoryid,$dep2categoryid);//只输出第二层
		}

		$output .= '</div>
					<!--主板块 end-->';

		return $output;
	}

	/** 界面修改前 */
	public function my_course_category2($category){
		if(!isset($_GET["dep1categoryid"])){//方向
			$dep1categoryid = null;
		}
		else{
			/** Start 参数转换为整数,防注入  20160301 毛英东 */
			//原代码:$dep1categoryid = $_GET["dep1categoryid"];
			$dep1categoryid = intval($_GET["dep1categoryid"]);
			/** End */
		}
		if(!isset($_GET["dep2categoryid"])){//分类
			$dep2categoryid = null;
		}
		else{
			/** Start 参数转换为整数,防注入  20160301 毛英东 */
			//原代码:$dep2categoryid = $_GET["dep2categoryid"];
			$dep2categoryid = intval($_GET["dep2categoryid"]);
			/** End */
		}
		if(!isset($_GET["sortkind"])){//排序规则，1是最新，2是最热
			$sortkind = null;
		}
		else{
			/** Start 参数转换为整数,防注入  20160301 毛英东 */
			//原代码: $sortkind = $_GET["sortkind"];
			$sortkind = intval($_GET["sortkind"]);
			/** End */
		}


		$output = '<!--主面板-->
		<div id="main">
			<div class="container">
				<div class="course-content">
					<div class="course-nav-box">
						<div class="course-nav-hd">
							<p>全部课程</p>
						</div>';
		//输出方向
		$output .=$this->my_course_category_subfirst2($dep1categoryid);
		//输出分类
		$output .=$this->my_course_category_subsecond2($dep1categoryid,$dep2categoryid);
		$output .= '
						
					</div>
					<!--marquee style="width: 100%;">《主题教育》课程最新上线</marquee-->
					<div class="course-tool-bar clearfix">
						<div class="tool-left l">';
		/** Start 输出排序方式，增加日、周、月热门排序  20160301 毛英东 */
		//最新
		if($sortkind == 1){
			$output .= '<a href="javascript:void(0)" class="sort-item active" onclick="sel5(1);">最新</a>';
		}else{
			$output .= '<a href="javascript:void(0)" class="sort-item" onclick="sel5(1);">最新</a>';
		}
		//日热门
		if($sortkind == 2){
			$output .= '<a href="javascript:void(0)" class="sort-item active" onclick="sel5(2);">周热门</a>';
		}else{
			$output .= '<a href="javascript:void(0)" class="sort-item" onclick="sel5(2);">周热门</a>';
		}
		//周热门
		if($sortkind == 3){
			$output .= '<a href="javascript:void(0)" class="sort-item active" onclick="sel5(3);">月热门</a>';
		}else{
			$output .= '<a href="javascript:void(0)" class="sort-item" onclick="sel5(3);">月热门</a>';
		}
		//月热门
		if($sortkind == 4){
			$output .= '<a href="javascript:void(0)" class="sort-item active" onclick="sel5(4);">总热门</a>';
		}else{
			$output .= '<a href="javascript:void(0)" class="sort-item" onclick="sel5(4);">总热门</a>';
		}
		/** Start	增加好评榜  20160303 毛英东 */
		//好评榜
		if($sortkind == 5){
			$output .= '<a href="javascript:void(0)" class="sort-item active" onclick="sel5(5);">好评榜</a>';
		}else{
			$output .= '<a href="javascript:void(0)" class="sort-item" onclick="sel5(5);">好评榜</a>';
		}
		/** End  */
//原代码：
//		if($sortkind==1){
//			$output .='
//			<a href="#" class="sort-item active" onclick="sel5(1);">最新</a>
//			<a href="#" class="sort-item" onclick="sel5(2);">最热</a>';
//		}
//		elseif($sortkind==2){
//			$output .='
//			<a href="#" class="sort-item" onclick="sel5(1);">最新</a>
//			<a href="#" class="sort-item active" onclick="sel5(2);">最热</a>';
//		}
//		else{
//			$output .='
//			<a href="#" class="sort-item" onclick="sel5(1);">最新</a>
//			<a href="#" class="sort-item" onclick="sel5(2);">最热</a>';
//		}
		/** End */
		/* 右上角的换页按钮 */
		$output .='
						</div>

						<div class="tool-right r">
							<span class="tool-item tool-pager">
                                                <span class="pager-num">
                            <b class="pager-cur">1</b>/<em class="pager-total">24</em>
                        </span>
							<a href="javascript:void(0)" class="pager-action pager-prev hide-text disabled">上一页</a>

							<a href="#" class="pager-action pager-next hide-text">下一页</a>
							</span>
						</div>
					</div>';
		//输出课程列表
		if($dep1categoryid==null&&$dep2categoryid==null){
			$output .= $this->my_course_category_display2(null,null,$sortkind);//全部
		}
		elseif($dep2categoryid==null){
			$output .= $this->my_course_category_display2($dep1categoryid,1,$sortkind);//第一层，要查询子类的课程
		}
		else{
			$output .= $this->my_course_category_display2($dep2categoryid,null,$sortkind);//只输出第二层
		}
		$output .= '			
				</div>
			</div>
		</div>';

		 return $output;
	}

	/** end 课程列表页入口 */

	/** start 输出课程列表 */
	protected function my_course_category_display($categoryid=null,$dep=null,$sortkind=null,$dep1categoryid,$dep2categoryid){
		global $DB;
		global $USER;
		//如果当前角色只能查看部分课程
		$sql_add = "";
		if($USER->ava_course_flag_my){
			if($USER->ava_course_my){
				$sql_add = "and a.id in ($USER->ava_course_my)";
			}
			else{//没有可查看的课程
				$sql_add = " and a.id = -1 ";
			}
		}

		/** Start 分页  20160304 毛英东 */
		if(isset($_GET['page'])){
			$current_page = intval($_GET['page']);
			$current_page = $current_page > 0 ? $current_page : 1;
		}else{
			$current_page = 1;
		}
		$page_size = 15;	//每页记录条数
		$page_offset = ($current_page - 1) * $page_size;	//查询记录起始位置
		/** End */

		/** Start 输出排序方式，增加日、周、月热门排序  20160301 毛英东 */
		//查询上次更新浏览次数的时间
		$update_time = $DB -> get_record_sql('select timeorder from `mdl_course_order_my` limit 1');
		if((time()-3600*24) > $update_time->timeorder){	//更新时间到了
			//获取所有课程ID
			$sql = 'select id from mdl_course a where id != 1 '.$sql_add;
			$courses_id_my = $DB -> get_records_sql($sql);
			foreach($courses_id_my as $course_id_i){
				//更新总浏览数
				$DB -> execute("update mdl_course_order_my set timeorder = ".time().", count_sum = (SELECT count(*)  FROM `mdl_logstore_standard_log` WHERE target = 'course' and action = 'viewed' and courseid = $course_id_i->id) where courseid=$course_id_i->id");
				//更新月浏览数
				$DB -> execute("update mdl_course_order_my set timeorder = ".time().", count_month = (SELECT count(*)  FROM `mdl_logstore_standard_log` WHERE target = 'course' and action = 'viewed' and courseid = $course_id_i->id and timecreated > ". (time() - 3600*24*30) .") where courseid=$course_id_i->id");
				//更新周浏览数
				$DB -> execute("update mdl_course_order_my set timeorder = ".time().", count_week = (SELECT count(*)  FROM `mdl_logstore_standard_log` WHERE target = 'course' and action = 'viewed' and courseid = $course_id_i->id and timecreated > ". (time() - 3600*24*7) .") where courseid=$course_id_i->id");
			}
		}//更新浏览次数结束

		$order_by_my = array(2 => 'count_week', 3 => 'count_month', 4 => 'count_sum');	//热门排序方式
		if($categoryid==null){//全部
			//查询记录总数
			$sql = "select count(*) as total FROM mdl_course a where id!=1 and visible=1 ".$sql_add;
			$record_count = $DB -> get_record_sql($sql);
			//是否排序
			//SELECT c.id, c.fullname FROM `mdl_course` c join `mdl_course_order_my` b on c.id = b.courseid WHERE c.id != 1 ORDER BY b.`count_week` desc limit 50
			switch($sortkind){
				case 1:		//按最新排序
					$sql =  'select id,fullname,summary FROM mdl_course a where id!=1 '.$sql_add.' and visible=1 order by timecreated desc limit '.$page_offset.', '.$page_size;
					$courses = $DB->get_records_sql($sql);
					break;
				case 2:	//按周热门排序
				case 3:	//按月热门排序
				case 4:	//按总热门排序
					$sql = 'SELECT a.id, a.fullname, a.summary FROM `mdl_course` a join `mdl_course_order_my` b on a.id = b.courseid WHERE a.id != 1 '.$sql_add.' and a.visible=1 ORDER BY b.`'.$order_by_my[$sortkind].'` desc limit '.$page_offset.', '.$page_size;
					$courses = $DB -> get_records_sql($sql);
					break;
				/** Start 增加好评榜  20160303 毛英东 */
				case 5:	//好评榜
					$sql = 'SELECT a.id, a.fullname, a.summary FROM `mdl_course` a join `mdl_score_course_sum_my` b on a.id = b.courseid WHERE a.id != 1 '.$sql_add.' and a.visible=1 ORDER BY b.sumscore desc, b.courseid desc limit '.$page_offset.', '.$page_size;
					$courses = $DB -> get_records_sql($sql);
					break;
				/** End */
				default:	//默认排序
					$sql = 'select id,fullname,summary FROM mdl_course a where id!=1 '.$sql_add.' and visible=1 order by sortorder limit '.$page_offset.', '.$page_size;
					$courses = $DB->get_records_sql($sql);
			}
//原代码：
//			if($sortkind==1){//最新
//				$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by timecreated desc;');
//			}
//			elseif($sortkind==2){//最热
//				$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by timecreated desc;');
//				//统计选课学生数,增加字段studnum
//				//$this->my_calculate_num_of_course($courses);
//				coursecat::my_calculate_num_of_course($courses);
//				//重新排序
//                $courses = $this->my_sortby_studnum($courses);
//			}
//			else{//默认按sortorder排序
//				$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by sortorder;');
//			}

		}
		elseif($dep==1){	//查询包括子类

			$subcategories=$DB->get_records_sql('select id from mdl_course_categories where parent='.$categoryid.';');
			$categoryids= $categoryid;
			foreach($subcategories as $subcategory){
				$categoryids .=','.$subcategory->id;
			}
			//查询记录总数
			$sql = 'select count(distinct a.id) as total from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id '.$sql_add;
			$record_count = $DB -> get_record_sql($sql);
//			echo 'select distinct count(a.id) as total from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id ';
//			print_r($record_count);
//			exit;
			switch($sortkind){
				case 1:
					$sql = 'select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id '.$sql_add.' order by timecreated desc limit '.$page_offset.', '.$page_size;
					$courses = $DB->get_records_sql($sql);
					break;
				case 2:
				case 3:
				case 4:
					$sql = 'select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b join mdl_course_order_my c ON a.id = c.courseid where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id '.$sql_add.' order by c.'.$order_by_my[$sortkind].' desc limit '.$page_offset.', '.$page_size;
					$courses = $DB->get_records_sql($sql);
					break;
				/** Start 增加好评榜  20160303 毛英东 */
				case 5:	//好评榜
					$sql = 'select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b join mdl_score_course_sum_my c ON a.id = c.courseid where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id '.$sql_add.' order by c.sumscore desc, c.courseid desc limit '.$page_offset.', '.$page_size;
					$courses = $DB -> get_records_sql($sql);
					break;
				/** End */
				default:
					$sql = 'select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id '.$sql_add.' order by a.sortorder limit '.$page_offset.', '.$page_size;
					$courses = $DB->get_records_sql($sql);
			}

			//原代码：
//			if($sortkind==1){//最新
//				$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by timecreated desc;');
//			}
//			elseif($sortkind==2){//最热
//				$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by timecreated desc;');
//				//统计选课学生数,增加字段studnum
//				//$this->my_calculate_num_of_course($courses);
//				coursecat::my_calculate_num_of_course($courses);
//				//重新排序
//                $courses = $this->my_sortby_studnum($courses);
//			}
//			else{//默认按sortorder排序
//				$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by a.sortorder;');
//			}

		}
		else{//只查询子类
			//查询记录总数
			$sql = 'select count(a.id) as total from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id '.$sql_add;
			$record_count = $DB -> get_record_sql($sql);
			switch($sortkind){
				case 1:
					$sql = 'select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id '.$sql_add.' order by timecreated desc limit '.$page_offset.', '.$page_size;
					$courses = $DB->get_records_sql($sql);
					break;
				case 2:
				case 3:
				case 4:
					$sql = 'select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b join mdl_course_order_my c ON a.id = c.courseid where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id '.$sql_add.' order by c.'.$order_by_my[$sortkind].' desc limit '.$page_offset.', '.$page_size;
					$courses = $DB->get_records_sql($sql);
					break;
				/** Start 增加好评榜  20160303 毛英东 */
				case 5:	//好评榜
					$sql = 'select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b join mdl_score_course_sum_my c ON a.id = c.courseid where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id '.$sql_add.' order by c.sumscore desc, c.courseid desc limit '.$page_offset.', '.$page_size;
					$courses = $DB -> get_records_sql($sql);
					break;
				/** End */
				default:
					$sql = 'select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id '.$sql_add.' order by a.sortorder limit '.$page_offset.', '.$page_size;
					$courses = $DB->get_records_sql($sql);
			}
			//原代码：
//			if($sortkind==1){//最新
//				$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by timecreated desc;');
//			}
//			elseif($sortkind==2){//最热
//				$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by timecreated desc;');
//				//统计选课学生数,增加字段studnum
//				//$this->my_calculate_num_of_course($courses);
//				coursecat::my_calculate_num_of_course($courses);
//				//重新排序
//                $courses = $this->my_sortby_studnum($courses);
//			}
//			else{//默认按sortorder排序
//				$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by a.sortorder;');
//
//			}
			//$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and ;');
		}
		/** End */

		//统计选课学生数
		coursecat::my_calculate_num_of_course($courses);

		//课程 网格式输出
		$output = $this->my_course_category_dispaly_by_ceil($courses);
		//课程 列表式输出
		$output .= $this->my_course_category_dispaly_by_list($courses);

		/* 列表下面的换页按钮 */
		$output .= $this->echo_end($current_page, ceil(($record_count->total)/$page_size),$dep1categoryid,$dep2categoryid,$sortkind);
		return $output;
	}
	/** end 输出课程列表 */

	/** start 课程按照网格输出 */
	protected function  my_course_category_dispaly_by_ceil($courses){
		$output =  '<!--课程列表 网格-->
					<div class="course-list">';
		$n = 0;
		foreach ($courses as $course) {
			$n++;
			if ($n % 5 == 1) {//行首
				$output .= '<!--第一行-->
							<div class="course first">
								<a href="view.php?id='.$course->id.'"><img width="220" height="150" alt="'.$course->fullname.'" '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).'></a>
								<div class="box">
									<a  class="title" href="view.php?id='.$course->id.'"><p>'.$course->fullname.'</p></a>
									<p class="info">'.mb_substr(strip_tags($course->summary),0,25,'utf-8').'</p>
									<p class="num"><span class="glyphicon glyphicon-user"></span>'.$course->studnum.'人学习</p>
								</div>
							</div>';
			}else{
				$output .= '<div class="course">
								<a href="view.php?id='.$course->id.'"><img width="220" height="150" alt="'.$course->fullname.'" '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).'></a>
								<div class="box">
									<a  class="title" href="view.php?id='.$course->id.'"><p>'.$course->fullname.'</p></a>
									<p class="info">'.mb_substr(strip_tags($course->summary),0,25,'utf-8').'</p>
									<p class="num"><span class="glyphicon glyphicon-user"></span>'.$course->studnum.'人学习</p>
								</div>
							</div>';
				if($n % 5 == 0){
					$output .= '<div style="clear: both;"></div>
								<!--第一行 end-->';
				}
			}
		}
		$output .= '</div>
					<!--课程列表 网格 end-->';
		return $output;
	}
	/** end 课程按照网格输出 */

	/** Start 输出课程分类 */
	protected function get_course_category($courseID){
		global $CFG;
		global $DB;
		$sql = 'select cc.name,cc.path from mdl_course_categories cc
					where cc.id in (
					select cl.mdl_course_categories_id from mdl_course_link_categories cl
					where cl.mdl_course_id = '.$courseID.'
					)';
		$courseCategorys = $DB->get_records_sql($sql);
		$courseCategory = new stdClass();
		$courseCategory->name = '';
		$courseCategory->path = '';
		foreach($courseCategorys as $courseCategory){
			$courseCategory->name = $courseCategory->name;
			$Path = $courseCategory->path;
			$Path = substr($Path,1,(strlen($Path)-1));
			$pathArray = explode('/',$Path);
			if(count($pathArray)==2){
				$courseCategory->path = $CFG->wwwroot.'/course/index.php?dep1categoryid='.$pathArray[0].'&dep2categoryid='.$pathArray[1];
			}else{
				$courseCategory->path = $CFG->wwwroot.'/course/index.php?dep1categoryid='.$pathArray[0];
			}
			break;//只显示一个分类
		}
		return $courseCategory;
	}
	/** end 输出课程分类 */


	/** start 课程按照列表输出 */
	protected function  my_course_category_dispaly_by_list($courses){

		$output =  '<!--课程列表 行-->
					<div class="course-list-th">';

		foreach ($courses as $course) {
			$courseCategory = $this->get_course_category($course->id);
			$output .= '<div class="course">
							<a href="view.php?id='.$course->id.'"><img width="220" height="150" alt="'.$course->fullname.'" '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).'></a>
							<div class="box">
								<a class="title" href="view.php?id='.$course->id.'"><p>'.$course->fullname.'</p></a>
								<a class="type" href="'.$courseCategory->path.'">'.$courseCategory->name.'</a>
								<div style="clear: both;"></div>
								<p class="info-title">课程简介：</p>
								<p class="info">'.mb_substr(strip_tags($course->summary),0,290,'utf-8').'</p>
								<p class="num"><span class="glyphicon glyphicon-user"></span>'.$course->studnum.'人学习</p>
							</div>
							<div class="division" ></div>
						</div>';
		}
		$output .= '</div>
					<!--课程列表 行 end-->';
		return $output;
	}
	/** end 课程按照列表输出 */

	//输出课程列表
	protected function my_course_category_display2($categoryid=null,$dep=null,$sortkind=null){
		global $DB;
		/** Start 分页  20160304 毛英东 */
		if(isset($_GET['page'])){
			$current_page = intval($_GET['page']);
			$current_page = $current_page > 0 ? $current_page : 1;
		}else{
			$current_page = 1;
		}
		$page_size = 16;	//每页记录条数
		$page_offset = ($current_page - 1) * $page_size;	//查询记录起始位置
		/** End */

		/** Start 输出排序方式，增加日、周、月热门排序  20160301 毛英东 */
		//查询上次更新浏览次数的时间
		$update_time = $DB -> get_record_sql('select timeorder from `mdl_course_order_my` limit 1');
		if((time()-3600*24) > $update_time->timeorder){	//更新时间到了
			//获取所有课程ID
			$courses_id_my = $DB -> get_records_sql('select id from mdl_course where id != 1');
			foreach($courses_id_my as $course_id_i){
				//更新总浏览数
				$DB -> execute("update mdl_course_order_my set timeorder = ".time().", count_sum = (SELECT count(*)  FROM `mdl_logstore_standard_log` WHERE target = 'course' and action = 'viewed' and courseid = $course_id_i->id) where courseid=$course_id_i->id");
				//更新月浏览数
				$DB -> execute("update mdl_course_order_my set timeorder = ".time().", count_month = (SELECT count(*)  FROM `mdl_logstore_standard_log` WHERE target = 'course' and action = 'viewed' and courseid = $course_id_i->id and timecreated > ". (time() - 3600*24*30) .") where courseid=$course_id_i->id");
				//更新周浏览数
				$DB -> execute("update mdl_course_order_my set timeorder = ".time().", count_week = (SELECT count(*)  FROM `mdl_logstore_standard_log` WHERE target = 'course' and action = 'viewed' and courseid = $course_id_i->id and timecreated > ". (time() - 3600*24*7) .") where courseid=$course_id_i->id");
			}
		}//更新浏览次数结束

		$order_by_my = array(2 => 'count_week', 3 => 'count_month', 4 => 'count_sum');	//热门排序方式
		if($categoryid==null){//全部
			//查询记录总数
			$record_count = $DB -> get_record_sql("select count(*) as total FROM mdl_course where id!=1 and visible=1");
			//是否排序
			//SELECT c.id, c.fullname FROM `mdl_course` c join `mdl_course_order_my` b on c.id = b.courseid WHERE c.id != 1 ORDER BY b.`count_week` desc limit 50
			switch($sortkind){
				case 1:		//按最新排序
					$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by timecreated desc limit '.$page_offset.', '.$page_size.';');
					break;
				case 2:	//按周热门排序
				case 3:	//按月热门排序
				case 4:	//按总热门排序
					$courses = $DB -> get_records_sql('SELECT a.id, a.fullname, a.summary FROM `mdl_course` a join `mdl_course_order_my` b on a.id = b.courseid WHERE a.id != 1 and a.visible=1 ORDER BY b.`'.$order_by_my[$sortkind].'` desc limit '.$page_offset.', '.$page_size.';');
					break;
				/** Start 增加好评榜  20160303 毛英东 */
				case 5:	//好评榜
					$courses = $DB -> get_records_sql('SELECT a.id, a.fullname, a.summary FROM `mdl_course` a join `mdl_score_course_sum_my` b on a.id = b.courseid WHERE a.id != 1 and a.visible=1 ORDER BY b.sumscore desc, b.courseid desc limit '.$page_offset.', '.$page_size.';');
					break;
				/** End */
				default:	//默认排序
					$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by sortorder limit '.$page_offset.', '.$page_size.';');
			}
//原代码：
//			if($sortkind==1){//最新
//				$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by timecreated desc;');
//			}
//			elseif($sortkind==2){//最热
//				$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by timecreated desc;');
//				//统计选课学生数,增加字段studnum
//				//$this->my_calculate_num_of_course($courses);
//				coursecat::my_calculate_num_of_course($courses);
//				//重新排序
//                $courses = $this->my_sortby_studnum($courses);
//			}
//			else{//默认按sortorder排序
//				$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by sortorder;');
//			}

		}
		elseif($dep==1){	//查询包括子类

			$subcategories=$DB->get_records_sql('select id from mdl_course_categories where parent='.$categoryid.';');
			$categoryids= $categoryid;
			foreach($subcategories as $subcategory){
				$categoryids .=','.$subcategory->id;
			}
			//查询记录总数
			$record_count = $DB -> get_record_sql('select count(distinct a.id) as total from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id ');
//			echo 'select distinct count(a.id) as total from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id ';
//			print_r($record_count);
//			exit;
			switch($sortkind){
				case 1:
					$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by timecreated desc limit '.$page_offset.', '.$page_size.';');
					break;
				case 2:
				case 3:
				case 4:
					$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b join mdl_course_order_my c ON a.id = c.courseid where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by c.'.$order_by_my[$sortkind].' desc limit '.$page_offset.', '.$page_size.';');
					break;
				/** Start 增加好评榜  20160303 毛英东 */
				case 5:	//好评榜
					$courses = $DB -> get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b join mdl_score_course_sum_my c ON a.id = c.courseid where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by c.sumscore desc, c.courseid desc limit '.$page_offset.', '.$page_size.';');
					break;
				/** End */
				default:
					$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by a.sortorder limit '.$page_offset.', '.$page_size.';');
			}

			//原代码：
//			if($sortkind==1){//最新
//				$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by timecreated desc;');
//			}
//			elseif($sortkind==2){//最热
//				$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by timecreated desc;');
//				//统计选课学生数,增加字段studnum
//				//$this->my_calculate_num_of_course($courses);
//				coursecat::my_calculate_num_of_course($courses);
//				//重新排序
//                $courses = $this->my_sortby_studnum($courses);
//			}
//			else{//默认按sortorder排序
//				$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by a.sortorder;');
//			}

		}
		else{//只查询子类
			//查询记录总数
			$record_count = $DB -> get_record_sql('select count(a.id) as total from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id ');
			switch($sortkind){
				case 1:
					$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by timecreated desc limit '.$page_offset.', '.$page_size.';');
					break;
				case 2:
				case 3:
				case 4:
					$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b join mdl_course_order_my c ON a.id = c.courseid where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by c.'.$order_by_my[$sortkind].' desc limit '.$page_offset.', '.$page_size.';');
					break;
				/** Start 增加好评榜  20160303 毛英东 */
				case 5:	//好评榜
					$courses = $DB -> get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b join mdl_score_course_sum_my c ON a.id = c.courseid where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by c.sumscore desc, c.courseid desc limit '.$page_offset.', '.$page_size.';');
					break;
				/** End */
				default:
					$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by a.sortorder limit '.$page_offset.', '.$page_size.';');
			}
			//原代码：
//			if($sortkind==1){//最新
//				$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by timecreated desc;');
//			}
//			elseif($sortkind==2){//最热
//				$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by timecreated desc;');
//				//统计选课学生数,增加字段studnum
//				//$this->my_calculate_num_of_course($courses);
//				coursecat::my_calculate_num_of_course($courses);
//				//重新排序
//                $courses = $this->my_sortby_studnum($courses);
//			}
//			else{//默认按sortorder排序
//				$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by a.sortorder;');
//
//			}
			//$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and ;');
		}
		/** End */
		$output = '	<div class="course-list">
						<!--课程列表-->
						<div class="js-course-lists">
							<ul>';
		$n=0;
		//统计选课学生数
		//$this->my_calculate_num_of_course($courses);
		coursecat::my_calculate_num_of_course($courses);
		foreach ($courses as $course) {
			$n++;
			if($n%4==1){//行首
				$output .= '<li class="course-one linefrist">
								<a href="view.php?id='.$course->id.'" target="_self">
									<div class="course-list-img">
										<img width="240" height="135" alt="'.$course->fullname.'" '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).'>
									</div>
									<h5><span>'.$course->fullname.'</span></h5>
									<div class="tips">
										<p class="text-ellipsis">'.mb_substr(strip_tags($course->summary),0,25,'utf-8').'</p>
										  
										<span class="l ml20"> '.$course->studnum.'人学习</span>
									</div>
									<b class="follow-label">跟我学</b>
								</a>
							</li>';
			}
			else{
				$output .= '<li class="course-one">
									<a href="view.php?id='.$course->id.'" target="_self">
										<div class="course-list-img">
											<img width="240" height="135" alt="'.$course->fullname.'" '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).'>
										
										</div>
										<h5><span>'.$course->fullname.'</span></h5>
										<div class="tips">
											<p class="text-ellipsis">'.mb_substr(strip_tags($course->summary),0,25,'utf-8').'</p>
											  
											<span class="l ml20">'.$course->studnum.'人学习</span>
										</div>
										<!--span class="time-label">1小时 | 高级</span-->
										<b class="follow-label">跟我学</b>
									</a>
								</li>';
			}
		}		
		$output .= '
							</ul>
						</div>
						<!--课程列表end-->
					</div>										
			';
		/* 列表下面的换页按钮 */
		$output .= '<!--换页按钮+-->
						<!--
						<div class="page">
							<span class="disabled_page">首页</span>
							<span class="disabled_page">上一页</span>
							<a href="javascript:void(0)" class="active">1</a>
							<span class="disabled_page">下一页</span>
							<span class="disabled_page">尾页</span>
						</div>
						-->
						<!--换页按钮end-->
						'.$this->echo_end2($current_page, ceil(($record_count->total)/$page_size)).'
						';
		return $output;
	}

	//输出主页的课程列表
	protected function my_course_category_display_indexpage($sortkind=null){
		global $DB;
		$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by timecreated desc LIMIT 0,12');
		$output = '';
		$n=0;
		//统计选课学生数
		//$this->my_calculate_num_of_course($courses);
		coursecat::my_calculate_num_of_course($courses);
		foreach ($courses as $course) {
			$n++;
			if($n%4==1){//行首

				$output .= '<li class="course-one linefrist">
								<a href="course/view.php?id='.$course->id.'" target="_self">
									<div class="course-list-img">
										<img width="240" height="135" alt="'.$course->fullname.'" '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).'>
									</div>
									<h5><span>'.$course->fullname.'</span></h5>
									<div class="tips">
										<p class="text-ellipsis">'.mb_substr(strip_tags($course->summary),0,25,'utf-8').'</p>
										  
										<span class="l ml20"> '.$course->studnum.'人学习</span>
									</div>
									<b class="follow-label">跟我学</b>
								</a>
							</li>';
			}
			else{
				$output .= '<li class="course-one">
									<a href="course/view.php?id='.$course->id.'" target="_self">
										<div class="course-list-img">
											<img width="240" height="135" alt="'.$course->fullname.'" '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).'>
										
										</div>
										<h5><span>'.$course->fullname.'</span></h5>
										<div class="tips">
											<p class="text-ellipsis">'.mb_substr(strip_tags($course->summary),0,25,'utf-8').'</p>
											  
											<span class="l ml20">'.$course->studnum.'人学习</span>
										</div>
										<!--span class="time-label">1小时 | 高级</span-->
										<b class="follow-label">跟我学</b>
									</a>
								</li>';
			}
		}		
		echo $output;
		return null;
	}

	//根据选课人数重新排序
	public  function my_sortby_studnum($courses){
		if(count($courses)==0){
			return $courses;
		}
		$myorder =array();
		$num=0;
		$studnums = array();
		//取id studnum
		foreach($courses as $course){
            $myorder[$num][0]=$course->id;
            $myorder[$num][1]=$course->studnum;
			$studnums[] = $course->studnum;
			$num++;
		}
		//二维数组排序
		array_multisort($studnums, SORT_DESC, $myorder);
		//重新生成排好序的课程数组
		$mycourses= array();
		foreach($myorder as $a){
			//$mycourses[] = $courses[$a[0]];
			Array_push($mycourses, $courses[$a[0]]);
		}
		return $mycourses;
	}

	/*以下3个函数已经放入coursecatlib内
	//根据课程集查询选课人数，为$courses添加一个studnum字段表示学生数量
	public function my_calculate_num_of_course($courses){
		if(count($courses)==0){
			return $courses;
		}
		//判断是否已查询过学生数量
		foreach($courses as $course){
			if(!isset($course->studnum)){
				break;
			}
			else{
				return $courses;
			}
		}
		$sql_courses='';
		foreach($courses as $course){
			$sql_courses .= $course->id.',';
			$course->studnum = 0;//添加选课的学生数字段
			//array_push($course, array("a"=>"red"));
		}
		$sql_courses = substr($sql_courses,0,strlen($sql_courses)-1); 
		global $DB;
		$users = $DB->get_records_sql('select a.id,a.userid,b.courseid from mdl_user_enrolments a join mdl_enrol b where b.courseid in ('.$sql_courses.') and b.id=a.enrolid;');
		foreach($courses as $course){
			foreach($users as $user){
				if($course->id==$user->courseid){
					$course->studnum++;//选了这门课的，数量+1
				}
			}
		}
		return $courses;
	}
	//获取summary中的图片
	public function my_get_course_formatted_summary_pix($course) {
        global $CFG;
        require_once($CFG->libdir. '/filelib.php');
        
        //$options = (array)$options;
        $context = context_course::instance($course->id);
        //if (!isset($options['context'])) {
            // TODO see MDL-38521
            // option 1 (current), page context - no code required
            // option 2, system context
            // $options['context'] = context_system::instance();
            // option 3, course context:
            // $options['context'] = $context;
            // option 4, course category context:
            // $options['context'] = $context->get_parent_context();
        // }
		//$summary即摘要
		//echo $course->summary;exit;
		$my_course_summary_pix=$this->my_search_summary_pix($course->summary);
        $summary = file_rewrite_pluginfile_urls($my_course_summary_pix, 'pluginfile.php', $context->id, 'course', 'summary', null);
        $summary = format_text($summary, $course->summaryformat,null, $course->id);
        // if (!empty($this->searchcriteria['search'])) {
            // $summary = highlight($this->searchcriteria['search'], $summary);
        // }

        return $summary;
    }
	
	public function my_search_summary_pix($summary){
		$pix_start='src="';
		$pix_end='"';	
		$start_position=strpos($summary,$pix_start);
		if($start_position==false){return '';}
		$end_position=strpos($summary,$pix_end,$start_position+5);
		$my_pix=substr($summary,$start_position,$end_position-$start_position+1);
		return $my_pix;
	}
	*/
	
	/** start 输出第一层分类 */
	protected function my_course_category_subfirst($selectedid){
		global $DB;
		$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=1 and visible=1 ORDER BY sortorder;');
		$current_category = '全部课程';
		$output = '<div class="coursetypebox">
						<div class="main">
							<a href="#" onclick="sel4();">全部</a>';
		$output_temp = '<div class="coursetypebox-more">
							<div class="main">';//方向（顶级分类下拉显示）
		$i = 0;
		foreach ($categorys as $category) {
			if($i<6){
				$output .='<a href="#" data-ct="fe" onclick="sel('.$category->id.');">'.$category->name.'</a>';
			}
			$i++;
			$output_temp .= '<a href="#" data-ct="fe" onclick="sel('.$category->id.');">'.$category->name.'</a>';
			if($selectedid != null && $category->id==$selectedid){
				$current_category = $category->name;
			}
		}
		$output_temp .= '</div>
						</div>';

		$output .= '	<a id="more-type" class="more">更多<span class="span_box"><span class="glyphicon glyphicon-chevron-down"></span><span class="glyphicon glyphicon-chevron-down"></span></a>
						</div>
					</div>';
		$output .= $output_temp;

		$result = new stdClass();
		$result->output = $output;
		$result->current_category = $current_category;

		return $result;
	}
	/** end 输出第一层分类 */

	/** start 输出第二层分类 */
	protected function my_course_category_subsecond($parentcategoryid=null,$mysubcategoryid=null){

		$output = '<!--课程分类-->
						<div class="types">
							<a class="title">分类：</a>
							<div class="r-abox">';

		if($mysubcategoryid==null){
			$output .= '<a href="#" class="active" onclick="sel('.$parentcategoryid.');">全部</a>';
			global $DB;
			if($parentcategoryid==null){
				$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=2 and visible=1 ORDER BY sortorder;');
				foreach ($categorys as $category) {
					$output .='<span>l</span><a href="#" onclick="sel2('.$category->id.');">'.$category->name.'</a>';
				}
			}
			else{
				$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=2 and visible=1 and parent='.$parentcategoryid.' ORDER BY sortorder;');
				foreach ($categorys as $category) {
					$output .='<span>l</span><a href="#" onclick="sel3('.$parentcategoryid.','.$category->id.');" >'.$category->name.'</a>';
				}
			}
		}
		else{//设置选中
			$output .= '<a href="#" onclick="sel('.$parentcategoryid.');">全部</a>';
			global $DB;
			if($parentcategoryid==null){
				$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=2 and visible=1 ORDER BY sortorder;');
				foreach ($categorys as $category) {
					if($mysubcategoryid==$category->id){
						$output .= '<span>l</span><a href="#" class="active" onclick="sel2('.$category->id.');">'.$category->name.'</a>';
						continue;
					}
					$output .= '<span>l</span><a href="#" onclick="sel2('.$category->id.');">'.$category->name.'</a>';
				}
			}
			else{
				$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=2 and visible=1 and parent='.$parentcategoryid.' ORDER BY sortorder;');
				foreach ($categorys as $category) {
					if($mysubcategoryid==$category->id){
						$output .= '<span>l</span><a href="#" class="active" onclick="sel3('.$parentcategoryid.','.$category->id.');">'.$category->name.'</a>';
						continue;
					}
					$output .= '<span>l</span><a href="#" onclick="sel3('.$parentcategoryid.','.$category->id.');">'.$category->name.'</a>';
				}
			}
		}
		$output .= '   </div>
   					<div style="clear: both"></div>
				   </div>
				   <!--课程分类end-->';
		return $output;
	}
	/** end 输出第二层分类 */

	/** start 输出排序方式，增加日、周、月热门排序 */
	protected function my_print_sortkind($sortkind){
		$output = '<div class="sort">';
		//最新
		if($sortkind == 1){
			$output .= '<a href="javascript:void(0)" class="first active" onclick="sel5(1);" >最新</a>';
		}else{
			$output .= '<a href="javascript:void(0)" class="first "  onclick="sel5(1);" >最新</a>';
		}
		//日热门
		if($sortkind == 2){
			$output .= '<a href="javascript:void(0)" class="active" onclick="sel5(2);">周热门</a>';
		}else{
			$output .= '<a href="javascript:void(0)" onclick="sel5(2);">周热门</a>';
		}
		//周热门
		if($sortkind == 3){
			$output .= '<a href="javascript:void(0)" class="active" onclick="sel5(3);">月热门</a>';
		}else{
			$output .= '<a href="javascript:void(0)" onclick="sel5(3);">月热门</a>';
		}
		//月热门
		if($sortkind == 4){
			$output .= '<a href="javascript:void(0)" class="active" onclick="sel5(4);">总热门</a>';
		}else{
			$output .= '<a href="javascript:void(0)" onclick="sel5(4);">总热门</a>';
		}
		//好评榜
		if($sortkind == 5){
			$output .= '<a href="javascript:void(0)" class="active" onclick="sel5(5);">好评榜</a>';
		}else{
			$output .= '<a href="javascript:void(0)" onclick="sel5(5);">好评榜</a>';
		}
		$output .= '<a id="list_btn_th" class="list_btn active"><span class="glyphicon glyphicon-th"></span></a>
					<a id="list_btn_tr" class="list_btn"><span class="glyphicon glyphicon-th-list"></span></a>
				</div>';

		return $output;
	}
	/** end 输出排序方式，增加日、周、月热门排序 */

	/** start 界面修改前 */
	//输出第一层分类
	protected function my_course_category_subfirst2($selectedid){
		global $DB;
		$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=1 and visible=1 ORDER BY sortorder;');
		if($selectedid==null){//<li class="course-nav-item on">有on
			$output = '<!--课程方向-->
							<div class="course-nav-row clearfix">
								<span class="hd l">方向：</span>
								<div class="bd">
									<ul class="direction" id="direction">
										<li class="course-nav-item on">
											<a href="#">全部</a>
										</li>';
			foreach ($categorys as $category) {
				$output .='<li class="course-nav-item">
								<a href="#" data-ct="fe" onclick="sel('.$category->id.');">'.$category->name.'</a>
							</li>';
			}
		}
		else{//设置选中的id,<li class="course-nav-item">没有on
			$output = '<!--课程方向-->
						<div class="course-nav-row clearfix">
							<span class="hd l">方向：</span>
							<div class="bd">
								<ul class="direction" id="direction">
									<li class="course-nav-item">
										<a href="#" onclick="sel4();">全部</a>
									</li>';
			foreach ($categorys as $category) {
				if($category->id==$selectedid){//设置on
					$output .='<li class="course-nav-item on">
											<a href="#" data-ct="fe" onclick="sel('.$category->id.');">'.$category->name.'</a>
										</li>';
					continue;
				}
				$output .='<li class="course-nav-item">
								<a href="#" data-ct="fe" onclick="sel('.$category->id.');">'.$category->name.'</a>
							</li>';
			}
		}

		$output .='
								</ul>
							</div>
						</div>
						<!--课程方向end-->';
		return $output;
	}
	//输出第二层分类
	protected function my_course_category_subsecond2($parentcategoryid=null,$mysubcategoryid=null){
		if($mysubcategoryid==null){
			$output = '<!--课程分类-->
							<div class="course-nav-row clearfix">
								<span class="hd l">分类：</span>
								<div class="bd" >
									<ul class="kinds" id="subsecond">
										<li class="course-nav-item on">
											<a href="#"  onclick="sel('.$parentcategoryid.');">全部</a>
										</li>';
			global $DB;
			if($parentcategoryid==null){
				$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=2 and visible=1 ORDER BY sortorder;');
				foreach ($categorys as $category) {
				$output .='<li class="course-nav-item ">
								<a href="#" data-id=7 data-ct=fe onclick="sel2('.$category->id.');">'.$category->name.'</a>
							</li>';
				}	
			}
			else{
				$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=2 and visible=1 and parent='.$parentcategoryid.' ORDER BY sortorder;');
				foreach ($categorys as $category) {
				$output .='<li class="course-nav-item ">
								<a href="#" data-id=7 data-ct=fe onclick="sel3('.$parentcategoryid.','.$category->id.');">'.$category->name.'</a>
							</li>';
				}	
			}
		}
		else{//设置选中
			//<li class="course-nav-item">没有on
			$output = '<!--课程分类-->
							<div class="course-nav-row clearfix">
								<span class="hd l">分类：</span>
								<div class="bd" >
									<ul class="kinds" id="subsecond">
										<li class="course-nav-item">
											<a href="#"  onclick="sel('.$parentcategoryid.');">全部</a>
										</li>';
			global $DB;
			if($parentcategoryid==null){
				$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=2 and visible=1 ORDER BY sortorder;');
				foreach ($categorys as $category) {
					if($mysubcategoryid==$category->id){
						$output .='<li class="course-nav-item on">
								<a href="#" data-id=7 data-ct=fe onclick="sel2('.$category->id.');">'.$category->name.'</a>
							</li>';
						continue;
					}
					$output .='<li class="course-nav-item ">
									<a href="#" data-id=7 data-ct=fe onclick="sel2('.$category->id.');">'.$category->name.'</a>
								</li>';
				}	
			}
			else{
				$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=2 and visible=1 and parent='.$parentcategoryid.' ORDER BY sortorder;');
				foreach ($categorys as $category) {
					if($mysubcategoryid==$category->id){
						$output .='<li class="course-nav-item on">
								<a href="#" data-id=7 data-ct=fe onclick="sel3('.$parentcategoryid.','.$category->id.');">'.$category->name.'</a>
							</li>';
						continue;
					}
					$output .='<li class="course-nav-item ">
									<a href="#" data-id=7 data-ct=fe onclick="sel3('.$parentcategoryid.','.$category->id.');">'.$category->name.'</a>
								</li>';
				}	
			}
		}
		$output .= '	
					</ul>
				</div>
			</div>
					<!--课程分类end-->';
		return $output;
	}
	/** end 界面修改前 */

/** ==== end 课程列表页====================================================================================  */


/** ==== start 网站首页====================================================================================  */
	/** start 输出网站首页最新课程板块 */
	protected function my_print_newcourse(){

		global $DB;
		global $CFG;
		global $USER;
		//如果当前角色只能查看部分课程
		$sql_add = "";
		if($USER->ava_course_flag_my){
			if($USER->ava_course_my){
				$sql_add = " and id in ($USER->ava_course_my)";
			}else{//没有可查看的课程
				$sql_add = " and id = -1 ";
			}
		}

		$sql = 'select id,fullname,summary FROM mdl_course where id!=1 and visible=1 '.$sql_add.'order by timecreated desc LIMIT 0,8';
		$courses = $DB->get_records_sql($sql);
		$output = '<!--最新课程板块-->
					<div class="newcourse">
						<h3>&nbsp;最新课程&nbsp;<span class="glyphicon glyphicon-tasks" style="top: 5px;"></span></h3>
						<div class="mainbanner">';
		$n=0;
		//统计选课学生数
		coursecat::my_calculate_num_of_course($courses);
		foreach ($courses as $course) {
			$n++;
			if($n%4==1){//行首

				$output .= '<!--课程 -->
							<div class="course first">
								<a href="course/view.php?id='.$course->id.'" target="_self"">
									<img '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).' alt="'.$course->fullname.'" />
									<div class="hidediv">
										<img src="'.$CFG->wwwroot.'/theme/more/img/play.png" />
									</div>
								</a>
								<div class="coursetips m-t">
									<a title="'.$course->fullname.'" class="coursename" href="course/view.php?id='.$course->id.'" target="_self"">'.$course->fullname.'</a>
									<a class="num"><span class="glyphicon glyphicon-user"></span>'.$course->studnum.'</a>
								</div>
							</div>
							<!--课程 end-->';
			}
			else{

				$output .= '<!--课程 -->
					<div class="course">
						<a href="course/view.php?id='.$course->id.'" target="_self"">
							<img '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).' alt="'.$course->fullname.'" />
							<div class="hidediv">
								<img src="'.$CFG->wwwroot.'/theme/more/img/play.png" />
							</div>
						</a>
						<div class="coursetips m-t">
							<a title="'.$course->fullname.'" class="coursename" href="course/view.php?id='.$course->id.'" target="_self"">'.$course->fullname.'</a>
							<a class="num"><span class="glyphicon glyphicon-user"></span>'.$course->studnum.'</a>
						</div>
					</div>
					<!--课程 end-->';

			}
		}

		$output .= '	</div>
						<div style="clear: both;"></div>
					</div>
					<!--最新课程板块 end-->';

		echo $output;
	}
	/** end 输出网站首页最新课程板块 */

	/** start 输出网站首页各课程板块 */
	protected function my_print_courseblocks(){
		global $DB;

		//所有顶级分类
		$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=1 and visible=1 ORDER BY sortorder;');

		$block_types = array('','polity','logistics','application');//板块样式
		$block_icons = array('glyphicon-screenshot','glyphicon-edit','glyphicon-briefcase','glyphicon-th');//板块图标
		$i = 0;
		foreach($categorys as $category){
			$courses  = $this->my_get_categoryCourse($category->id);//获取该分类课程
			switch($category->id){//根据板块的ID，筛选首页要输出板块
				case 2:
				case 3:
				case 4:
				case 11:
					$this->my_print_courseblock($courses,$category->name,$block_types[$i],$block_icons[$i]);//输出该分类课程
					$i++;
			}
		}

	}
	/** end 输出网站首页各课程板块 */

	/** start 输出单课程板块 */
	protected function my_print_courseblock($courses,$categoryname,$block_type,$block_icon){
		global $CFG;

		$output = '<!--军事业务板块-->
					<div class="military '.$block_type.'">
						<div class="mainbox">
							<h3>&nbsp;'.$categoryname.'&nbsp;<span class="glyphicon '.$block_icon.'" style="top: 4px;"></h3>
							<div class="mainbanner">';

		//如果该分类有课程
		if($courses){
			$n = 0;
			//统计选课学生数
			coursecat::my_calculate_num_of_course($courses);
			$course = current($courses);
			$output .= '<div class="l-course">
							<a href="course/view.php?id='.$course->id.'">
								<div class="course">
									<img '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).'/>
									<dd>
										<h6>'.$course->fullname.'</h6>
										<p>'.$course->studnum.'</p>
									</dd>
								</div>
							</a>
						</div>';
			$output .= '<div class="r-course">';
			foreach ($courses as $course) {
				$n++;
				if($n == 1){
					continue;
				}
				elseif(($n-1)%3==1) {//行首
					$output .= '<!--课程 -->
							<div class="course first">
								<a href="course/view.php?id='.$course->id.'">
									<img '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).'/>
									<div class="hidediv">
										<img src="'.$CFG->wwwroot.'/theme/more/img/play.png" />
									</div>
								</a>
								<div class="coursetips m-t">
									<a title="'.$course->fullname.'" class="coursename" href="course/view.php?id='.$course->id.'">'.$course->fullname.'</a>
									<a class="num"><span class="glyphicon glyphicon-user"></span>'.$course->studnum.'</a>
								</div>
							</div>
							<!--课程 end-->';
				}
				else{
					$output .= '<!--课程 -->
							<div class="course">
								<a href="course/view.php?id='.$course->id.'">
									<img '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).'/>
									<div class="hidediv">
										<img src="'.$CFG->wwwroot.'/theme/more/img/play.png" />
									</div>
								</a>
								<div class="coursetips m-t">
									<a title="'.$course->fullname.'" class="coursename" href="course/view.php?id='.$course->id.'">'.$course->fullname.'</a>
									<a class="num"><span class="glyphicon glyphicon-user"></span>'.$course->studnum.'</a>
								</div>
							</div>
							<!--课程 end-->';
				}

			}
		}

		$output .= '				</div>
								</div>
							</div>
						<div style="clear: both;"></div>
					</div>
					<!--军事业务板块 end-->';

		echo $output;
	}
	/** end 输出单课程板块 */

	/** start 获取各板块分类的课程 */
	protected function my_get_categoryCourse($categoryid){

		global $DB;
		global $USER;

		$page_size = 7;	//获取的记录条数
		$page_offset = 0;	//查询记录起始位置

		$subcategories=$DB->get_records_sql('select id from mdl_course_categories where parent='.$categoryid.';');
		$categoryids= $categoryid;
		foreach($subcategories as $subcategory){
			$categoryids .=','.$subcategory->id;
		}
		//如果当前角色只能查看部分课程
		$sql_add = "";
		if($USER->ava_course_flag_my){
			if($USER->ava_course_my){
				$sql_add = "and a.id in ($USER->ava_course_my)";
			}
			else{//没有可查看的课程
				$sql_add = " and a.id = -1 ";
			}
		}
		//查询记录总数
		$sql = 'select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id '.$sql_add.' order by a.sortorder limit '.$page_offset.', '.$page_size;
		$courses = $DB -> get_records_sql($sql);

		return $courses;
	}
	/** end  获取各板块分类的课程 */

	/** start 输出首页排行榜 */
	protected function my_print_rank(){

		global $DB;
		global $OUTPUT;

		$output = '<div class="military rank">
					<div class="mainbox">
						<h3>&nbsp;排行榜&nbsp;<span class="glyphicon glyphicon-stats" ></span></h3>
						<div class="mainbanner">';

		$rank_styles = array('n1','n2','n3','','','','','','','');//排名样式
		$study_rank_users = $DB -> get_records_sql('select r.id, u.*, r.complete_count, r.complete_time from mdl_user as u,   mdl_course_index_rank_my as r where r.userid=u.id order by r.complete_count desc, r.complete_time asc limit 0 , 10');
		$rank_i = 1;
		foreach($study_rank_users as $rank_user ){
			$str1 = $OUTPUT->user_picture($study_rank_users[$rank_user->id],array('link' => false,'visibletoscreenreaders' => false));
			$output .= '<!--学员-->
							<div class="learnnerbox">
								'.$str1.'
								<a href="#"><p>'.$rank_user->firstname.'</p></a>
								<p class="num '.$rank_styles[$rank_i-1].'">no.'.$rank_i .'</p>
							</div>
						<!--学员 end-->';
			$rank_i++;
		}
		$output .= '</div>
				</div>
				<!--排行榜 end-->';

		echo $output;
	}
	/** end 输出首页排行榜 */

	/** start 首页index */
	public function my_print_indexpage(){

		//$this->my_print_index_category();
		global $USER;
		/** Start 按照用户所属级别，筛选课程 */
		if($USER->id != 0 ){//如果不是访客身份，即已登录
			$this->set_userAllowCourse();
		}
		/** end 按照用户所属级别，筛选课程 */

		$this->my_print_newcourse();//最新课程板块
		$this->my_print_courseblocks();//输出课程各板块
		$this->my_print_rank();//输出排行榜

		/*
                echo '
                <div class="course-content">
                    <div class="course-tool-bar clearfix">
                        <div class="tool-left l">
                            <a href="javascript:void(0)" class="sort-item">新课程</a>

                        </div>

                        <div class="tool-right r">
                            <span class="tool-item tool-pager">
                                     <span class="pager-num">
                                    <!--<b class="pager-cur">1</b>/<em class="pager-total">24</em>-->
                                </span>
                            <!--<a href="javascript:void(0)" class="pager-action pager-prev hide-text disabled">上一页</a>

                            <a href="#" class="pager-action pager-next hide-text">下一页</a>-->
                            </span>
                        </div>
                    </div>

                    <div class="course-list">

                        <!--课程列表-->
                        <div class="js-course-lists">
                            <ul>';
                $this->my_course_category_display_indexpage();
                                echo '
                                <!--第三行end-->
                            </ul>
                        </div>
                        <!--课程列表end-->

                        <!--换页按钮+-->
                        <!--<div class="page">
                            <span class="disabled_page">首页</span>
                            <span class="disabled_page">上一页</span>
                            <a href="javascript:void(0)" class="active">1</a>
                            <span class="disabled_page">下一页</span>
                            <span class="disabled_page">尾页</span>
                        </div>-->
                        <!--换页按钮end-->
                    </div>
                </div>
                ';*/
	}
	/** end 首页index */

	/** Start 按照用户所属级别，筛选课程 */
	function set_userAllowCourse(){
		global $CFG;
		require_once($CFG->dirroot.'/course/course_classify/course_lib.php');
		add_userAllowCourse();
	}
	/** end 按照用户所属级别，筛选课程 */

	/** ===== end 网站首页===================================================================================  */

	/** START 输出上下页按钮等
	 * @param  $currentpage 当前页码
	 * @param  $totalpage  总页数
	 */
	protected function echo_end($currentpage,$totalpage,$dep1categoryid,$dep2categoryid,$sortkind){
		global $CFG;
		$param = '';
		if($dep1categoryid != null){
			$param .= '&dep1categoryid='.$dep1categoryid;
		}
		if($dep2categoryid != null){
			$param .= '&dep2categoryid='.$dep2categoryid;
		}
		if($sortkind != null){
			$param .= '&sortkind='.$sortkind;
		}

		$html = '<!--分页-->
				<div class="pagination">';
		$html .= '<a href="'.$CFG->wwwroot.'/course/index.php?page='.(($currentpage-1)<1?1:($currentpage-1)).$param.'"><span class="glyphicon glyphicon-chevron-left"></span></a>';
		$html .= $this->echo_end_pageList($totalpage,$currentpage,$param);
		$html .= '<a href="'.$CFG->wwwroot.'/course/index.php?page='.(($currentpage+1)>$totalpage?$totalpage:($currentpage+1)).$param.'"><span class="glyphicon glyphicon-chevron-right"></span></a>';
		$html.= '</div>
				<!--分页 end-->';	// end div.page
		return $html;
	}

	/** 输出页码 */
	public function echo_end_pageList($count_page,$current_page,$param)
	{
		global $CFG;
		/** Start 设置评论数的显示页码（只显示5页） */
		$numstart = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page - 2):1):($count_page - 4)):1;
		$numend = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page + 2):5):($count_page)):$count_page;
		/** End 设置评论数的显示页码（只显示5页）*/
		$output = '';
		for($num = $numstart; $num <= $numend; $num ++) {
			if ($num == $current_page) {
				//  修改当前页样式标示
				$output .= '<a class="active" href="'.$CFG->wwwroot.'/course/index.php?page='.$num.$param.'">' . $num . '</a>';
			} else {
				$output .= '<a href="'.$CFG->wwwroot.'/course/index.php?page='.$num.$param.'">' . $num . '</a>';
			}
		}
		return $output;
	}

	/** START 输出上下页按钮等
	 * @param  $currentpage 当前页码
	 * @param  $totalpage  总页数
	 */
	protected function echo_end2($currentpage,$totalpage){

		//页尾
		$html = '<div class="page">';
		//$html .= "第$currentpage 页， 共 $totalpage 页";
		$html .= '<input type="hidden" value="'.$currentpage.'" id="hidden_current_page">';
		$html .= '<input type="hidden" value="'.$totalpage.'" id="hidden_total_page">';
		$html.= '</div>';	// end div.page
		return $html;
	}
	
	protected function my_print_index_category(){
		echo '<div class="catagory">
			<div class="catagory-item" style="">
				<a href="'.$CFG->wwwroot.'/moodle/course/index.php?dep1categoryid=2" class="catagory-title">
					<span class="catagory-icon">            
            			<img src="../moodle/theme/more/pix/indexpage/Home_Catagory_1.png" width="72" height="75">
        			</span>
					<h3>军事业务</h3>
				</a>

				'.$this->my_select_coursecategory(2).'
			</div>

			<div class="catagory-item" style="">
				<a href="'.$CFG->wwwroot.'/moodle/course/index.php?dep1categoryid=3" class="catagory-title">
					<span class="catagory-icon">            
            			<img src="../moodle/theme/more/pix/indexpage/Home_Catagory_2.png" width="72" height="75">
        			</span>
					<h3>政治工作</h3>
				</a>

				'.$this->my_select_coursecategory(3).'
			</div>

			<div class="catagory-item" style="">
				<a href="'.$CFG->wwwroot.'/moodle/course/index.php?dep1categoryid=4" class="catagory-title">
					<span class="catagory-icon">       
            			<img src="../moodle/theme/more/pix/indexpage/Home_Catagory_3.png" width="72" height="75">
        			</span>
					<h3>后勤保障</h3>
				</a>

				'.$this->my_select_coursecategory(4).'
			</div>
			<div class="catagory-item" style="margin-right:0;">
				<a href="'.$CFG->wwwroot.'/moodle/course/index.php?dep1categoryid=11" class="catagory-title">
					<span class="catagory-icon">         
            			<img src="../moodle/theme/more/pix/indexpage/Home_Catagory_4.png" width="72" height="75">
       		 		</span>
					<h3>综合应用</h3>
				</a>
				'.$this->my_select_coursecategory(11).'
			</div>

			<div class="clr"></div>
		</div>';
	}
	
	protected function my_select_coursecategory($parentcategoryid){
		global $DB;
		global $CFG;
		$categorys=$DB->get_records_sql('select id,name from mdl_course_categories where visible=1 and parent ='.$parentcategoryid);
		$output = '<div class="catagory-list">';
		foreach($categorys as $category){
			$output .= '<a href="'.$CFG->wwwroot.'/course/index.php?dep1categoryid='.$parentcategoryid.'&dep2categoryid='.$category->id.'">'.$category->name.'</a>';
		}
		$output .= '</div>';
		return $output;
	}

	/** Start 课程搜索结果页 xdw 20160530
	 *@param  $page 当前显示页
	 *@param $perpage 每页显示记录数
	 */
	public function my_course_searchresult($search,$page,$perpage){
		global $CFG;
		global $DB;
		global $USER;
		/** Start 按照用户所属级别，筛选课程 */
		if($USER->id != 0 ){//如果不是访客身份，即已登录
			$this->set_userAllowCourse();
		}
		/** end 按照用户所属级别，筛选课程 */
		//如果当前角色只能查看部分课程
		$sql_add = "";
		if($USER->ava_course_flag_my){
			if($USER->ava_course_my){
				$sql_add = "and c.id in ($USER->ava_course_my)";
			}
			else{//没有可查看的课程
				$sql_add = " and c.id = -1 ";
			}
		}

		//获取课程信息
		$index = ($page-1)*$perpage;
		$sql = "SELECT * FROM mdl_course c WHERE c.fullname LIKE '%$search%' and c.category != 0 $sql_add ORDER BY c.timecreated DESC LIMIT $index,$perpage";
		$courses = $DB->get_records_sql($sql);
		$sql = "SELECT count(1) as `count` FROM mdl_course c WHERE c.fullname LIKE '%$search%' and c.category != 0 ".$sql_add;
		$coursescount = $DB->get_record_sql($sql);
		if($coursescount->count==0){
			return '<div style="margin:0 auto;text-align:center;"><div style="margin-top: 50px;font-size: 24px;">暂无相关课程！</div></div>';
		}

		$output = '
				<body>
					<div class="main">';
		// start 输出查询结果
		foreach($courses as $course){
			$output .= '<div class="classbanner">
							<!--课程图片-->
							<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" target="_blank" >
								<div class="course-pic">
									<div class="course-pic-cover"><span>点击查看</span></div>
									<img '.$this->my_get_course_formatted_summary_pix(new course_in_list($course)).' style="width:267px;height:178px;" />
								</div>
							</a>
							<!--课程图片 end-->
							<!--课程信息-->
							<div class="classinfo">
								<p class="classname"><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" target="_blank" >'.$course->fullname.'</a></p>
								<p class="info">'.mb_substr(strip_tags($course->summary),0,196,'UTF-8').'</p>
							</div>
							<!--课程信息 end-->
							<div class="course-data">
								<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" target="_blank">开始学习</a>
								<p>学习人数:</p>&nbsp;<p>'.$this->my_get_course_student_count($course).'</p>
							</div>
						</div>';
		}

		// end 输出查询结果
		$pagenum =  ceil($coursescount->count/$perpage);
		$prepage = ($page-1) > 1 ?($page-1):1;//上一页
		$nextpage = ($page+1) > $pagenum ? $pagenum:($page+1);//下一页
		$output .='
						<!--分页-->
						<div style="clear: both;"></div>
						<div class="paging">
						<nav>
							<ul class="pagination">
								<li>
									<a href="'.$CFG->wwwroot.'/course/mysearch.php?searchType=课程名&searchParam='.$search.'">
										<span aria-hidden="true">首页</span>
									</a>
								</li>
								<li>
									<a href="'.$CFG->wwwroot.'/course/mysearch.php?searchType=课程名&searchParam='.$search.'&page='.$prepage.'" aria-label="Previous">
										<span aria-hidden="true">上一页</span>
									</a>
								</li>';

		if(($page-5)>0){
			$output .= '<li><a href="#">...</a></li>';
		}
		for($i=1;$i<=$pagenum;$i++){
			if($page==$i){
				$output .= '<li><a class="active" href="'.$CFG->wwwroot.'/course/mysearch.php?searchType=课程名&searchParam='.$search.'&page='.$i.'">'.$i.'</a></li>';
			}elseif( $i>($page-5) && $i<($page+5) ){ //显示分页索引显示限定
				$output .= '<li><a href="'.$CFG->wwwroot.'/course/mysearch.php?searchType=课程名&searchParam='.$search.'&page='.$i.'">'.$i.'</a></li>';
			}
		}
		if(($pagenum > ($page+4))){
			$output .= '<li><a href="#">...</a></li>';
		}

		$output .='
								<li>
									<a href="'.$CFG->wwwroot.'/course/mysearch.php?searchType=课程名&searchParam='.$search.'&page='.$nextpage.'" aria-label="Next">
										<span aria-hidden="true">下一页</span>
									</a>
								</li>
								<li>
									<a href="'.$CFG->wwwroot.'/course/mysearch.php?searchType=课程名&searchParam='.$search.'&page='.$pagenum.'">
										<span aria-hidden="true">尾页</span>
									</a>
								</li>
							</ul>
						</nav>
						<div style="clear:both"></div>
						</div>
						<!--分页 end-->
						<div style="clear:both"></div>
					</div>

				</body>
			';

		return $output;
	}
	/** End 课程搜索结果页 xdw 20160530 */
	/** start 获取课程学习人数 */
	public function my_get_course_student_count($course){
		global $DB;
		$studentsNum = $DB->get_record_sql('select b.courseid,count(*) num from mdl_user_enrolments a join mdl_enrol b where b.courseid='.$course->id.' and b.id=a.enrolid');
		return $studentsNum->num;
	}
	/** end 获取课程学习人数 */

	
}
