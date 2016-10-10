<style>
    .bookclassified .main .more {float: right; font-size: 12px; padding: 6px 10px;cursor: pointer; color: #F0F0F0;}
    .bookclassified .main .more .glyphicon {top: 0px; margin-left: 5px; margin-top: -4px;}
    .bookclassified .main .more:hover{color: #fe8358}
    .bookclassified .main .more.active{color: #fe8358}
    .bookclassified .main .more .span_box {display: block;padding-top:3px;float:right;width: 14px; height: 100%;line-height: 0px;}
    .bookclassified-more {width: 100%; position: absolute; height: 200px; background-color: rgba(36,97,158,0.8);z-index: 10;display: none;}
    .bookclassified-more .main {width: 1200px; margin: auto; padding: 15px 0px; line-height: 30px;}
    .bookclassified-more .main a {text-decoration: none;color: #a2c9f0;padding: 3px 20px; font-size: 16px; float: left}
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
//微阅》文库》文档分类
global $DB;
//获取文档的顶级分类
$docclasses = $DB->get_records_sql("select * from mdl_doc_categories_my dc where dc.parent = 0");

$html = '<div class="bookclassified">
            <div class="bookclassified-center main">';
$html_temp = '';
if($docclasses != null){
    $html_temp .= '<div class="bookclassified-more">
                        <div class="main">';
    $n = 1;
    foreach($docclasses as $docclass){
        if($n<7) {
            $html .= '<div class="line"></div>
					<a href="classify.php?docclassid=' . $docclass->id . '" class="kinds">' . $docclass->name . '</a>';
            $n++;
        }
        $html_temp .= '<a href="classify.php?docclassid=' . $docclass->id . '" class="kinds">' . $docclass->name . '</a>';
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



<!--**********************************************************-->
<!--<div class="bookclassified">-->
<!--    <div class="bookclassified-center">-->
        <!-- 书本分类按钮 -->
        <!--		<div class="btn-group" style="float: left;">-->
        <!--			<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
        <!--				<img src="../img/tushuFenlei.png">-->
        <!--			</a>-->
        <!--			<ul class="dropdown-menu">-->
        <!--				<li><a href="#">现代</a></li>-->
        <!--				<li role="separator" class="divider"></li>-->
        <!--				<li><a href="#">军事</a></li>-->
        <!--				<li role="separator" class="divider"></li>-->
        <!--				<li><a href="#">战争</a></li>-->
        <!--				<li role="separator" class="divider"></li>-->
        <!--				<li><a href="#">科技</a></li>-->
        <!--			</ul>-->
        <!--		</div>-->
        <!-- 书本分类按钮 end-->
<!--        --><?php
//        if($docclasses != null){
//            foreach($docclasses as $docclass){
//                echo '<div class="line"></div>
//										<a href="classify.php?docclassid='.$docclass->id.'" class="kinds">'.$docclass->name.'</a>';
//            }
//        }
//        ?>
<!--    </div>-->
<!--</div>-->

