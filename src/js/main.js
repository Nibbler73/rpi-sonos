



$(function() {




    if ($.fn.reflect) {
        $('.photos .cover').reflect();
    }
    $('.photos').coverflow({
        width:			320,
        height:			240,
        visible:		'density',
        selectedCss:	{	opacity: 1	},
        outerCss:		{	opacity: .1	},

        confirm:		function() {
            console.log('Clicked: ' + $('#photos-name').text());
        },

        change:			function(event, cover) {
            var img = $(cover).children().andSelf().filter('img').last();
            $('#photos-name').text(img.data('name') || 'unknown');
        }

    });
});

