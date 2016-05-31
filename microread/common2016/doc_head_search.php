<?php
//微阅》文库》搜索栏
//除了搜索页面外，文库中的其余页面都将使用这个搜索栏
//因为搜索页面要保留搜索值
?>

<script>
    $(document).ready(function() {
        //搜索选项下拉框
        $('#searchtype a').click(function() {
            $('#searchtypebtn').text($(this).text());
            $('#searchtypebtn').append('<span class="caret"></span>');
        });
//				//单选组合
//				$("input[type='radio'][name='optionsRadios']").removeAttr("checked");
//				$("input[type='radio'][id='optionsRadios-"+searchDocType+"']").attr("checked","checked");

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
        var searchType = document.getElementById("searchtypebtn");//获取选项
        var searchParam = document.getElementById("searchParam");//获取查询参数
        var searchDocTypes = document.getElementsByName("optionsRadios");//获取文档类型
        var searchDocType = '';
        for(var i =0;i<searchDocTypes.length;i++){
            if(searchDocTypes[i].checked){
                searchDocType = searchDocTypes[i].value;
            }
        }
        window.location.href="searchresult.php?searchType="+searchType.textContent+"&searchParam="+searchParam.value+"&searchDocType="+searchDocType;
    }
</script>

<div class="header-banner">
    <a href="index.php"><img  src="../img/logo_WenKu.png"/></a>
    <!--搜索框组-->
    <div class="search-box">
        <div class="input-group">
            <div class="input-group-btn">
                <button type="button" id="searchtypebtn" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo ($searchType != '')?$searchType :'全部'; ?><span class="caret"></span></button>
                <ul id="searchtype" class="dropdown-menu">
                    <li><a href="#">全部</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#">标题</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#">上传者</a></li>
                </ul>
            </div><!-- /btn-group -->
            <input id="searchParam" type="text" class="form-control" >
        </div><!-- /input-group -->
        <button onclick="search()" id="search_btn" class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>

        <div id="searchDocType" class="radio">
            <label>
                <input type="radio" checked="checked" name="optionsRadios" id="optionsRadios-all" value="all">
                全部
            </label>
            <label>
                <input type="radio" name="optionsRadios" id="optionsRadios-doc" value="doc">
                DOC
            </label>
            <label>
                <input type="radio" name="optionsRadios" id="optionsRadios-ppt" value="ppt">
                PPT
            </label>
            <label>
                <input type="radio" name="optionsRadios" id="optionsRadios-txt" value="txt">
                TXT
            </label>
            <label>
                <input type="radio" name="optionsRadios" id="optionsRadios-pdf" value="pdf">
                PDF
            </label>
            <label>
                <input type="radio" name="optionsRadios" id="optionsRadios-xls" value="xls">
                XLS
            </label>
        </div>

    </div>
    <!--搜索框组 end-->
</div>
