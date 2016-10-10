<style>
    .bookclassified .main .more {float: right; font-size: 12px; padding: 6px 10px;cursor: pointer; color: #F0F0F0;}
    .bookclassified .main .more .glyphicon {top: 0px; margin-left: 5px; margin-top: -4px;}
    .bookclassified .main .more:hover{color: #fe8358}
    .bookclassified .main .more.active{color: #fe8358}
    .bookclassified .main .more .span_box {display: block;padding-top:3px;float:right;width: 14px; height: 100%;line-height: 0px;}
    .bookclassified-more {width: 100%; position: absolute; height: 200px; background-color: rgba(36,97,158,0.8);z-index: 10;display: none;}
    .bookclassified-more .main {width: 1200px; margin: auto; padding: 15px 0px !important; line-height: 30px; min-height: inherit !important;}
    .bookclassified-more .main a {text-decoration: none;color: #a2c9f0;padding: 3px 32px; font-size: 16px; float: left}
    .bookclassified-more .main a:hover{color: #FFFFFF ;text-decoration: underline;}
</style>
<script>
    $(document).ready(function() {

        $('#more-type').mouseover(function(){
            $('.bookclassified-more').show();
            $('#more-type').addClass('active');
        })

        $('.bookclassified-more').mouseover(function(){
            $('.bookclassified-more').show();
        })

        $('.bookclassified-more').mouseout(function(){
            $('.bookclassified-more').hide();
            $('#more-type').removeClass('active');
        })
    });
</script>

<?php
//微阅》书库》书籍分类
global $USER;
global $DB;
//获取电子书的顶级分类
$bookclasses = $DB->get_records_sql("select e.id,e.name from mdl_ebook_categories_my e where e.parent = 0");

$html = '<div class="bookclassified">
            <div class="bookclassified-center main">';
$html_temp = '';
if($bookclasses != null){
    $html_temp .= '<div class="bookclassified-more">
                        <div class="main">';
    $n = 1;
    foreach($bookclasses as $bookclass){
        if($n<7) {
            $html .= '<div class="line"></div>
                          <a href="classify.php?bookclassid=' . $bookclass->id . '" class="kinds">' . $bookclass->name . '</a>';
            $n++;
        }
        $html_temp .=  '<a href="classify.php?bookclassid=' . $bookclass->id . '" class="kinds">' . $bookclass->name . '</a>';
    }
    $html .= '<a id="more-type" class="more">更多<span class="span_box"><span class="glyphicon glyphicon-chevron-down"></span><span class="glyphicon glyphicon-chevron-down"></span></a>';
    $html_temp .= '    </div>
                    </div>';
}
$html .= '    </div>
        </div>';
$html .= $html_temp;

echo $html;

?>





<!--***********************************************************-->
<!--<div class="bookclassified">-->
<!--    <div class="bookclassified-center">-->
        <!-- 书本分类按钮 -->
        <!--				<div class="btn-group" style="float: left;">-->
        <!--				  	<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
        <!--				  		<img src="../img/tushuFenlei.png">-->
        <!--				  	</a>-->
        <!--				  	<ul class="dropdown-menu">-->
        <!--				    	<li><a href="#">现代</a></li>-->
        <!--				    	<li role="separator" class="divider"></li>-->
        <!--				    	<li><a href="#">军事</a></li>-->
        <!--				    	<li role="separator" class="divider"></li>-->
        <!--				    	<li><a href="#">战争</a></li>-->
        <!--				    	<li role="separator" class="divider"></li>-->
        <!--				    	<li><a href="#">科技</a></li>-->
        <!--				  	</ul>-->
        <!--				</div>-->
        <!-- 书本分类按钮 end-->
<!--        --><?php
//        if($bookclasses != null){
//            foreach($bookclasses as $bookclass){
//                echo '<div class="line"></div>
//                          <a href="classify.php?bookclassid='.$bookclass->id.'" class="kinds">'.$bookclass->name.'</a>';
//            }
//        }
//        ?>
<!--    </div>-->
<!--</div>-->