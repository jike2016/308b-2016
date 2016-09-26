videojs(document.getElementById("my_videojs"), {}, function(){
this.on('loadedmetadata', function(){
	this.currentTime(50);//设置视频的播放时间
	//var whereYouAt = this.currentTime(); //获取当前的播放时间
	//var duration_time =  Math.floor(this.duration());
	this.play();
});

this.on("timeupdate",function(){
	var whereYouAt = this.currentTime();
 
});

this.on("ended", function(){ 
	alert('4');
	//this.src({type: "application/x-mpegURL", src: "http://192.168.3.11/hls/movie2/movie2.m3u8"});
}); 
});
