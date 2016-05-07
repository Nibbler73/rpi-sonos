function debug() {
    for(var i=0; i<arguments.length; i++) {
        console.log(arguments[i])
    }
}

jQuery(function($) {
    'use strict';

    // -------------------------------------------------------------
    //   Basic Navigation
    // -------------------------------------------------------------
    (function() {
        var $frame = $('#basic');
        var $wrap = $frame.parent();

        var sonosAPI = "getPlaylist.php";
        $.getJSON( sonosAPI, {
            format: "json"
        }).done(function( data ) {
            $.each( data.items, function( i, item ) {
                $( "<img>" ).attr( "src", item.albumArt).attr("data-name", item.name).attr("data-id", item.id).appendTo( $( "<li>").appendTo("#basic-list") );
            });

            // Call Sly on frame
            var sly = new Sly($frame,
                {
                    horizontal: 1,
                    itemNav: 'basic',
                    smart: 1,
                    activateOn: 'click',
                    mouseDragging: 1,
                    touchDragging: 1,
                    releaseSwing: 1,
                    startAt: 3,
                    scrollBar: $wrap.find('.scrollbar'),
                    scrollBy: 1,
                    pagesBar: $wrap.find('.pages'),
                    activatePageOn: 'click',
                    speed: 300,
                    elasticBounds: 1,
                    easing: 'easeOutExpo',
                    dragHandle: 1,
                    dynamicHandle: 1,
                    clickBar: 1
                }).init();

            // Click Handler
            sly.on('active', function (eventName, itemIndex) {
                var img = $(sly.items[itemIndex].el).children().andSelf().filter('img').last();
                $('#photos-name').text(img.data('name') || 'unknown');
                var selectItem = "selectItem.php";
                $.getJSON( selectItem, {
                    position: itemIndex,
                    id: img.data('id'),
                    name: img.data('name')
                })
            });
        });

    }());

});