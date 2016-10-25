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
 * Renderer for outputting the topics course format.
 *
 * @author 徐东威 2016-01-12
 * @package format_topics
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.3
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/renderer.php');
require_once($CFG->dirroot . "/course/renderer.php");
require_once($CFG->libdir. '/coursecatlib.php');
require_once($CFG->libdir. '/modinfolib.php');
require_once($CFG->libdir.'/outputrenderers.php');

/**
 * Basic renderer for topics format.
 *
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_studtopics_renderer extends format_section_renderer_base {

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);

        // Since format_topics_renderer::section_edit_controls() only displays the 'Set current section' control when editing mode is on
        // we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any other managing capability.
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'topics'));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('topicoutline');
    }

    /** start 输出课程所属分类 */
    public function my_print_courseCategorypath($course){
        global $CFG;
        $courseCategorys = $this->my_get_courseCategory($course->category);
        $courseCategoryStr = '<div class="coursetypebox">
                                   <div class="main">';
        if(count($courseCategorys)==1){//只有一级
            $courseCategoryStr .= '<a href="'.$CFG->wwwroot.'/course/index.php?dep1categoryid='.$courseCategorys[0]['categoryId'].'">'.$courseCategorys[0]['categoryName'].'</a>
                                   <span class="glyphicon glyphicon-chevron-right"></span>';
        }elseif(count($courseCategorys)==2){//有两级
            $courseCategoryStr .= '<a href="'.$CFG->wwwroot.'/course/index.php?dep1categoryid='.$courseCategorys[0]['categoryId'].'">'.$courseCategorys[0]['categoryName'].'</a>
                                   <span class="glyphicon glyphicon-chevron-right"></span>';
            $courseCategoryStr .= '<a href="'.$CFG->wwwroot.'/course/index.php?dep1categoryid='.$courseCategorys[0]['categoryId'].'&dep2categoryid='.$courseCategorys[1]['categoryId'].'">'.$courseCategorys[1]['categoryName'].'</a>
                                   <span class="glyphicon glyphicon-chevron-right"></span>';
        }
        $courseCategoryStr .=  '<a class="title" href="#">'.$course->fullname.'</a>
                                </div>
                            </div>';
        echo $courseCategoryStr;
    }
    /** end 输出课程所属分类 */

    /**
     * start 获取课程所属分类 20160802
     * @param (int) $courseCategory 当前课程的课程分类ID
     * @return Array|false $courseCategorys 课程所属分类数组
     */
    public function my_get_courseCategory($courseCategoryId){
        global $DB;
        $courseCategoryArray = false;
        $courseCategory = $DB->get_record_sql("select * from mdl_course_categories c where c.id = $courseCategoryId");
        $courseCategory_depth = $courseCategory->depth;//所属层级数
        $courseCategory_path = $courseCategory->path;//层级路径
        $courseCategory_paths = explode('/',mb_substr($courseCategory_path,1,strlen($courseCategory_path)));
        foreach($courseCategory_paths as $category_path){
            $temp = $DB->get_record_sql("select * from mdl_course_categories c where c.id = $category_path");
            $courseCategoryArray[] = ['categoryName'=>$temp->name,'categoryId'=>$temp->id];
        }
        return $courseCategoryArray;
    }
    /** end 获取课程所属分类 20160802 */

    /** renderer的入口函数,此函数被 ..moodle\course\format\studtopics\format.php 文件调用 20160729  */
    public function my_print_page_test($course, $sections, $mods, $modnames, $modnamesused){

        $modinfo = get_fast_modinfo($course);//获取课程的全部信息

        $this->my_print_courseCategorypath($course);//输出课程所属分类

        echo '<!--主板块-->
            <div class="main">
                <!--课程信息板块-->
                 '.$this->my_print_course_introduce($course).'
                <!--课程信息板块 end-->';

        echo '<div class="plate-box">
                <!--课程章节及评论板块-->
                '.$this->my_print_course_chapter_and_comment($course,$modinfo).'
                <!--课程章节及评论板块 end-->
                <!--课程评分及排行榜板块-->
                 '.$this->my_print_course_score_and_rank($course).'
                <!--课程评分及排行榜板块 end-->
                </div>';

        echo '  </div>
                <!--主板块 end--> ';

        //课程笔记隐藏参数设置
        echo '<button id="hiddencourseid" value="'.$course->id.'" style="display: none;"></button>
			  <button id="hiddencoursefullname" value="'.$course->fullname.'" style="display: none;"></button>';
        //end 课程笔记隐藏参数设置

    }

    /** (旧) renderer的入口函数,此函数被 ..moodle\course\format\studtopics\format.php 文件调用   */
    public function my_print_page_test2($course, $sections, $mods, $modnames, $modnamesused){



        $modinfo = get_fast_modinfo($course);//获取课程的全部信息

        echo '<!--新html静态页面 body 内容--><div id="main">';
        //输出course_infos
        $this->my_print_course_infos($course);
        echo '
			<div class="course-info-main clearfix w has-progress">
				<div class="content-wrap clearfix">
					<div class="content">
						<div class="classintroduce">
							<h3>课程介绍</h3>
							<p>'.$this->my_search_summary_dic($course).'</p>
						</div>
						<div class="mod-tab-menu">
							<ul class="course-menu clearfix">';

        $evaluationCount = $this->my_get_course_evaluation_count($course);
        $count_page = $evaluationCount->ceilcount;

        if(!isset($_GET['page'])){
            echo '<li><a id="zhangjie" class="active"  href="javascript:void(0);"><span>课程提纲</span></a></li>
								<li><a id="pingjia"  class="" href="javascript:void(0);" style="width:150px"><span>课程评价('.$evaluationCount->count.')</span></a></li>
								</ul>
						</div>

						<div class="mod-chapters" >';
            //	<!--第一章-->
            //（输出各章的内容）输出课程中各主题及其包含的活动
            echo $this->my_printf_course_chapter($modinfo);
            //	<!--第一章end-->
            echo '
						</div>
						<!--mod-chapters 的 div end-->
						<div class="evaluation-list" style="display: none">
							<div class="mycomment">
								<!-- 2016.3.25 毛英东 添加表情-->
								<textarea class="form-control" id="comment-text" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
                                <img src="../theme/more/img/emotion.png" class="pull-left emotion" style="width:25px;height:25px;margin-top:4px;cursor:pointer">
                                <!-- end  2016.3.25 毛英东 添加表情 -->
								<button id="comment-btn" class="btn btn-success">发表评论</button>
							</div>

							<!--evaluation-info end-->';
        }
        else{
            echo '<li><a id="zhangjie" class=""  href="javascript:void(0);"><span>课程提纲</span></a></li>
								<li><a id="pingjia"  class="active" href="javascript:void(0);" style="width:150px"><span>课程评价('.$evaluationCount->count.')</span></a></li>
								</ul>
						</div>

						<div class="mod-chapters" style="display: none">';
            //	<!--第一章-->
            //（输出各章的内容）输出课程中各主题及其包含的活动
            echo $this->my_printf_course_chapter($modinfo);
            //	<!--第一章end-->
            echo '
						</div>
						<!--mod-chapters 的 div end-->
						<div class="evaluation-list" style="display: block">
							<div class="mycomment">
								<!-- 2016.3.25 毛英东 添加表情-->
								<textarea class="form-control" id="comment-text" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
                                <img src="../theme/more/img/emotion.png" class="pull-left emotion" style="width:25px;height:25px;margin-top:4px;cursor:pointer">
                                <!-- end  2016.3.25 毛英东 添加表情 -->
								<button id="comment-btn" class="btn btn-success">发表评论</button>
							</div>

							<!--evaluation-info end-->';
        }

        $current_page = $_SESSION['pageid'];
        unset ($_SESSION['pageid']);
        $this->my_get_course_evaluation($course, $current_page - 1);
        echo '
<!--evaluation-con end-->
                                <div class="paginationbox">
                                    <ul class="pagination">
                                        <li>
                                          <a href="../course/view.php?id='.$course->id.'&page=1">首页</a>
                                        </li>
                                        <li>

                                          <a href="../course/view.php?id='.$course->id.'&page='.($current_page <= 1 ? 1: $current_page - 1).'">上一页</a>
                                        </li>';
        /**  Start 修改翻页函数 朱子武 20160327*/
        $this->my_get_course_evaluation_current_count($count_page, $course,$current_page);
        /**  End 修改翻页函数 朱子武 20160327*/
        echo'
                                        <li>
                                          <a href="../course/view.php?id='.$course->id.'&page='.($current_page < $count_page ? ($current_page + 1): $count_page).'">下一页</a>
                                        </li>
                                        <li>
                                          <a href="../course/view.php?id='.$course->id.'&page='.$count_page.'">尾页</a>
                                        </li>
                                    </ul>
							    </div>

							<!--evaluation end-->

						</div>
						<!--evaluation-list end-->

					</div>
					<!--content end-->
					<div class="aside r">
						<div class="bd">
							<div class="box mb40">';
        $this->my_get_continue_video($course);

        $this->my_print_teamlearn($course);
        echo '

								<div style="width: 100%; height: 160px; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px;">
									<p style="color: #777777; font-size: 14px;">满意度评分</p>';
        $mysumscore = $this->my_get_course_score($course);
        echo '
									<div style="width: 100%; margin: auto; border-bottom: 1px solid #ddd; margin-bottom: 10px; height: 40px;">
										<p style="color: #E38D13;font-size: 20px;">';
        $this->my_get_glyphicon_star($mysumscore);
        echo '
										</p>
									</div>
									<a href="../course/coursegrade/index.php?id='.$course->id.'&page=1" style="color: #AC2925; font-size: 10px;">立即去评分>></a>
								</div>
							';

        echo '
								<!--<h4>教师信息</h4>
								<div class="teacher-info">
									<div><img  src="../theme/more/pix/courseforstu/52afc8fe0001be1001000100-80-80.jpg"/></div>
									<div>
										<a class="tit" href="#">luoyonghao</a>
										<a class="job" href="#">工程师</a>
									</div>
								</div>
								<div class="course-info-tip">
									<dl class="first">
										<dt>个人简介</dt>
										<dd class="autowrap">没有任何WEB经验的WEB应用程序开发者、对WEB前端技术感兴趣的用户均可学习。</dd>
									</dl>
									<dl>
										<dt>老师告诉你能学到什么？</dt>
										<dd class="autowrap">轻松学习HTML、CSS样式基础知识，可以利用HTML、CSS样式技术制作出简单页面。</dd>
									</dl>
								</div>
							</div>

						</div>-->

						<div class="courserecommend">
							<div class="courserecommend-title">
								<h2><span class="glyphicon glyphicon-bookmark"></span><p>&nbsp;相关课程推荐</p></h2>
							</div>
							<table class="table courserecommend-table">
								<tbody>';

        echo $this->my_get_similar_course($course);//获取推荐的课程
        echo '
								</tbody>
							</table>
						</div>';

        global $CFG;
        global $USER;
        $rankResult = $this->rankCourse($course->id,$USER->id);//获取课程的排名
        $courseRank = $rankResult->showRank;//前几名
        $numberMy = $rankResult->myRank;//我的名次
        $numberOne = '';
        $numberTow = '';
        $numberThree = '';

        if($courseRank){//如果有记录再显示排名
            if($courseRank['1']){
                $numberOne = $courseRank['1']->firstname;
            }
            if($courseRank['2']){
                $numberTow = $courseRank['2']->firstname;
            }
            if($courseRank['3']){
                $numberThree = $courseRank['3']->firstname;
            }

            //如果当前用户的名次在前三名
            if($numberMy <= 3){
                echo '
						<!--排行榜-->
						<div class="ranking-list">
							<div class="ranking-list-title">
								<h2><span class="glyphicon glyphicon-signal"></span><p>&nbsp;学习排行榜</p></h2>
							</div>
							<table class="ranking-list-table">
								<tbody>
									<tr>
										<td><img src="'.$CFG->wwwroot.'/theme/more/pix/courseforstu/no1.png"><p class="NO1 king" href="#" >&nbsp;NO.1 '.$numberOne.'</p></td>
									</tr>
									<tr>
										<td><img src="'.$CFG->wwwroot.'/theme/more/pix/courseforstu/no2.png"><p class="NO2 silver" href="#">&nbsp;NO.2 '.$numberTow.'</p></td>
									</tr>
									<tr>
										<td><img src="'.$CFG->wwwroot.'/theme/more/pix/courseforstu/no3.png"><p class="NO3 copper" href="#">&nbsp;NO.3  '.$numberThree.'</p></td>
									</tr>
									<tr>
										<td><a class="more"  href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;............</a></td>
									</tr>
								</tbody>
							</table>
						</div>';
            }
            else{
                echo '
						<!--排行榜-->
						<div class="ranking-list">
							<div class="ranking-list-title">
								<h2><span class="glyphicon glyphicon-signal"></span><p>&nbsp;学习排行榜</p></h2>
							</div>
							<table class="ranking-list-table">
								<tbody>
									<tr>
										<td><img src="'.$CFG->wwwroot.'/theme/more/pix/courseforstu/no1.png"><p class="NO1 king" href="#" >&nbsp;NO.1 '.$numberOne.'</p></td>
									</tr>
									<tr>
										<td><img src="'.$CFG->wwwroot.'/theme/more/pix/courseforstu/no2.png"><p class="NO2 silver" href="#">&nbsp;NO.2 '.$numberTow.'</p></td>
									</tr>
									<tr>
										<td><img src="'.$CFG->wwwroot.'/theme/more/pix/courseforstu/no3.png"><p class="NO3 copper" href="#">&nbsp;NO.3  '.$numberThree.'</p></td>
									</tr>
									<tr>
										<td><a class="more"  href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;............</a></td>
									</tr>
									<tr>
										<td><a class="who" href="#"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;no.'.$numberMy.' '.fullname($USER).' </a></td>
									</tr>
								</tbody>
							</table>
						</div>';
            }
        }



        echo'
					</div>
				</div>

				<div class="clear"></div>

			</div>
		</div>



		<div id="J_GotoTop" class="elevator">
			<a class="elevator-msg" id="mynote-btn" style="cursor:pointer"></a>
			<button id="hiddencourseid" value="'.$course->id.'" style="display: none;"></button>
			<button id="hiddencoursefullname" value="'.$course->fullname.'" style="display: none;"></button>
			<a class="elevator-weixin" style="cursor:pointer"></a>
            <a class="elevator-app"  id="collection-btn" style="cursor:pointer"></a>
			<a class="elevator-diaocha" id="like-btn" style="cursor:pointer"></a>
            <a class="elevator-top" href="#"></a>

		</div>
		<div class="chat-box chat-box1">
			<div class="chat-head">
				<p>聊天室</p>
				<p id="chat-close" class="close">x</p>
			</div>
				</div>
            <div class="chat-box chat-box2">
				<div class="chat-head">
					<p>课程笔记</p>
					<p id="chat-close2" class="close">x</p>
				</div>
			</div>
		<!--script-->

		<div class="mask"></div>

<!--新新html静态页面 body 内容 end-->';
        //$this->my_get_likecount();
    }

    /** 课程学习排行榜 20160729 */
    public function my_get_study_rank($course){
        global $CFG;
        global $USER;
        $rankResult = $this->rankCourse($course->id,$USER->id);//获取课程的排名
        $courseRank = $rankResult->showRank;//前几名
        $numberMy = $rankResult->myRank;//我的名次
        $numberOne = '';
        $numberTow = '';
        $numberThree = '';
        $output = '';

        if($courseRank){//如果有记录再显示排名
            if($courseRank['1']){
                $numberOne = $courseRank['1']->firstname;
            }
            if($courseRank['2']){
                $numberTow = $courseRank['2']->firstname;
            }
            if($courseRank['3']){
                $numberThree = $courseRank['3']->firstname;
            }

            //如果当前用户的名次在前三名
            if($numberMy <= 3){
                $output .= ' <p class="learnner"><span>1</span><a href="#">'.$numberOne.'</a></p>
                            <p class="learnner"><span>2</span><a href="#">'.$numberTow.'</a></p>
                            <p class="learnner"><span>3</span><a href="#">'.$numberThree.'</a></p>
                            <p class="learnner"><span>&nbsp;&nbsp;</span><a href="#">.............</a></p>';
            }elseif($numberMy == 4){
                $output .= ' <p class="learnner"><span>1</span><a href="#">'.$numberOne.'</a></p>
                            <p class="learnner"><span>2</span><a href="#">'.$numberTow.'</a></p>
                            <p class="learnner"><span>3</span><a href="#">'.$numberThree.'</a></p>
                            <p class="learnner"><span>'.$numberMy.'</span><a href="#">'.fullname($USER).'</a></p>
                <p class="learnner"><span>&nbsp;&nbsp;</span><a href="#">.............</a></p>';
            }
            else{
                $output .= ' <p class="learnner"><span>1</span><a href="#">'.$numberOne.'</a></p>
                            <p class="learnner"><span>2</span><a href="#">'.$numberTow.'</a></p>
                            <p class="learnner"><span>3</span><a href="#">'.$numberThree.'</a></p>
                            <p class="learnner"><span>&nbsp;&nbsp;</span><a href="#">.............</a></p>
                            <p class="learnner"><span>'.$numberMy.'</span><a href="#">'.fullname($USER).'</a></p>';
            }
        }
        return $output;
    }

    /** start 输出课程评分和排行 20160728*/
    public function my_print_course_score_and_rank($course){

        $output = '<div class="plate-r">';
        $output .= $this->my_get_continue_video2($course);//继续学习
        $output .= $this->my_print_teamlearn2($course);//集体学习
        $mysumscore = $this->my_get_course_score2($course);//获取评分分数
        $output .= '<div class="score">
                        <p class="title">满意度评分：</p>
                        <p class="num">'.$mysumscore.'</p>
                        '.$this->my_get_glyphicon_star2($mysumscore).'
                        <a href="../course/coursegrade/index.php?id='.$course->id.'&page=1" >立即评分>>></a>
                    </div>';
        $output .= '<div class="recommend">
                        <p class="title"><span class="glyphicon glyphicon-list"></span>&nbsp;相关课程推荐</p>
                        '.$this->my_get_similar_course2($course).'
                    </div>';
        $output .= '<div class="rank">
                        <p class="title"><span class="glyphicon glyphicon-signal"></span>&nbsp;学习排行榜</p>
                        '.$this->my_get_study_rank($course).'
                    </div>';
        $output .= '</div>';

        return $output;
    }
    /** end 输出课程评分和排行 */

    /** start 输出课程章节和评论 20160728*/
    public function my_print_course_chapter_and_comment($course,$modinfo){

        if(!isset($_GET['page'])){//对课程章节和课程评论的显隐控制
            $courseOutline_class = 'active';
            $courseOutline_display = 'style="display: block"';
            $courseCommemt_class = '';
            $courseCommemt_display = 'style="display: none"';
        }else{
            $courseOutline_class = '';
            $courseOutline_display = 'style="display: none"';
            $courseCommemt_class = 'active';
            $courseCommemt_display = 'style="display: block"';
        }
        $evaluationCount = $this->my_get_course_evaluation_count($course);
        $count_page = $evaluationCount->ceilcount;//评论页数

        $output = '<div class="plate-l">
				<div class="main">
					<div class="mod-tab-menu">
						<ul class="course-menu clearfix">
							<li><a id="zhangjie" class="'.$courseOutline_class.'"  href="javascript:void(0);"><span>课程提纲</span></a></li>
							<li><a id="pingjia"  class="'.$courseCommemt_class.'" href="javascript:void(0);">课程评价(<span>'.$evaluationCount->count.'</span>)</a></li>
						</ul>
					</div>';

        $output .= $this->my_printf_course_chapter3($modinfo,$courseOutline_display);//输出课程章节
        $output .= $this->my_printf_course_comment($course,$count_page,$courseCommemt_display);//输出课程评论

        $output .= '    </div>
			        </div>';

        return $output;
    }
    /** end 输出课程章节和评论 */

    /** start 输出课程介绍  20160728*/
    public function my_print_course_introduce($course){
        $output = ' <div class="banner">
                    <div class="maininfo">
                        <div class="l-b">
                            <img style="height: 247px;width: 348px;" '.$this->my_get_course_formatted_summary_pix(new course_in_list($course)).' />
                        </div>
                        <div class="r-b">
                            <h3 class="title">'.$course->fullname.'</h3><h3 class="title-btn">'.$this->my_print_exitcouse($course).'</h3>
                            <div style="clear: both;"></div>
                            <p>课程介绍</p>
                            <p class="courseinfo">'.mb_substr($this->my_search_summary_dic($course),0,196,"UTF-8").'</p>
                            <p class="num">学习人数：<span>'.$this->my_get_course_student_count($course).'</span></p>
                        </div>
                    </div>
                </div>';

        return $output;
    }
    /** end 输出课程介绍 */

    /** Start  加入集体学习按钮 20160729 **/
    public function my_print_teamlearn2($course){
        $context = context_course::instance($course->id);
        $output = '';
        if (has_capability('mod/teamlearn:addinstance', $context)) {//判断用户有没有集体学习的权限，目前：管理员，集体学习角色
            global $DB;
            $teamlearn_switchdata = $DB->get_record('teamlearn_switch', array('id' => '1'));
            if($teamlearn_switchdata->teamlearn_switch==1){
                //判断当前集体学习是否已经开始
                if(isset($_SESSION['collectiveLearn']) && $_SESSION['collectiveLearn']==true){
                    $currenturl = new moodle_url('/course/view.php?id='.$course->id.'&collectiveLearnoff=true');
                    $output .=  '<a href="'.$currenturl.'" class="group-btn" >关闭集体学习</a>';
                }
                else{
                    $currenturl = new moodle_url('/course/teamlearn_pickuser.php?id='.$course->id);
                    $output .=  '<a href="'.$currenturl.'" class="group-btn" >打开集体学习</a>';
                }
            }
            $output .=  '</br>';
        }
        return $output;
    }
    /** End**/

    /** Start 20160102 加入集体学习按钮 岑霄**/
    public function my_print_teamlearn($course){
        $context = context_course::instance($course->id);
        if (has_capability('mod/teamlearn:addinstance', $context)) {//判断用户有没有集体学习的权限，目前：管理员，集体学习角色
            global $DB;
            $teamlearn_switchdata = $DB->get_record('teamlearn_switch', array('id' => '1'));
            if($teamlearn_switchdata->teamlearn_switch==1){
                //判断当前集体学习是否已经开始
                if(isset($_SESSION['collectiveLearn']) && $_SESSION['collectiveLearn']==true){
                    $currenturl = new moodle_url('/course/view.php?id='.$course->id.'&collectiveLearnoff=true');
                    //$buttons = $OUTPUT->teamlearn_button($currenturl,true);//关闭按钮
                    //$PAGE->set_button($buttons);
                    echo '<a href="'.$currenturl.'"><button class="btn btn-danger" style="width: 89%; height: 36px; margin-bottom: 20px;">关闭集体学习</button></a>';

                }
                else{
                    $currenturl = new moodle_url('/course/teamlearn_pickuser.php?id='.$course->id);
                    echo '<a href="'.$currenturl.'"><button class="btn btn-danger" style="width: 89%; height: 36px; margin-bottom: 20px;">打开集体学习</button></a>';
                    //$buttons = $OUTPUT->teamlearn_button($currenturl,false);//打开按钮
                    //$PAGE->set_button($buttons);
                }
            }
            echo '</br>';
        }
    }
    /** End**/

    /**获取学生的数量 xdw
     * @param $course
     * @return mixed 返回选课的学生人数
     */
    public function my_get_course_student_count($course){

        global $DB;
        $studentsNum = $DB->get_records_sql('select b.courseid,count(*) num from mdl_user_enrolments a join mdl_enrol b where b.courseid='.$course->id.' and b.id=a.enrolid');

//        var_dump($studentsNum);
        return $studentsNum[$course->id]->num;
    }

    /**获取summary中的图片
     * @param $course
     * @return string 返回的例子：src="http://localhost/moodle/pluginfile.php/228/course/summary/xiangji.png"
     */
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

    /**截取课程简介
     * xdw
     * @param $course
     * @return string 课程简介
     */
    public function my_search_summary_dic($course){

//        $index=strpos($course->summary,'</p');
//        $NewSummary=substr($course->summary,3,$index-3);
//        $NewSummary=mb_substr($course->summary,0,count($course->summary),'UTF-8');//这里设置第三个参数出现字符串的截取问题
        $NewSummary=strip_tags($course->summary);//去掉html标签

        return $NewSummary;
    }

    //输出课程的简介图片栏
    public function my_print_course_infos($course){

        echo '<div class="course-infos"  style="background-color: #f0f0f0;">
				<div class="w pr" style="background-color: #5b5b5a;">

					<div class="banner-left">
                        <img '.$this->my_get_course_formatted_summary_pix(new course_in_list($course)).' />
					</div>

					<div class="banner-right">
						<div class="path">
							<a href="#"></a>
							<i class="path-split"></i><a href="#"></a>
							<i class="path-split"></i><a href="#"></a>
							<i class="path-split"></i><span></span>
						</div>

						<div class="hd">
							<h2 class="l">'.$course->fullname.'</h2>
						</div>

						<div class="learnernum">
							<p class="p1">'.$this->my_get_course_student_count($course).'</p>
							<p class="p2">学习人数</p>
						</div>
					</div>
				</div>
				<div class="info-bg" id="js-info-bg">
					<div class="cover-img-wrap">
						<img data-src="http://img.mukewang.com/55af49ad000116a506000338.jpg" alt="" style="display: none" id="js-cover-img">
					</div>
					<div class="cover-mask"></div>
					<canvas width="1903" height="240" class="cover-canvas" id="js-cover-canvas"></canvas>
				</div>
			</div>';
    }

    /** xdw 返回课程评论 20160729 */
    public function my_printf_course_comment($course,$count_page,$courseCommemt_display){

        $output = '<div class="evaluation-list" '.$courseCommemt_display.'>
						<div class="mycomment">
							<textarea class="form-control" id="comment-text" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
                            <img src="../theme/more/img/emotion.png" class="pull-left emotion" style="width:25px;height:25px;margin-top:4px;cursor:pointer">
                            <button id="comment-btn" class="btn btn-primary">发表评论</button>
						</div>';

//        $current_page = $_SESSION['pageid'];
//        unset ($_SESSION['pageid']);
//        $output .= $this->my_get_course_evaluation2($course, $current_page - 1);//课程评论
//
//        //评论的分页
//        $output .= '<div class="paginationbox">
//							<a href="../course/view.php?id='.$course->id.'&page=1">首页</a>
//                        	<a href="../course/view.php?id='.$course->id.'&page='.($current_page <= 1 ? 1: $current_page - 1).'">上一页</a>
//                        	'.$this->my_get_course_evaluation_current_count2($count_page, $course,$current_page).'
//                        	<a href="../course/view.php?id='.$course->id.'&page='.($current_page < $count_page ? ($current_page + 1): $count_page).'">下一页</a>
//                        	<a href="../course/view.php?id='.$course->id.'&page='.$count_page.'">尾页</a>
//						</div>';

        /**替换部分 删除原有的评论输出及分页条输出 将以下代码替换*/
        /**start nlw20160823 评论内容局部刷新*/
        $output .= '
                <!--评论内容-->
				<div class="evaluation" id="page-evaluation">
				</div>
         		<!--评论内容 end-->
         		<!--分页-->
				<div class="paginationbox">
				  <!--输出页码-->
				  <div id="page-list">
		         </div>
		         <!--输出页码-->
				</div>
				<!--分页 end-->';
        /**end 评论内容局部刷新*/
        /**end 替换部分*/

        $output .= '</div>';

        return $output;
    }

    /** xdw 返回课程各主题及其中的活动 20160729 */
    public function my_printf_course_chapter3($modinfo,$courseOutline_display){

        $output='<div class="mod-chapters" '.$courseOutline_display.' >';

        for($i=1;$i<count($modinfo->sections);$i++){

            // $session_name=$modinfo->sectioninfo[1]['_name'];//这里获取不到_name的值，因为该属性是被保护的
            $thissection = $modinfo->get_section_info($i);//这里通过系统中原来类的定义方法才能获取其中某些被保护的值
            $session_name = $thissection->__get('name');

            $session = $modinfo->sections[$i];//获取每个主题下的活动ID列表

            $output.='<!--第'.$i.'章--><div class="chapter ">
                        <h3 class="showul">
                            <strong><i class="state-expand"></i>';
            $output.=$session_name;//拼接主题的名称
            $output.='</strong>
                        </h3>
                        <ul class="video">';

            for($k=0;$k<count($session);$k++){
                $cms=$modinfo->cms[$session[$k]];//每个课程中的各个活动的信息
                $cms_name=$cms->name;//活动的名称
                $cms_url=$cms->url;//活动的URL对象
                if(!empty($cms_url)){//如果不是空
                    $cms_url_path=$cms_url->get_path();//从URL对象中获取path，注意：这里是的path是protect属性，要调用系统的方法进行获取
                }else{
                    $cms_url_path='#';
                }
                $cms_id=$cms->id;//获取活动id
                $cms_url_path=$cms_url_path.'?id='.$cms_id;//拼接URL
                $course=$modinfo->get_course();//再次获取课程对象

                $completiondata = $this->my_get_course_complateState($course,$cms);//获取课程的完成状态对象，这里的$cms对应$mod
                $course_completionstate = $completiondata->completionstate;//获取完成状态标记值

                $output.='<li>
                                <a target="_blank" href="'.$cms_url_path.'" class="J-media-item programme">';
                $output.=$cms_name;

                //对完成状态的图标进行样式的修改
                if( $course_completionstate == 0 ){//如果没有完成

                    $output.='<i class="study-state"></i>
                                </a>
                            </li>';

                }else{//如果完成了，就添加或修改样式css

                    $output.='<i class="study-state done"></i>
                                </a>
                            </li>';
                }

            }

            $output.=' </ul>
                    </div><!--第'.$i.'章end-->';

        }

        $output .= '</div>
					<!--mod-chapters 的 div end-->';

        return $output;
    }

    /** xdw 返回课程各主题及其中的活动 */
    public function my_printf_course_chapter($modinfo){

        $output='';

        for($i=1;$i<count($modinfo->sections);$i++){

            // $session_name=$modinfo->sectioninfo[1]['_name'];//这里获取不到_name的值，因为该属性是被保护的
            $thissection = $modinfo->get_section_info($i);//这里通过系统中原来类的定义方法才能获取其中某些被保护的值
            $session_name = $thissection->__get('name');

            $session = $modinfo->sections[$i];//获取每个主题下的活动ID列表

            $output.='<div class="chapter ">
                        <h3 class="showul">
                            <strong><i class="state-expand"></i>';
            $output.=$session_name;//拼接主题的名称
            $output.='</strong>
                        </h3>
                        <ul class="video">';

            for($k=0;$k<count($session);$k++){
                $cms=$modinfo->cms[$session[$k]];//每个课程中的各个活动的信息
                $cms_name=$cms->name;//活动的名称
                $cms_url=$cms->url;//活动的URL对象
                if(!empty($cms_url)){//如果不是空
                    $cms_url_path=$cms_url->get_path();//从URL对象中获取path，注意：这里是的path是protect属性，要调用系统的方法进行获取
                }else{
                    $cms_url_path='#';
                }
                $cms_id=$cms->id;//获取活动id
                $cms_url_path=$cms_url_path.'?id='.$cms_id;//拼接URL
                $course=$modinfo->get_course();//再次获取课程对象

                $completiondata = $this->my_get_course_complateState($course,$cms);//获取课程的完成状态对象，这里的$cms对应$mod
                $course_completionstate = $completiondata->completionstate;//获取完成状态标记值

                $output.='<li>
                                <a target="_blank" href="'.$cms_url_path.'" class="J-media-item programme">';
                $output.=$cms_name;

                //对完成状态的图标进行样式的修改
                if( $course_completionstate == 0 ){//如果没有完成

                    $output.='<i class="study-state"></i>
                                </a>
                            </li>';

                }else{//如果完成了，就添加或修改样式css

                    $output.='<i class="study-state done"></i>
                                </a>
                            </li>';
                }

            }

            $output.=' </ul>
                    </div>';

        }

        return $output;
    }

    //输出课程‘各主题’中的活动--> 原html样例
    //xdw
    public  function  my_printf_course_chapter2(){

        $output='<div class="chapter ">
                        <h3 class="showul">
                            <strong><i class="state-expand"></i>第1章 Html介绍</strong>
                        </h3>
                        <ul class="video">
                            <li>
                                <a target="_blank" href="javascript:void(0);" class="J-media-item programme">1-1 代码初体验，制作我的第一个网页
                                    <i class="study-state"></i>
                                </a>
                                <!--em class="laststudy">最近学习</em-->
                            </li>
                            <li>
                                <a target="_blank" href="javascript:void(0);" class="J-media-item programme">1-2 Html和CSS的关系
                                    <i class="study-state done"></i>
                                </a>
                            </li>
                            <li>
                                <a target="_blank" href="javascript:void(0);" class="J-media-item programme">1-3 认识html标签
                                    <i class="study-state"></i>
                                </a>
                            </li>
                            <li>
                                <a target="_blank" href="javascript:void(0);" class="J-media-item programme">1-4 标签的语法
                                    <i class="study-state done"></i>
                                </a>
                            </li>
                            <li>
                                <a target="_blank" href="javascript:void(0);" class="J-media-item programme">1-5 认识html文件基本结构
                                    <i class="study-state"></i>
                                </a>
                            </li>
                            <li>
                                <a target="_blank" href="javascript:void(0);" class="J-media-item programme">1-6 认识head标签
                                    <i class="study-state done"></i>
                                </a>
                            </li>
                            <li>
                                <a target="_blank" href="javascript:void(0);" class="J-media-item programme">1-7 了解HTML的代码注释
                                    <i class="study-state"></i>
                                </a>
                            </li>
                        </ul>
                    </div>';

        return $output;
    }

    //获取课程的学习状态
    //xdw
    //参照course\renderer.php中的函数course_section_cm_list和
    //course_section_cm_completion函数中的 $completiondata = $completioninfo->get_data($mod, true);方法
    public function my_get_course_complateState($course,$mod){

        $completioninfo = new completion_info($course);
        $completiondata = $completioninfo->get_data($mod, true);

        return $completiondata;
    }

    /** Start 按照用户所属级别，筛选课程 */
    function set_userAllowCourse(){
        global $CFG;
        require_once($CFG->dirroot.'/course/course_classify/course_lib.php');
        add_userAllowCourse();
    }
    /** end 按照用户所属级别，筛选课程 */


    /** xdw 获取相似课程 20160729*/
    public function my_get_similar_course2($course){

        // Start 按照用户所属级别，筛选课程
        $this->set_userAllowCourse();
        // end 按照用户所属级别，筛选课程
        global $USER;
        //如果当前角色只能查看部分课程
        $sql_add = "";
        if($USER->ava_course_flag_my){
            if($USER->ava_course_my){
                $sql_add = " and mc.id in ($USER->ava_course_my)";
            }
            else{//没有可查看的课程
                $sql_add = " and mc.id = -1 ";
            }
        }

        $output = '';
        global $DB;
        $courselist = $DB->get_records_sql('select mtl.link_id from mdl_tag_link mtl where mtl.tagid in (select mtl2.tagid from mdl_tag_link mtl2 where mtl2.link_id = '.$course->id.') and mtl.link_id != '.$course->id.' limit 0,4');
        $courseIDarray=array_keys($courselist);//获取数组的相似的课程ID
        for($i=0;$i<count($courselist);$i++){
            $courseID = $courseIDarray[$i];
            $sql = 'select mc.* from mdl_course mc  where mc.id = '.$courseID.$sql_add;
            $similar_course =  $DB->get_records_sql($sql);//由课程的id获取课程的信息,或者课程名称
            if($similar_course){
                $similar_course_name = $similar_course[$courseID]->fullname;
                $similar_course_URL = "../course/view.php?id=".$courseIDarray[$i];//拼接课程的URL
                $output .= '<p class="course"><a href="'.$similar_course_URL.'" >'.$similar_course_name.'</a></p>';
            }
        }
        return $output;
    }

    /** xdw 获取相似课程 */
    public function my_get_similar_course($course){

        $output = '';
        global $DB;

        $courselist = $DB->get_records_sql('select mtl.link_id from mdl_tag_link mtl where mtl.tagid in (select mtl2.tagid from mdl_tag_link mtl2 where mtl2.link_id = '.$course->id.') and mtl.link_id != '.$course->id.' limit 0,4');
        $courseIDarray=array_keys($courselist);//获取数组的相似的课程ID
        for($i=0;$i<count($courselist);$i++){

            $courseID = $courseIDarray[$i];
            $similar_course =  $DB->get_records_sql('select mc.* from mdl_course mc  where mc.id = '.$courseID);//由课程的id获取课程的信息,或者课程名称
            $similar_course_name = $similar_course[$courseID]->fullname;
            $similar_course_URL = "../course/view.php?id=".$courseIDarray[$i];//拼接课程的URL

            $output .= '<tr>
										<td><a href="'.$similar_course_URL.'"><span class="glyphicon glyphicon-book"></span>&nbsp;'.$similar_course_name.'</a></td>
									</tr>';
        }

        return $output;
    }

//    获取评价数目页数
    public function my_get_course_evaluation_count($course)
    {
        global $DB;
        $evaluation = $DB->get_records_sql('SELECT id as mycount FROM mdl_comment_course_my WHERE courseid = ? ', array($course->id));
        //$evaluation = $DB->get_records_sql('SELECT courseid, count(*) as mycount FROM mdl_comment_course_my WHERE courseid = ? ', array($course->id));
        //$mycount = $evaluation[$course->id]->mycount;
        $mycount = count($evaluation) < 0 ? 0 : count($evaluation);
        $evaluationCount = new stdClass();
        $evaluationCount->count = $mycount;
        $mycount = ceil($mycount/10);
        $evaluationCount->ceilcount = ($mycount <= 1 ? 1: $mycount);
        return $evaluationCount;
    }

    /** START  获取课程评分星星 20160729*/
    function my_get_glyphicon_star2($num)
    {
        $output = '<p class="starbox">';
        for($i = 0; $i < ceil($num/2); $i++)
        {
            $output .=  '<span class="glyphicon glyphicon-star"></span>';
        }
        $output .= '</p>';
        return $output;
    }
    /** --- my_get_glyphicon_star END ---*/

     /** START 朱子武 获取课程评分星星 20160226*/
    function my_get_glyphicon_star($num)
    {
        for($i = 0; $i < ceil($num/2); $i++)
        {
            echo'<span class="glyphicon glyphicon-star"></span>';
        }
    }
    /** --- my_get_glyphicon_star END ---*/

    //  输出页码
    public function my_get_course_evaluation_current_count2($count_page, $course,$current_page)
    {
        /** Start 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
        $numstart = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page - 2):1):($count_page - 4)):1;
        $numend = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page + 2):5):($count_page)):$count_page;
        /** End 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
        $output = '';
        for($num = $numstart; $num <= $numend; $num ++) {
            if ($num == $current_page) {
                //  修改当前页样式标示
                $output .= '<a class="active" href="../course/view.php?id=' . $course->id . '&page=' . $num . '">' . $num . '</a>';
            } else {
                $output .= '<a href="../course/view.php?id=' . $course->id . '&page=' . $num . '">' . $num . '</a>';
            }
        }
        return $output;
    }

    //    输出页码
    public function my_get_course_evaluation_current_count($count_page, $course,$current_page)
    {
        /** Start 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
        $numstart = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page - 2):1):($count_page - 4)):1;
        $numend = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page + 2):5):($count_page)):$count_page;
        /** End 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
        for($num = $numstart; $num <= $numend; $num ++)
        {
//            if($num == $current_page)
//            {
//                //  这里需要修改样式标示当前页
//                echo'<li><a href="../course/view.php?id='.$course->id.'&page='.$num.'">'.$num.'</a></li>';
//            }
//            else
//            {
//                echo'<li><a href="../course/view.php?id='.$course->id.'&page='.$num.'">'.$num.'</a></li>';
//            }
            if($num == $current_page)
            {
                //  这里需要修改样式标示当前页
                echo'<li><a class="pagination_li_active" href="../course/view.php?id='.$course->id.'&page='.$num.'">'.$num.'</a></li>';
            }
            else
            {
                echo'<li><a href="../course/view.php?id='.$course->id.'&page='.$num.'">'.$num.'</a></li>';
            }
        }
    }

    //   获取课程评价 20160729
    public function my_get_course_evaluation2($course, $current_page)
    {
        $my_page = $current_page * 10;
        global $DB;
        global $OUTPUT;
        $evaluation = $DB->get_records_sql('SELECT a.id, userid, comment, b.firstname, b.lastname, commenttime FROM mdl_comment_course_my a JOIN mdl_user b ON a.userid = b.id WHERE courseid = ? ORDER BY commenttime DESC LIMIT '.$my_page.',10', array($course->id));

        $output = '';
        foreach($evaluation as $value)
        {
            $userobject = new stdClass();
            $userobject->metadata = array();
            $user = $DB->get_record('user', array('id' => $value->userid), '*', MUST_EXIST);
            $userobject->metadata['useravatar'] = $OUTPUT->user_picture (
                $user,
                array(
                    'link' => false,
                    'visibletoscreenreaders' => false
                )
            );

            $userobject->metadata['useravatar'] = str_replace("width=\"35\" height=\"35\"", " ", $userobject->metadata['useravatar']);
            $output .= '
                 <div class="evaluation">
                      <div class="evaluation-con">
                         <a href="#" class="img-box">
                         <!--<img src="../theme/more/pix/courseforstu/545867340001101702200220-100-100.jpg" alt="星海里的时光">-->
                         '.$userobject->metadata['useravatar'].'
                         </a>
                             <div class="content-box">
                                 <div class="user-info clearfix">
                                    <a href="#" class="username">'.$value->lastname.$value->firstname.'</a>
                                 </div>
                                 <!--user-info end-->
                             <p class="content">'.$value->comment.'</p>
                                 <div class="info">
                                 <span class="time">时间：'.userdate($value->commenttime,'%Y-%m-%d %H:%M').'</span>
                                 </div>
                             </div>
                      <!--content end-->
                      </div>
        <!--evaluation-con end-->
                 </div>
        ';
        }
        return $output;
    }

    //   获取课程评价
    public function my_get_course_evaluation($course, $current_page)
    {
        $my_page = $current_page * 10;
        global $DB;
        global $OUTPUT;
        $evaluation = $DB->get_records_sql('SELECT a.id, userid, comment, b.firstname, b.lastname, commenttime FROM mdl_comment_course_my a JOIN mdl_user b ON a.userid = b.id WHERE courseid = ? ORDER BY commenttime DESC LIMIT '.$my_page.',10', array($course->id));

        foreach($evaluation as $value)
        {
            $userobject = new stdClass();
            $userobject->metadata = array();
            $user = $DB->get_record('user', array('id' => $value->userid), '*', MUST_EXIST);
            $userobject->metadata['useravatar'] = $OUTPUT->user_picture (
                $user,
                array(
                    'link' => false,
                    'visibletoscreenreaders' => false
                )
            );

            $userobject->metadata['useravatar'] = str_replace("width=\"35\" height=\"35\"", " ", $userobject->metadata['useravatar']);
            echo '
                 <div class="evaluation">
                      <div class="evaluation-con">
                         <a href="#" class="img-box">
                         <!--<img src="../theme/more/pix/courseforstu/545867340001101702200220-100-100.jpg" alt="星海里的时光">-->
                         '.$userobject->metadata['useravatar'].'
                         </a>
                             <div class="content-box">
                                 <div class="user-info clearfix">
                                    <a href="#" class="username">'.$value->lastname.$value->firstname.'</a>
                                 </div>
                                 <!--user-info end-->
                             <p class="content">'.$value->comment.'</p>
                                 <div class="info">
                                 <span class="time">时间：'.userdate($value->commenttime,'%Y-%m-%d %H:%M').'</span>
                                 </div>
                             </div>
                      <!--content end-->
                      </div>
        <!--evaluation-con end-->
                 </div>
        ';
        }
    }

    /** START 朱子武 获取点赞数 20160227*/
    function  my_get_likecount()
    {
        global $DB;
        $myurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $mylikecountarray = $DB->get_records_sql('SELECT id, url, likecount FROM mdl_course_like_count_my WHERE url = ?', array($myurl));
        $mylikecount = 0;
        foreach($mylikecountarray as $value)
        {
            $mylikecount = $value->likecount;
        }
        echo'<p>当前页面'.$mylikecount.'人点赞</p>';
    }

    /** START  获取课程评分 20160729 */
    function my_get_course_score2($course)
    {
        global $DB;
        $mysumscore = 0.0;
        $myscore = $DB->get_records_sql('SELECT id, sumscore FROM mdl_score_course_sum_my WHERE courseid = ?', array($course->id));
        if(count($myscore))
        {
            foreach($myscore as $value)
            {
                $mysumscore = $value->sumscore;
            }
        }else
        {
            $mysumscore = 10.0;
        }
        return $mysumscore;
    }
    /** ---END---*/

    /** START 朱子武 获取课程评分 20160226*/
    function my_get_course_score($course)
    {
        global $DB;
        $mysumscore = 0.0;
        $myscore = $DB->get_records_sql('SELECT id, sumscore FROM mdl_score_course_sum_my WHERE courseid = ?', array($course->id));
        if(count($myscore))
        {
            foreach($myscore as $value)
            {
                $mysumscore = $value->sumscore;
            }
            echo'<p style="color: #777777; font-size: 30px;">'.$mysumscore.'</p>';
        }
        else
        {
            echo'<p style="color: #777777; font-size: 30px;">10.0</p>';
            $mysumscore = 10.0;
        }
        return $mysumscore;
    }
    /** ---END---*/

    /** Start 获取继续学习数据  20160729 */
    function my_get_continue_video2($course){
        global $DB;
        global $CFG;
        global $USER;
        $videoids = $DB->get_records_sql('select id from mdl_course_modules where course='.$course->id.' and module=13');
        $videoseeks =  $DB->get_records_sql('select * from mdl_videoseek_my where userid='.$USER->id.' ORDER BY timecreated DESC');
        foreach($videoseeks as $videoseek){
            foreach($videoids as $videoid){
                if($videoseek->videourl == $CFG->wwwroot."/mod/lesson/view.php?id=".$videoid->id ){
                    return '<a href="'.$videoseek->videourl.'" class="continue-btn" >继续学习</a>';
                }
            }
        }
    }
    /*End */

	/** Start 岑霄 获取继续学习数据 */
	function my_get_continue_video($course){
		global $DB;
		global $CFG;
		global $USER;
		$videoids = $DB->get_records_sql('select id from mdl_course_modules where course='.$course->id.' and module=13');
		$videoseeks =  $DB->get_records_sql('select * from mdl_videoseek_my where userid='.$USER->id.' ORDER BY timecreated DESC');
		foreach($videoseeks as $videoseek){
			foreach($videoids as $videoid){
				if($videoseek->videourl == $CFG->wwwroot."/mod/lesson/view.php?id=".$videoid->id ){
					echo '<a href="'.$videoseek->videourl.'"><button class="btn btn-info" style="width: 89%; height: 36px; margin-bottom: 20px;">继续学习</button></a>';
					return;
				}
			}
		}
	}
	/*End */

    /** Start 课程排名 徐东威 20160407
     * @param $courseid 课程ID
     * @return
     */
    function rankCourse($courseid,$userID){

        global $DB;

        $records = $DB->get_records_sql("select * from mdl_course_complete_rank_my c where c.courseid = $courseid ORDER BY c.complete_count desc,c.complete_time asc ");
        $num = 1;//名次
        $showRank =  array();
        $myRank = 0;
        foreach($records as $record){
            if($num < 4){//前三名
                $user = $DB->get_record_sql("select * from mdl_user where id = $record->userid");
                $showRank[$num] = $user;
            }elseif($myRank != 0){//如果当前用户在前三名中
                break;
            }
            if($userID == $record->userid){
                $myRank = $num;//当前用户的名次
                if($num >= 3){
                    break;
                }
            }
            $num++;
        }
        $rankResult = new stdClass();
        $rankResult->showRank = $showRank;
        $rankResult->myRank = $myRank;
        return $rankResult;
    }
    /**End */

    /**输出退课按钮
     * //nlw
     * @param $course
     */
    public function my_print_exitcouse($course){
        global $DB;
        global $USER;
        $output = '';

        //查询当前课程是否为当前学生通过自助选课选的课
        $user_enrol_my = $DB->get_record_sql('select * from mdl_user_enrolments where userid ='.$USER->id.' and enrolid in (select id from mdl_enrol where courseid='.$course->id.' and enrol=\'self\')');

        //查询当前学生台账任务中的必修课的required_course_id
        $sql_my = 'select GROUP_CONCAT(required_course_id) as required_course_id_str from mdl_mission_my where id in (select mission_id from mdl_mission_user_my where user_id = '.$USER->id.');';
        $required_course_id = $DB->get_record_sql($sql_my);

        if (!empty($required_course_id->required_course_id_str)){
            $reuired_course_arr = explode(',',$required_course_id->required_course_id_str);
            if (!in_array($course->id,$reuired_course_arr) && $user_enrol_my){//不是必修课且是通过自助选课选的课就输出退课按钮
                $currenturl = new moodle_url('/enrol/self/unenrolself.php?enrolid='.$user_enrol_my->enrolid);
//                $output = '<a href="'.$currenturl.'"><button class="btn btn-danger" style="width: 89%; height: 36px; margin-bottom: 20px;">退课</button></a>';
                $output = '<a href="'.$currenturl.'" class="tuike-btn"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;退课</a>';
            }
        }
        return $output;
    }
    /** 输出退课按钮 end */

}
