// Target specific elements
$(function(){
	$("li:first-child").addClass("first");
	$("li:last-child").addClass("last");
	$("div.widget:first").addClass("first");
	$("div.widget:last").addClass("last");
	$(".navbar ul li ul li:has(ul)").find("a:first").append(" &raquo;");
});