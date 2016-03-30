	var imgarray = new Array;
	imgarray[0]=getPageDir()+"themechange/images/banner1.jpg";
	imgarray[1]=getPageDir()+"themechange/images/banner2.png";
	imgarray[2]=getPageDir()+"themechange/images/banner3.jpg";
	imgarray[3]=getPageDir()+"themechange/images/banner4.png";
	imgarray[4]=getPageDir()+"themechange/images/banner5.jpg";
	var img=new Array;
	img[0]=document.getElementById("img0");
	img[1]=document.getElementById("img1");
	img[2]=document.getElementById("img2");
	img[3]=document.getElementById("img3");
	img[4]=document.getElementById("img4");
	
	var base=0;	
	function begin()
	{
		for(var i=0;i<5;i++)
			img[i].style.background="#666";
	}
	function changebgcolor(bg,base)
	{
		if(base==0)
			bg.style.backgroundColor="#201c1b";
		else if(base==1)
			bg.style.backgroundColor="#dbe98f";
		else if(base==2)
			bg.style.backgroundColor="#707070";
		else if(base==3)
			bg.style.backgroundColor="#f4f5ef";
		else if(base==4)
			bg.style.backgroundColor="#cfbd8f";
		
	}
	function change(ch)
	{
		var bg=document.getElementById("bg");
			if(ch==1)
			{
				if(base==4)
					base=-1;
				bg.style.background="url(" + imgarray[base+1] + ")";
				bg.style.backgroundPosition ='center';
				bg.style.backgroundRepeat='no-repeat';				
				begin();
				img[base+1].style.background="#0C3";
				base=base+1;
				changebgcolor(bg,base)
			}
			else
			{
				if(base==0)
					base=5;
				bg.style.background="url(" + imgarray[base-1] + ")";
				bg.style.backgroundPosition ='center';
				bg.style.backgroundRepeat='no-repeat';				
				begin();
				img[base-1].style.background="#0C3";
				base=base-1;
				changebgcolor(bg,base)
			}

	}	
	//获取当前页面所在目录的绝对路径
function getPageDir(){
var div = document.createElement('div');
div.innerHTML = '<a href="./"></a>';
var pageDir = div.firstChild.href;	
div = null;
return pageDir;
}