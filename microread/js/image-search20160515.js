$(function(){
	var options;
	var last_page_elem;
	var toppos = 0;
	var firstScrool = true;

	$('.img-list').infinitescroll(
		{
			navSelector: ".img-more", 		//导航的选择器，会被隐藏
			nextSelector: ".img-more a",	//包含下一页链接的选择器
			bufferPx: 40,					//载入信息的显示时间，时间越大，载入信息显示时间越短
			//extraScrollPx: 150,			//触发的剩余滚动高度
			dataType: 'json',
			loading: {
				img: '../img/loading_circle.gif',
				msgText: "加载中...",
				finishedMsg: '没有新数据了...',
				selector: '.loading' // 显示loading信息的div
			},
			pathParse: function(path, nextPage){
				--this.state.currPage;
				return [path, ''];
			},
			/**
			 * 解析json生成图片显示元素
			 */
			template: function(data) {
				if(!options) 
					options = this;
				var item_tpl = document.querySelector('.img-item:first-child');
				last_page_elem = document.createElement('h2');
				var arr = [last_page_elem];
				for (var i = 0; i < data.length; i++) {
					var item = item_tpl.cloneNode(true);
					var imgdata = data[i];
					var img = item.querySelector('img');
					img.src = imgdata.src;
					img.alt = imgdata.label;
					item.querySelector('a').href = imgdata.url;
					item.querySelector('.img-title').innerHTML = imgdata.label;
					item.querySelector('.img-size').innerHTML = imgdata.size;
					arr.push(item);
				};
				return arr;
			},
			errorCallback: function(e) {
				// console.log(e);
			},
			canScroll: function(){
				var is_down = false;
				var scrollTop = $(window).scrollTop();
				if (scrollTop > toppos) is_down = true;
				else if (firstScrool) {
					is_down = true;
					firstScrool = false;
				}
				toppos = scrollTop;
				return is_down;
			}
		}, 
		function(newElems) {
			//程序执行完的回调函数
			last_page_elem.innerHTML = '第' + options.state.currPage + '页';
			if(document.body.scrollHeight - document.body.offsetHeight < 10){
				firstScrool = true;
				$(window).scroll();
			}
		}
	);
	
	$(window).scroll();
});