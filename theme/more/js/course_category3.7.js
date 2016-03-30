// JavaScript Document
function sel(obj)//方向
{
	if(obj==null){
			window.location.href="index.php";
	}
	else{
			window.location.href="index.php?dep1categoryid="+obj;
	}

//ajaxpost方法刷新
/*  $.post("../theme/more/ajax/getpost.php", {categoryid:obj},
   function(data){
		//刷新分类
		//alert("Data Loaded: " + data);
		var s=document.getElementById('subsecond2');
		var t=s.childNodes.length;
		alert(t);
		for (var i=0;i<t;i++){
			//s.removeChild(s.childNodes[i]);
			alert(s.childNodes[i].text);
		}
   }); */
}


function sel2(obj)//分类，无方向
{
	//获取category id 并局部刷新页面
	//var currentUrl = this.location.href;
	//alert(currentUrl+'?categoryid='+obj);
	window.location.href="index.php?dep2categoryid="+obj;
}
function sel3(parentid,subid)//分类，有方向
{
	//获取category id 并局部刷新页面
	//var currentUrl = this.location.href;
	//alert(currentUrl+'?categoryid='+obj);
	window.location.href="index.php?dep1categoryid="+parentid+"&dep2categoryid="+subid;
}
function sel4()//方向：全部，直接刷新
{
	//获取category id 并局部刷新页面
	//var currentUrl = this.location.href;
	//alert(currentUrl+'?categoryid='+obj);
	window.location.href="index.php";
}
function sel5(sortkind)//排序
{
	//alert(this.location.href);
	//获取category id 并局部刷新页面
	var currentUrl = this.location.href;
	if(currentUrl.indexOf("?")<0){//无参
		newurl=currentUrl+"?sortkind="+sortkind;
	}
	else if(currentUrl.indexOf("sortkind")<0){//无sortkind参
		newurl=currentUrl+"&sortkind="+sortkind;
	}
	else{//有sortkind，修改参数
		newurl=currentUrl.substring(0,currentUrl.length-1)+sortkind;
	}
	//alert(newurl);
	window.location.href=newurl;	
}
