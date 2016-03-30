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
 * Renderers for outputting blog data
 *
 * @package    core_blog
 * @subpackage blog
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Blog renderer
 */
class core_blog_renderer extends plugin_renderer_base {
	 /**  START  添加JavaScript样式  朱子武 20160308*/
    function render_blog_script()
    {
//        $str = $_SERVER["PHP_SELF"];
//        $str = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];
//        echo $str;
        return'<script type="text/javascript">
            function followBlog(obj)
            {
            var btn = document.getElementById("followBlog-btn");//根据id获取button节点
                $.ajax({
                    url: "../blog/blogbackground.php",
                    data: {concernid: obj.value, type:"concern" },
                    success: function(msg){
                        if(msg=="1"){
							//alert("关注成功");
							location.reload();
						}
						else if(msg=="2"){
							location.reload();
						}
						else
						{
							alert("关注失败");
						}
                    }
                });
            }

//            点赞
            function likeBlog(value)
            {
                var btn = document.getElementById("likeBlog-btn");//根据id获取button节点
                $.ajax({
                    url: "../blog/blogbackground.php",
                    data: {blogid: value, relatedtype:"3", type:"related" },
                    success: function(msg){
                        if(msg=="1"){
                            // alert("点赞成功");
                            location.reload();
                        }
                        else{
                            msg=="2" ? alert("你已经为它点赞了，无需再次点赞") :alert("点赞失败");
                        }
                    }
                });
            }

//            转发
            function forwardedBlog(value)
            {
                //alert(btn.value);
                //var btn = document.getElementById("forwardedBlog-btn");//根据id获取button节点
                //alert(btn.value);
                $.ajax({
                    url: "../blog/blogbackground.php",
                    data: {blogid: value, relatedtype:"2", type:"related" },
                    success: function(msg){
                        if(msg=="1"){
                            // alert("转发成功");
                            location.reload();
                        }

                    }
                });
            }
            </script>';
    }	
    /**  END  添加JavaScript样式  朱子武 20160308*/
	
	/**  START  获取是否已关注 朱子武 20160311*/
		function follow_or_not($concernid)
		{
			global $DB;
			global $USER;
			$res = '';
			$myresult = $DB->get_records_sql('SELECT id FROM mdl_blog_concern_me_my WHERE concernid = ? AND userid = ?', array($concernid,$USER->id));
			if(count($myresult))
			{
				$res = '取消关注';
			}
			else
			{
				$res = '关注此用户';
			}
			return $res;
		}
	/**  END  获取是否已关注 朱子武 20160311*/

    /**  START  添加点赞数  朱子武 20160309*/
    function my_get_blog_like_count($blogid)
    {
        global $DB;
        $res = '0';
        $mylikecount = $DB->get_records_sql('SELECT id, blogid, likecount FROM mdl_blog_like_count_my WHERE blogid = ?', array($blogid));
        foreach($mylikecount as $value)
        {
            $res = $value->likecount;
        }
        return $res;
    }
    /**  END  添加点赞数  朱子武 20160309*/

    /**  START  添加转发数  朱子武 20160309*/
    function my_get_blog_forwarded_count($blogid)
    {
        global $DB;
        $res = '0';
        $originalid = '';
        $my_original = $DB->get_records_sql('SELECT id, originalblogid FROM mdl_post WHERE id = '.$blogid);
        foreach($my_original as $value)
        {
            $originalid = $value->originalblogid;
        }

        if($originalid)
        {
            $my_original_count = $DB->get_records_sql('SELECT id, blogid, forwardedcount FROM mdl_blog_forwarded_count_my WHERE blogid = '.$originalid);
            foreach($my_original_count as $original_value)
            {
                $res = $original_value->forwardedcount;
            }
        }

        return $res;
    }
    /**  END  添加转发数  朱子武 20160309*/
	
    /**
     * Renders a blog entry
     *
     * @param blog_entry $entry
     * @return string The table HTML
     */
    public function render_blog_entry(blog_entry $entry) {

        global $CFG;

        $syscontext = context_system::instance();

        $stredit = get_string('edit');
        $strdelete = get_string('delete');

        // Header.
        $mainclass = 'forumpost blog_entry blog clearfix ';
        if ($entry->renderable->unassociatedentry) {
            $mainclass .= 'draft';
        } else {
            $mainclass .= $entry->publishstate;
        }
		
		$o = "";
//        添加JavaScript事件处理  朱子武 20160308
        $o .= $this->render_blog_script();
        $o .= $this->output->container_start($mainclass, 'b' . $entry->id);
        $o .= $this->output->container_start('row header clearfix');

        // User picture.
        $o .= $this->output->container_start('left picture header');
        $o .= $this->output->user_picture($entry->renderable->user);
        $o .= $this->output->container_end();

        $o .= $this->output->container_start('topic starter header clearfix');

        // Title.
        $titlelink = html_writer::link(new moodle_url('/blog/index.php',
                                                       array('entryid' => $entry->id)),
                                                       format_string($entry->subject));
        $o .= $this->output->container($titlelink, 'subject');
		// 添加关注 朱子武  20160309
		if($this->follow_or_not($entry->userid)=='关注此用户'){
			 $o .= "<button id='followBlog-btn' value='".$entry->userid."' class='btn btn-info' onclick='followBlog(this)'>".$this->follow_or_not($entry->userid)."</button>";
		}
		else{
			 $o .= "<button id='followBlog-btn' value='".$entry->userid."' class='btn btn-default' onclick='followBlog(this)'>".$this->follow_or_not($entry->userid)."</button>";
		}
       
        // Post by.
        $by = new stdClass();
        $fullname = fullname($entry->renderable->user, has_capability('moodle/site:viewfullnames', $syscontext));
        $userurlparams = array('id' => $entry->renderable->user->id, 'course' => $this->page->course->id);
        $by->name = html_writer::link(new moodle_url('/user/view.php', $userurlparams), $fullname);

        $by->date = userdate($entry->created);
        $o .= $this->output->container(get_string('bynameondate', 'forum', $by), 'author');

        // Adding external blog link.
        if (!empty($entry->renderable->externalblogtext)) {
            $o .= $this->output->container($entry->renderable->externalblogtext, 'externalblog');
        }

        // Closing subject tag and header tag.
        $o .= $this->output->container_end();
        $o .= $this->output->container_end();

        // Post content.
        $o .= $this->output->container_start('row maincontent clearfix');

        // Entry.
        $o .= $this->output->container_start('no-overflow content ');

        // Determine text for publish state.
        switch ($entry->publishstate) {
            case 'draft':
                $blogtype = get_string('publishtonoone', 'blog');
                break;
            case 'site':
                $blogtype = get_string('publishtosite', 'blog');
                break;
            case 'public':
                $blogtype = get_string('publishtoworld', 'blog');
                break;
            default:
                $blogtype = '';
                break;

        }
        $o .= $this->output->container($blogtype, 'audience');

        // Attachments.
        $attachmentsoutputs = array();
        if ($entry->renderable->attachments) {
            foreach ($entry->renderable->attachments as $attachment) {
                $o .= $this->render($attachment, false);
            }
        }

        // Body.
        $o .= format_text($entry->summary, $entry->summaryformat, array('overflowdiv' => true));

        if (!empty($entry->uniquehash)) {
            // Uniquehash is used as a link to an external blog.
            $url = clean_param($entry->uniquehash, PARAM_URL);
            if (!empty($url)) {
                $o .= $this->output->container_start('externalblog');
                $o .= html_writer::link($url, get_string('linktooriginalentry', 'blog'));
                $o .= $this->output->container_end();
            }
        }

        // Links to tags.
        $officialtags = tag_get_tags_csv('post', $entry->id, TAG_RETURN_HTML, 'official');
        $defaulttags = tag_get_tags_csv('post', $entry->id, TAG_RETURN_HTML, 'default');

        if (!empty($CFG->usetags) && ($officialtags || $defaulttags) ) {
            $o .= $this->output->container_start('tags');

            if ($officialtags) {
                $o .= get_string('tags', 'tag') .': '. $this->output->container($officialtags, 'officialblogtags');
                if ($defaulttags) {
                    $o .=  ', ';
                }
            }
            $o .=  $defaulttags;
            $o .= $this->output->container_end();
        }

        // Add associations.
        if (!empty($CFG->useblogassociations) && !empty($entry->renderable->blogassociations)) {

            // First find and show the associated course.
            $assocstr = '';
            $coursesarray = array();
            foreach ($entry->renderable->blogassociations as $assocrec) {
                if ($assocrec->contextlevel ==  CONTEXT_COURSE) {
                    $coursesarray[] = $this->output->action_icon($assocrec->url, $assocrec->icon, null, array(), true);
                }
            }
            if (!empty($coursesarray)) {
                $assocstr .= get_string('associated', 'blog', get_string('course')) . ': ' . implode(', ', $coursesarray);
            }

            // Now show mod association.
            $modulesarray = array();
            foreach ($entry->renderable->blogassociations as $assocrec) {
                if ($assocrec->contextlevel ==  CONTEXT_MODULE) {
                    $str = get_string('associated', 'blog', $assocrec->type) . ': ';
                    $str .= $this->output->action_icon($assocrec->url, $assocrec->icon, null, array(), true);
                    $modulesarray[] = $str;
                }
            }
            if (!empty($modulesarray)) {
                if (!empty($coursesarray)) {
                    $assocstr .= '<br/>';
                }
                $assocstr .= implode('<br/>', $modulesarray);
            }

            // Adding the asociations to the output.
            $o .= $this->output->container($assocstr, 'tags');
        }

        if ($entry->renderable->unassociatedentry) {
            $o .= $this->output->container(get_string('associationunviewable', 'blog'), 'noticebox');
        }

        // Commands.
        $o .= $this->output->container_start('commands');
        if ($entry->renderable->usercanedit) {

            // External blog entries should not be edited.
            if (empty($entry->uniquehash)) {
                $o .= html_writer::link(new moodle_url('/blog/edit.php',
                                                        array('action' => 'edit', 'entryid' => $entry->id)),
                                                        $stredit) . ' | ';
            }
            $o .= html_writer::link(new moodle_url('/blog/edit.php',
                                                    array('action' => 'delete', 'entryid' => $entry->id)),
                                                    $strdelete) . ' | ';
        }
		
		// 添加转发和点赞 朱子武  20160309
        //$o .= "<button id='forwardedBlog-btn' value='".$entry->id."' onclick='forwardedBlog(this)'>转发(".$this->my_get_blog_forwarded_count($entry->id).")</button>";
        //$o .= "<button id='likeBlog-btn' value='".$entry->id."' onclick='likeBlog(this)'>喜欢(".$this->my_get_blog_like_count($entry->id).")</button>";
        $o .= '<a id="forwardedBlog-btn" onclick="forwardedBlog('.$entry->id.')" style="cursor:pointer">转发('.$this->my_get_blog_forwarded_count($entry->id).')</a> | ';
        $o .= '<a id="likeBlog-btn" onclick="likeBlog('.$entry->id.')" style="cursor:pointer">喜欢('.$this->my_get_blog_like_count($entry->id).')</a>';
		
		//注释 永久链接
        // $entryurl = new moodle_url('/blog/index.php', array('entryid' => $entry->id));
        // $o .= html_writer::link($entryurl, get_string('permalink', 'blog'));

        $o .= $this->output->container_end();

        // Last modification.
        if ($entry->created != $entry->lastmodified) {
            $o .= $this->output->container(' [ '.get_string('modified').': '.userdate($entry->lastmodified).' ]');
        }

        // Comments.
        if (!empty($entry->renderable->comment)) {
            $o .= $entry->renderable->comment->output(true);
        }

        $o .= $this->output->container_end();

        // Closing maincontent div.
        $o .= $this->output->container('&nbsp;', 'side options');
        $o .= $this->output->container_end();

        $o .= $this->output->container_end();

        return $o;
    }

    /**
     * Renders an entry attachment
     *
     * Print link for non-images and returns images as HTML
     *
     * @param blog_entry_attachment $attachment
     * @return string List of attachments depending on the $return input
     */
    public function render_blog_entry_attachment(blog_entry_attachment $attachment) {

        $syscontext = context_system::instance();

        // Image attachments don't get printed as links.
        if (file_mimetype_in_typegroup($attachment->file->get_mimetype(), 'web_image')) {
            $attrs = array('src' => $attachment->url, 'alt' => '');
            $o = html_writer::empty_tag('img', $attrs);
            $class = 'attachedimages';
        } else {
            $image = $this->output->pix_icon(file_file_icon($attachment->file),
                                             $attachment->filename,
                                             'moodle',
                                             array('class' => 'icon'));
            $o = html_writer::link($attachment->url, $image);
            $o .= format_text(html_writer::link($attachment->url, $attachment->filename),
                              FORMAT_HTML,
                              array('context' => $syscontext));
            $class = 'attachments';
        }

        return $this->output->container($o, $class);
    }
}
