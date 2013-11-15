$( document ).ready(function() {
	/*var ddBasic = [
		{ text: "Vote", value: 1, },
		{ text: "Date", value: 2, }
	];
	
	$('#orderBy').ddslick({
		data: ddBasic,
		background: 'transparent',
		height: '100px',
		selectText: "Order by"
	});*/
	$("#orderBy").chosen({
		width: '100%',
		disable_search: true
	});
});