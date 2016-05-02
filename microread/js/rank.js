
$(document).ready(function() {
	var ranklist = document.getElementsByClassName("final")[0];
		var bookname = ranklist.getElementsByClassName("bookname");
		for (var i = 0; i < bookname.length; i++)
			bookname[i].text = weekrank[i];
	$(".top-charts").children(".mooth").click(function() { //月书单
		var ranklist = document.getElementsByClassName("final")[0];
		var bookname = ranklist.getElementsByClassName("bookname");
		for (var i = 0; i < bookname.length; i++)
			bookname[i].text = moothrank[i];
	});
	$(".top-charts").children(".week").click(function() { //周书单
		var ranklist = document.getElementsByClassName("final")[0];
		var bookname = ranklist.getElementsByClassName("bookname");
		for (var i = 0; i < bookname.length; i++)
			bookname[i].text = weekrank[i];
	});
	$(".top-charts").children(".total").click(function() { //总书单
		var ranklist = document.getElementsByClassName("final")[0];
		var bookname = ranklist.getElementsByClassName("bookname");
		for (var i = 0; i < bookname.length; i++)
			bookname[i].text = totalrank[i];
	});
});