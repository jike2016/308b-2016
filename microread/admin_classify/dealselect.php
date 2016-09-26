<?php //处理综合查询将每一查询sql语句连接起来
function join_sql_select($select_sqls){
    $sql='where ';
    $j=count($select_sqls);
    $i=1;
    foreach ($select_sqls as $select_sql) {
        $sql.=$select_sql;
        if($i<$j){
            $sql.=' and ';
        }
        $i++;
    }
    return $sql;
}

?>