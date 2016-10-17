<?php
/**
 * 时间控件
 * 1、在页面合适的地方引入此文件
 * 2、要引入样式控制文件 time_plug_style.php
 * 3、在页面引入jq 前引入time_plug_js.php 文件
 */
?>

<!--Start 添加时间日期控件 xwd-->
<div id="time_plug" style="float: right;">
    <div id="show_time_btn" style="float: left;color: #10adf3;margin-right: 10px"> &lt;&lt;选择时段</div>
    <div class="dropdownlist1" id="start_time_content">
        <div class="input-group">
            <div class="timetitle">开始时间：</div>
            <div style="float: right"><input type="text" id="start_time" /></div>
        </div>
    </div>
    <div class="dropdownlist1" id="end_time_content">
        <div class="input-group">
            <div  class="timetitle">结束时间：</div>
            <div style="float: right"><input type="text" id="end_time"  /></div>
        </div>
    </div>
    <button id="check_time_btn" style="float: right;padding: 4px 12px;margin-right: 10px;display: none;" class="btn btn-default">确定</button>
</div>
<!--End 添加时间日期控件 xwd-->
