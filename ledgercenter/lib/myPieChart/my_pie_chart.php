
<?php
/**
 * 饼状图 xdw
 */




/**
 * 输出饼状图
 * @param array $datas 显示的分量数据集，其中元素对象包含'数量' (count) 和'名称'(name)
 * @param string $chartName 饼状图名称
 * @return int 反馈标志，反馈该数据是否能生成饼状图
 */
function echo_piechar(array $datas,$chartName='比例图：'){

    if(count($datas)==0){
        echo '</br></br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;暂无数据</br></br>';
        return 0;
    }
    else{
        $sumcount=0;
        $output = '<div style="clear:both;"></div>
                    <!--饼状图 第一个数字是总数-->
                    <div style="width: 100%; margin: 0 auto;">
                        <table id=\'piechart\'>
                            <caption>'.$chartName.'</caption>
                            <thead>
                                <tr>
                                    <th></th>';
        foreach($datas as $data){
            $sumcount+=$data->count;
            $output .= '<th>'.$data->name.'</th>';
        }

        $output .= '
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <th>'.$sumcount.'</th>';
        foreach($datas as $data){
            $output .= '<td>'.$data->count.'</td>';
        }
        $output .= '
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--饼状图 end-->
            ';
        echo $output;
        echo get_control_script();
        return 1;//有值
    }

}

/**
 * 获取控制饼状图的脚本
 * @return string
 */
function get_control_script(){
    $script = "<script>
                    gvChartInit();
                    $(document).ready(function() {
                        //饼状图
                        $('#piechart').gvChart({
                            chartType: 'PieChart',
                            gvSettings: {
                                vAxis: {
                                    title: 'No of players'
                                },
                                hAxis: {
                                    title: 'Month'
                                },
                                width: 700,
                                height: 400
                            }
                        });
                        //饼状图 end
                    });
                </script>";
    return $script;
}







