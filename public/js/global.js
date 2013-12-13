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
		width: '200px',
		disable_search: true
	});

    /*
     * Switch up and down the comment block
     */
    $(".new_form a").click(function(){
        $(".new_form form").slideToggle();
    });


    /*
     * Removes the information box in the dom
     * TODO get the url in a dynamic way.
     */
    $("section#infoBox .close").click(function() {
        $.ajax({
            url: "/thread/hideInfo",
            type: 'POST',
            success: function(data){
                if(data.success) {
                    $("#infoBox").remove();
                }
            }
        });
    });
});

function vote_click(post_id, value) {
    $("#vote_" + post_id + " .action").remove();

    $.ajax({
        url: "/post/vote/"+post_id+"/"+value,
        type: 'POST',
        success: function(data){
            if(data.success) {
                var htmlCount =  $("#vote_" + post_id + " .count");
                var count = parseInt(htmlCount.html());
                count += value;
                htmlCount.html(function(){return count;});
            }
        }
    });
}

var showDropdown = false;

function dropdown_showHide(icon, content)
{
	if (showDropdown)
	{
		$(content + '> div:first-of-type').slideUp("slow", function() {
			$(content).hide();
			$(icon).removeClass('user_icon_active');
			$(icon).addClass('user_icon');
		});
	}
	else
	{
		$(icon).addClass('user_icon_active');
		$(icon).removeClass('user_icon');
		$(content).show();
		$(content + '> div:first-of-type').slideDown();
	}

	showDropdown = !showDropdown;
}
