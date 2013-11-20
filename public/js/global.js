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


    /*
     * Removes the information box in the dom
     * TODO get the url in a dynamic way.
     */
    $("section#infoBox .close").click(function() {
        $.ajax({
            url: "http://127.0.0.1/thread/hideInfo",
            type: 'POST',
            success: function(data){
                if(data.success) {
                    $("#infoBox").remove();
                }
            }
        });
    });
});