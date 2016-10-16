<?php
/**
 * 时间控件样式
 */
global $CFG;
?>

<!--Start 时间日期控件  xdw -->
<link rel="stylesheet" href="<?php echo $CFG->wwwroot; ?>/privatecenter/mybookdata/time_plug/css/jquery-ui.css" />
<style type="text/css">
    .dropdownlist1 {display: none;}
    .dropdownlist1 a{color:#007bc4/*#424242*/; text-decoration:none;}
    .dropdownlist1 a:hover{text-decoration:underline}
    .dropdownlist1 ol,ul{list-style:none}
    .dropdownlist1 {font:12px/18px Tahoma, Helvetica, Arial, Verdana, "\5b8b\4f53", sans-serif; color:#51555C;}
    .dropdownlist1 img{border:none}
    .dropdownlist1 input{width:140px; height:30px; line-height:20px; padding:2px; border:1px solid #d3d3d3}
    .dropdownlist1 pre{padding:6px 0 0 0; color:#666; line-height:20px; background:#f7f7f7}

    .ui-timepicker-div .ui-widget-header { margin-bottom: 8px;}
    .ui-timepicker-div dl { text-align: left; }
    .ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
    .ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
    .ui-timepicker-div td { font-size: 90%; }
    .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
    .ui_tpicker_hour_label,.ui_tpicker_minute_label,.ui_tpicker_second_label,.ui_tpicker_millisec_label,.ui_tpicker_time_label{padding-left:20px}
    .dropdownlist1 {  float: left;  width: 210px;  height: 36px; margin-right: 10px; }
    .timetitle{float: left; font-size: 14px; margin-top: 5px}
</style>

<script type="text/javascript" >

    jqmin(document).ready(function(){
        jqmin('#start_time').datetimepicker({
            showSecond: true,
//			showMillisec: true,//显示毫秒
            timeFormat: 'hh:mm:ss'
        });
        jqmin('#end_time').datetimepicker({
            showSecond: true,
//			showMillisec: true,//显示毫秒
            timeFormat: 'hh:mm:ss'
        });
    });

    var start_time = 0;
    var end_time = 0;
    var time_flag = false;//时间控件标志
    var index_flag = 1;//1:全部课程 2：单课程  3：微阅统计
    //显示时间控件
    $("#show_time_btn").on('click',function(){
        time_flag = true;
        $(".dropdownlist1").show();
        $("#check_time_btn").show();
        $("#show_time_btn").hide();
    });

    //Start 时间字符串转换为时间戳 dateStr = 2016-05-25 12:12:12 xdw
    function get_unix_time(dateStr)
    {
        var newstr = dateStr.replace(/-/g,'/');
        var date =  new Date(newstr);
        var time_str = date.getTime().toString();
        return time_str.substr(0, 10);
    }
    //end 时间字符串转换为时间戳 dateStr = 2016-05-25 12:12:12 xdw

    //用选定时间查询
    $("#check_time_btn").on('click',function(){

        start_time = $('#start_time').val();//开始时间
        end_time = $('#end_time').val();//结束时间

        if (start_time != '') {
            start_time = get_unix_time(start_time);
        } else {
            start_time = 0;
        }
        if (end_time != '') {
            end_time = get_unix_time(end_time);
        } else {
            end_time = 0;
        }
//        console.log(start_time + "--" + end_time);
        if ((start_time != 0 && end_time != 0) && (start_time >= end_time)) {
            alert('请确认结束时间大于开始时间！');
            return;
        }

        $('.lockpage').show();
        var newUrl = "";
        if(index_flag==1){
            newUrl = "../privatecenter/mybookdata/course_index.php?start_time="+start_time+"&end_time="+end_time;
            $(".kinds-a").removeClass("a-active");
            $("#course_statistic").addClass("a-active");
        }else if(index_flag==2){
            newUrl = "../privatecenter/mybookdata/index_course.php?courseid="+courseid+"&start_time="+start_time+"&end_time="+end_time;
            $(".kinds-a").removeClass("a-active");
            $("#course_statistic").addClass("a-active");
        }
        else if(index_flag==3){
            newUrl = "../privatecenter/mybookdata/microread_index.php?start_time="+start_time+"&end_time="+end_time;
            $(".kinds-a").removeClass("a-active");
            $("#microread_statistic").addClass("a-active");
        }

//        console.log(newUrl);

        $('.maininfo-box-index').load(newUrl);
    });


</script>
