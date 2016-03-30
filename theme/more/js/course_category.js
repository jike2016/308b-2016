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
		//newurl=currentUrl.substring(0,currentUrl.length-1)+sortkind;
		newurl = currentUrl.replace(/sortkind=[\d]+/,"sortkind="+sortkind);
	}
	/** 选择排序后把分页切换到第一页 */
	newurl = newurl.replace(/page=[\d]+/,"page=1");
	//把链接中多于的“#”号去掉
	newurl = newurl.replace(/#/,"");
	//alert(newurl);
	window.location.href=newurl;	
}

function select_page(page_num){
	if(page_num  < 1) return;

	var currentUrl = this.location.href;
	if(currentUrl.indexOf("?")<0){//无参
		newurl=currentUrl+"?page="+page_num;
	}else if(currentUrl.indexOf("page=")<0) {//无page参
		newurl = currentUrl+"&page="+page_num;
	}else{	//有page参数
		//修改page参数
		newurl = currentUrl.replace(/page=[\d]+/,"page="+page_num);
	}
	//alert(newurl);
	window.location.href=newurl;
}

$(document).ready(function(){
	//获取当前页与总页数
	var current_page = parseInt($('#hidden_current_page').val());
	var total_page = parseInt($('#hidden_total_page').val());
	//在页面中显示当前页与总页数
	$('.pager-cur').text($('#hidden_current_page').val());
	$('.pager-total').text($('#hidden_total_page').val());

	//如果在第一页，首页按钮不可点
	if(current_page == 1){
		$('.page').append('<span class="disabled_page" >首页</span>');
	}else{
		$('.page').append('<button class="btn btn-primary" onclick="select_page(1)">首页</button>');
	}

	//如果不在第一页，上一页的按钮可以点击
	if(current_page > 1){
		$('.page').append('<button class="btn btn-primary" onclick="select_page('+(current_page-1)+')">上一页</button>');
		//右上角换页按钮
		$('.pager-prev').removeClass('disabled');
		$('.pager-prev').click(function(){
			select_page(current_page-1);
		})
	}else{
		$('.page').append('<span class="disabled_page">上一页</span>');
		$('.pager-prev').addClass('disabled');
	}

	//显示当前页码
	$('.page').append('<a href="javascript:void(0)"  class="active">'+current_page+'</a>');

	//当前页小于总页数，有下一页
	if(current_page < total_page){
		$('.page').append('<button class="btn btn-primary" onclick="select_page('+(current_page+1)+')">下一页</button>');
		//右上角换页按钮添加点击事件
		$('.pager-next').removeClass('disabled');
		$('.pager-next').click(function(){
			select_page(1+current_page);
		})
	}else{
		$('.page').append('<span class="disabled_page">下一页</span>');
		$('.pager-next').addClass('disabled');
	}

	//当前页小于总页数，尾页按钮可以点击
	if(current_page != total_page){
		$('.page').append('<button class="btn btn-primary" onclick="select_page('+total_page+')">尾页</button>');
	}else{
		$('.page').append('<span class="disabled_page">尾页</span>');
	}

	//按钮靠在一起了，得把他们分开
	$('.page button').css('margin-left','6px');
});