<?php 
//成功返回消息
/**Start cx 增加$forwardUrl参数，为审核成功后跳到指定页面 20160723*/
function success($message,$navTabId,$callbackType,$forwardUrl=null){
    echo '{
		"statusCode":"200",
		"message":"'.$message.'",
		"navTabId":"'.$navTabId.'",
		"rel":"",
		"callbackType":"'.$callbackType.'",
		"forwardUrl":"'.$forwardUrl.'",
		"confirmMsg":""
	}';
}
/**End cx 增加$forwardUrl参数，为审核成功后跳到指定页面 20160723*/
//失败返回消息
function failure($message){
    echo '{
		"statusCode":"300",
		"message":"'.$message.'",
		"navTabId":"",
		"rel":"",
		"callbackType":"",
		"forwardUrl":"",
		"confirmMsg":""
	}';
}
