
$(document).ready(function() {
	var ranklist = document.getElementsByClassName("final")[0];
		var bookname = ranklist.getElementsByClassName("bookname");
		for (var i = 0; i < bookname.length; i++)
		{	bookname[i].text = weekrank[i];
			bookname[i].href = weekrank_href[i];}
	$(".top-charts").children(".mooth").click(function() { //月书单
		var ranklist = document.getElementsByClassName("final")[0];
		var bookname = ranklist.getElementsByClassName("bookname");
	
		for (var i = 0; i < bookname.length; i++)
		{	bookname[i].text = moothrank[i];
			bookname[i].href = moothrank_href[i];}
			
	});
	$(".top-charts").children(".week").click(function() { //周书单
		var ranklist = document.getElementsByClassName("final")[0];
		var bookname = ranklist.getElementsByClassName("bookname");
		for (var i = 0; i < bookname.length; i++)
		{	bookname[i].text = weekrank[i];
			bookname[i].href = weekrank_href[i];}
	});
	$(".top-charts").children(".total").click(function() { //总书单
		var ranklist = document.getElementsByClassName("final")[0];
		var bookname = ranklist.getElementsByClassName("bookname");
		for (var i = 0; i < bookname.length; i++)
		{
			bookname[i].text = totalrank[i];
			bookname[i].href = totalrank_href[i];
		}	
	});
});