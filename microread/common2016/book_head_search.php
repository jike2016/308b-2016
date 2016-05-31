<?php
//微阅》书库》搜索栏
//除了搜索页面外，书库中的其余页面都将使用这个搜索栏
//因为搜索页面要保留搜索值
?>

<script>
    //搜索选项下拉框
    $(document).ready(function() {
        $('#searchtype a').click(function() {
            $('#searchtypebtn').text($(this).text());
            $('#searchtypebtn').append('<span class="caret"></span>');
        });
    });
    //回车事件
    document.onkeydown = function (e) {
        var theEvent = window.event || e;
        var code = theEvent.keyCode || theEvent.which;
        if ( $('#searchParam').val() != '' && code == 13) {
            $("#search_btn").click();
        }
    }
    //搜索
    function search(){
        var searchType = document.getElementById("searchtypebtn");//获取查询参数
        var searchParam = document.getElementById("searchParam");//获取选项
        window.location.href="searchresult.php?searchType="+searchType.textContent+"&searchParam="+searchParam.value;
    }
</script>

<div class="header-banner">
    <a href="index.php"><img  src="../img/shuku_logo.png"/></a>
    <!--搜索框组-->
    <div class="search-box">
        <div class="input-group">
            <div class="input-group-btn">
                <button type="button" id="searchtypebtn" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">全部<span class="caret"></span></button>
                <ul id="searchtype" class="dropdown-menu">
                    <li><a id="bookall" href="#">全部</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a id="booktitle" href="#">标题</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a id="bookauthor" href="#">作者</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a id="bookuploader" href="#">上传者</a></li>
                </ul>
            </div><!-- /btn-group -->
            <input id="searchParam" type="text" class="form-control" >
        </div><!-- /input-group -->
        <button onclick="search()" id="search_btn" class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>

        <!--			    <div class="radio">-->
        <!--			  		<label>-->
        <!--			    		<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1">-->
        <!--			    		全部字段-->
        <!--			  		</label>-->
        <!--			  		<label>-->
        <!--			    		<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">-->
        <!--			    		标题-->
        <!--			  		</label>-->
        <!--			  		<label>-->
        <!--			    		<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">-->
        <!--			    		主讲人-->
        <!--			  		</label>-->
        <!--				</div>-->

    </div>
    <!--搜索框组 end-->
</div>

