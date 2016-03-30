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
	 * 选课页面的入口，（这里替换原来的方法 course_info_box ）
	 * 2016-03-02 徐东威
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
						<img '.$this->my_get_course_formatted_summary_pix(new course_in_list($course)).' />
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

	/** 2016-03-02 徐东威 */
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


	
	
	
	public function my_course_category($category){
		if(!isset($_GET["dep1categoryid"])){//方向
			$dep1categoryid = null;
		}
		else{
			$dep1categoryid = $_GET["dep1categoryid"];
		}
		if(!isset($_GET["dep2categoryid"])){//分类
			$dep2categoryid = null;
		}
		else{
			$dep2categoryid = $_GET["dep2categoryid"];
		}
		if(!isset($_GET["sortkind"])){//排序规则，1是最新，2是最热
			$sortkind = null;
		}
		else{
			$sortkind = $_GET["sortkind"];
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
		$output .=$this->my_course_category_subfirst($dep1categoryid);				
		//输出分类
		$output .=$this->my_course_category_subsecond($dep1categoryid,$dep2categoryid);		
		$output .= '
						
					</div>
					<!--marquee style="width: 100%;">《主题教育》课程最新上线</marquee-->
					<div class="course-tool-bar clearfix">
						<div class="tool-left l">';
		if($sortkind==1){
			$output .='
			<a href="#" class="sort-item active" onclick="sel5(1);">最新</a>
			<a href="#" class="sort-item" onclick="sel5(2);">最热</a>';
		}
		elseif($sortkind==2){
			$output .='
			<a href="#" class="sort-item" onclick="sel5(1);">最新</a>
			<a href="#" class="sort-item active" onclick="sel5(2);">最热</a>';
		}
		else{
			$output .='
			<a href="#" class="sort-item" onclick="sel5(1);">最新</a>
			<a href="#" class="sort-item" onclick="sel5(2);">最热</a>';
		}
		
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
			$output .= $this->my_course_category_display(null,null,$sortkind);//全部
		}
		elseif($dep2categoryid==null){
			$output .= $this->my_course_category_display($dep1categoryid,1,$sortkind);//第一层，要查询子类的课程
		}
		else{
			$output .= $this->my_course_category_display($dep2categoryid,null,$sortkind);//只输出第二层
		}
		$output .= '			
				</div>
			</div>
		</div>';
		
		
		 return $output;
	}
	//输出课程列表
	protected function my_course_category_display($categoryid=null,$dep=null,$sortkind=null){
		global $DB;
		if($categoryid==null){//全部
			//是否排序
			if($sortkind==1){//最新
				$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by timecreated desc;');
			}
			elseif($sortkind==2){//最热
				$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by timecreated desc;');
				//统计选课学生数,增加字段studnum
				//$this->my_calculate_num_of_course($courses);
				coursecat::my_calculate_num_of_course($courses);
				//重新排序
                $courses = $this->my_sortby_studnum($courses);
			}
			else{//默认按sortorder排序
				$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by sortorder;');
			}
		}
		elseif($dep==1){
			//查询包括子类
			$subcategories=$DB->get_records_sql('select id from mdl_course_categories where parent='.$categoryid.';');
			$categoryids= $categoryid;
			foreach($subcategories as $subcategory){
				$categoryids .=','.$subcategory->id;
			}
			if($sortkind==1){//最新
				$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by timecreated desc;');
			}
			elseif($sortkind==2){//最热
				$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by timecreated desc;');
				//统计选课学生数,增加字段studnum
				//$this->my_calculate_num_of_course($courses);
				coursecat::my_calculate_num_of_course($courses);
				//重新排序
                $courses = $this->my_sortby_studnum($courses);
			}
			else{//默认按sortorder排序
				$courses = $DB->get_records_sql('select distinct a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id in('.$categoryids.') and b.mdl_course_id=a.id order by a.sortorder;');
			}

		}
		else{//只查询子类
			if($sortkind==1){//最新
				$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by timecreated desc;');
			}
			elseif($sortkind==2){//最热
				$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by timecreated desc;');
				//统计选课学生数,增加字段studnum
				//$this->my_calculate_num_of_course($courses);
				coursecat::my_calculate_num_of_course($courses);
				//重新排序
                $courses = $this->my_sortby_studnum($courses);
			}
			else{//默认按sortorder排序
				$courses = $DB->get_records_sql('select a.id,fullname,summary from mdl_course a join mdl_course_link_categories b where b.mdl_course_categories_id='.$categoryid.' and b.mdl_course_id=a.id order by a.sortorder;');
			
			}
			//$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and ;');
		}

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
		$output .= '<!--换页按钮+-->
						<div class="page">
							<span class="disabled_page">首页</span>
							<span class="disabled_page">上一页</span>
							<a href="javascript:void(0)" class="active">1</a>
							<span class="disabled_page">下一页</span>
							<span class="disabled_page">尾页</span>
						</div>
						<!--换页按钮end-->';	
		return $output;
	}
	//输出主页的课程列表
	protected function my_course_category_display_indexpage($sortkind=null){
		global $DB;
		$courses = $DB->get_records_sql('select id,fullname,summary FROM mdl_course where id!=1 and visible=1 order by sortorder;');
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
	
	//输出第一层分类
	protected function my_course_category_subfirst($selectedid){
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
	protected function my_course_category_subsecond($parentcategoryid=null,$mysubcategoryid=null){
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
	//主页index
	public function my_print_indexpage(){
		if(!isset($_GET["sortkind"])){//排序规则，1是最新，2是最热
			$sortkind = null;
		}
		else{
			$sortkind = $_GET["sortkind"];
		}
		echo '
		<div class="course-content">
			<div class="course-tool-bar clearfix">
				<div class="tool-left l">
					<a href="javascript:void(0)" class="sort-item">最新</a>
					<a href="javascript:void(0)" class="sort-item active">最热</a>
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
			</div>
				
			<div class="course-list">

				<!--课程列表-->
				<div class="js-course-lists">
					<ul>';
		$this->my_course_category_display_indexpage($sortkind);
						echo '
						<!--第三行end-->
					</ul>
				</div>
				<!--课程列表end-->

				<!--换页按钮+-->
				<div class="page">
							<span class="disabled_page">首页</span>
							<span class="disabled_page">上一页</span>
							<a href="javascript:void(0)" class="active">1</a>
							<span class="disabled_page">下一页</span>
							<span class="disabled_page">尾页</span>
						</div>
				<!--换页按钮end-->
			</div>
		</div>
		';
	}
	
	
	
}
