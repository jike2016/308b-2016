<?php
/**
 * 折线图 xdw
 */


/**
 * 输出折线图
 */
function echo_line_chart(){

    echo '<div style="clear: both;"></div>
            <div class="learningsituation-box">
            <!--折线图-->
            <h5>单位：课时</h5>
            <div id="Histogram" style="width: 100%; height: 400px; margin: 0 auto"></div>
            <!--折线图 end-->
            </div>';

}

?>


<!--折线图-->
<script type="text/javascript">
    $(function() {
        $('#Histogram').highcharts({

            title: {   //正标题
                text: ' '
            },
            subtitle: { //副标题
                text: ' '
            },
            xAxis: {  //X轴文本
                categories: [<?php echo $Histogram_data[0];?>],
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: { //Y轴提示
                    text: '',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {  //数据点提示
                valueSuffix: ''
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            credits: {
                enabled: false
            },
            series: [{ //数据
                name: '学习课时',
                data: [<?php echo $Histogram_data[1];?>]
            }]
        });
    });
</script>
<!--折线图-->

