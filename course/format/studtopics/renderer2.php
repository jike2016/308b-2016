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
 * @package format_topics
 * @copyright 2012 Dan Poltawski
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.3
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/renderer.php');

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



    public function my_print_page($course, $sections, $mods, $modnames, $modnamesused) {
        echo '<!--新html静态页面 body 内容--><div id="main">';
		//输出课程信息course-infos
		$this->my_course_infos($course);
		echo '
			<div class="course-info-main clearfix w has-progress">
				<div class="content-wrap clearfix">
					<div class="content">
						<div class="classintroduce">
							<h3>课程介绍</h3>
							<p>本课程从最基本的概念开始讲起，步步深入，带领大家学习HTML、CSS样式基础知识，了解各种常用标签的意义以及基本用法，后半部分讲解CSS样式代码添加，为后面的案例课程打下基础。</p>
						</div>
						<div class="mod-tab-menu">
							<ul class="course-menu clearfix">
								<li><a id="zhangjie" class="active"  href="javascript:void(0);"><span>课程提纲</span></a></li>
								<li><a id="pingjia"  class="" href="javascript:void(0);"><span>课程评价</span></a></li>
							</ul>
						</div>

						<div class="mod-chapters">
							<!--第一章-->
							<div class="chapter ">
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

								</ul>
							</div>
							<!--第一章end-->

						</div>
						<!--mod-chapters 的 div end-->
						<div class="evaluation-list">
							<h3>课程评价</h3>
							<div class="evaluation-info clearfix">
								<!--p class="satisfaction">满意度评分：<em>9.8</em></p>
								<p>内容实用：9.9</p>
								<p>通俗易懂：9.6</p>
								<p>逻辑清晰：9.2</p>
								<p class="person_num"><em>346</em>位同学参与评价</p-->
							</div>
							<!--evaluation-info end-->
							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2675648" class="img-box">
										<img src="../theme/more/pix/courseforstu/533e564d0001308602000200-100-100.jpg" alt="anananan007">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2675648" class="username">anananan007</a>
										</div>
										<!--user-info end-->
										<p class="content"></p>
										<div class="info">
											<span class="time">时间：22分钟前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->
							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2670294" class="img-box">
										<img src="../theme/more/pix/courseforstu/545867340001101702200220-100-100.jpg" alt="星海里的时光">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2670294" class="username">星海里的时光</a>
										</div>
										<!--user-info end-->
										<p class="content">很容易懂，很适合初学者</p>
										<div class="info">
											<span class="time">时间：12小时前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/1874746" class="img-box">
										<img src="../theme/more/pix/courseforstu/5531e72000012a5705100400-100-100.jpg" alt="wenfor">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/1874746" class="username">wenfor</a>
										</div>
										<!--user-info end-->
										<p class="content">不错,只是基础!</p>
										<div class="info">
											<span class="time">时间：14小时前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2698676" class="img-box">
										<img src="../theme/more/pix/courseforstu/545865c30001a2d802200220-100-100.jpg" alt="mitan00">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2698676" class="username">mitan00</a>
										</div>
										<!--user-info end-->
										<p class="content"></p>
										<div class="info">
											<span class="time">时间：15小时前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2724397" class="img-box">
										<img src="../theme/more/pix/courseforstu/545847490001582602200220-100-100.jpg" alt="冰室">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2724397" class="username">冰室</a>
										</div>
										<!--user-info end-->
										<p class="content">内容通俗易懂，非常适合初学者。</p>
										<div class="info">
											<span class="time">时间：16小时前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2557905" class="img-box">
										<img src="../theme/more/pix/courseforstu/5646135500019f5707410741-100-100.jpg" alt="小小小IT">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2557905" class="username">小小小IT</a>
										</div>
										<!--user-info end-->
										<p class="content"></p>
										<div class="info">
											<span class="time">时间：16小时前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2266141" class="img-box">
										<img src="../theme/more/pix/courseforstu/55d2e5da0001f31306400640-100-100.jpg" alt="David璐">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2266141" class="username">David璐</a>
										</div>
										<!--user-info end-->
										<p class="content">很有新意，边学边操作，编辑器的界面也很舒服。</p>
										<div class="info">
											<span class="time">时间：19小时前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2719264" class="img-box">
										<img src="../theme/more/pix/courseforstu/5458621e0001bd7b02200220-100-100.jpg" alt="依米暖暖">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2719264" class="username">依米暖暖</a>
										</div>
										<!--user-info end-->
										<p class="content">好！！！！！！！！！！！！！！！！！！！！！</p>
										<div class="info">
											<span class="time">时间：22小时前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2458818" class="img-box">
										<img src="../theme/more/pix/courseforstu/545862770001a22702200220-100-100.jpg" alt="回眸一笑仙魔震">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2458818" class="username">回眸一笑仙魔震</a>
										</div>
										<!--user-info end-->
										<p class="content">棒棒哒</p>
										<div class="info">
											<span class="time">时间：1天前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2728550" class="img-box">
										<img src="../theme/more/pix/courseforstu/56777827000100ba05000526-100-100.jpg" alt="最亲爱的你">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2728550" class="username">最亲爱的你</a>
										</div>
										<!--user-info end-->
										<p class="content">可以 通俗易懂 边看编写很方便</p>
										<div class="info">
											<span class="time">时间：1天前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2708356" class="img-box">
										<img src="../theme/more/pix/courseforstu/533e4cbd00011ecc01000100-100-100.jpg" alt="大灰吃饭">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2708356" class="username">大灰吃饭</a>
										</div>
										<!--user-info end-->
										<p class="content">很好</p>
										<div class="info">
											<span class="time">时间：1天前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/1314320" class="img-box">
										<img src="../theme/more/pix/courseforstu/5606970c0001360f04000400-100-100.jpg" alt="雲中菩提">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/1314320" class="username">雲中菩提</a>
										</div>
										<!--user-info end-->
										<p class="content"></p>
										<div class="info">
											<span class="time">时间：1天前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/1257497" class="img-box">
										<img src="../theme/more/pix/courseforstu/54e1d2820001d5a301800180-100-100.jpg" alt="金戈大王">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/1257497" class="username">金戈大王</a>
										</div>
										<!--user-info end-->
										<p class="content">非常棒的教程</p>
										<div class="info">
											<span class="time">时间：1天前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/1272479" class="img-box">
										<img src="../theme/more/pix/courseforstu/54eed72800015cae01000100-100-100.jpg" alt="等待消散">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/1272479" class="username">等待消散</a>
										</div>
										<!--user-info end-->
										<p class="content">基础的知识点，便于新学的人，对于熟悉的人当作复习也是不错的选择。</p>
										<div class="info">
											<span class="time">时间：1天前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->

							<div class="evaluation">
								<div class="evaluation-con">
									<a href="http://www.imooc.com/space/u/uid/2431982" class="img-box">
										<img src="../theme/more/pix/courseforstu/56174ac90001043e01000100-100-100.jpg" alt="_No作NoDie_0">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="http://www.imooc.com/space/u/uid/2431982" class="username">_No作NoDie_0</a>
										</div>
										<!--user-info end-->
										<p class="content">就是视频少了一点，其他完美</p>
										<div class="info">
											<span class="time">时间：1天前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->
							<button class="btn btn-block btn-danger">查看更多评价</button>
						</div>
						<!--evaluation-list end-->

					</div>
					<!--content end-->

					<div class="aside r">
						<div class="bd">
							<div class="box mb40">
								<h4>教师信息</h4>
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

						</div>

                        <div class="courserecommend">
							<div class="courserecommend-title">
								<h2><span class="glyphicon glyphicon-bookmark"></span><p>&nbsp;相关课程推荐</p></h2>
							</div>
							<table class="table courserecommend-table">
								<tbody>
									<tr>
										<td><a href="#"><span class="glyphicon glyphicon-book"></span>&nbsp;高等数学</a></td>
									</tr>
									<tr>
										<td><a href="#"><span class="glyphicon glyphicon-book"></span>&nbsp;高等数学</a></td>
									</tr>
									<tr>
										<td><a href="#"><span class="glyphicon glyphicon-book"></span>&nbsp;高等数学</a></td>
									</tr>
									<tr>
										<td><a href="#"><span class="glyphicon glyphicon-book"></span>&nbsp;高等数学</a></td>
									</tr>
									<tr>
										<td><a href="#"><span class="glyphicon glyphicon-book"></span>&nbsp;高等数学</a></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="clear"></div>

			</div>

		</div>

		<div id="footer">
			<div class="waper">
				<div class="footerwaper clearfix">
					<div class="followus r">
					</div>
					<div class="footer_intro l">
						<div class="footer_link">
							<ul>
								<li><a href="http://www.imooc.com/" target="_blank">网站首页</a></li>
								<li><a href="http://www.imooc.com/about/job" target="_blank">人才招聘</a></li>
								<li> <a href="http://www.imooc.com/about/contact" target="_blank">联系我们</a></li>
								<li><a href="http://yun.imooc.com/" target="_blank">慕课云</a></li>
								<li><a href="http://www.imooc.com/about/us" target="_blank">关于我们</a></li>
								<li> <a href="http://www.imooc.com/about/recruit" target="_blank">讲师招募</a></li>
								<li> <a href="http://www.imooc.com/user/feedback" target="_blank">意见反馈</a></li>
								<li> <a href="http://www.imooc.com/about/friendly" target="_blank">友情链接</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="J_GotoTop" class="elevator">
			<a class="elevator-msg" href="#" target="_blank" id="feedBack"></a>
				<div class="elevator-app-box">
				</div>
			</a>
		</div>

		<!--script-->

		<div class="mask"></div>

<!--新新html静态页面 body 内容 end-->';
    }

	public function my_course_infos($course){
		echo '<div class="course-infos"  style="background-color: #5b5b5a;">
				<div class="w pr">

					<div class="banner-left">
						<img src="../theme/more/pix/courseforstu/1.jpg" />
					</div>

					<div class="banner-right">
						<div class="path">
							<a href="#">课程</a>
							<i class="path-split">\</i><a href="#">前端开发</a>
							<i class="path-split">\</i><a href="#">HTML/CSS</a>
							<i class="path-split">\</i><span>HTML+CSS基础课程</span>
						</div>

						<div class="hd">
							<h2 class="l">HTML+CSS基础课程</h2>
						</div>

						<div class="learnernum">
							<p class="p1">218056</p>
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
		return;
	}
}
