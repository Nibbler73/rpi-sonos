function debug() {
    for(var i=0; i<arguments.length; i++) {
        console.log(arguments[i])
    }
}

// Add a spinner to the page while Ajax-Requests are active
// Spinner Image: http://preloaders.net/en/circular
// Spinner JS: https://stackoverflow.com/questions/1964839/how-can-i-create-a-please-wait-loading-animation-using-jquery
$body = $("body");
$(document).on({
    ajaxStart:  function() { $body.addClass("loading");     },
    ajaxStop:   function() { $body.removeClass("loading");  }
});

function updateControls(responseData) {
    if(typeof responseData === 'object') {
        var controls = $("#musicControls");
        if (responseData.hasOwnProperty("playingState")) {
            controls.children(".playPause").removeClass("playing paused").addClass(responseData.playingState);
        }
        if (responseData.hasOwnProperty("repeat")) {
            if (responseData.repeat === true) {
                controls.children(".repeat").removeClass("off once all").addClass('all');
            } else if (responseData.repeat === false) {
                controls.children(".repeat").removeClass("off once all").addClass('off');
            }
        }
        if (responseData.hasOwnProperty("type")) {
            if (responseData.type == 3302) {
                controls.children(".previous").addClass('off');
            } else if (responseData.type == 3301) {
                controls.children(".previous").removeClass('off');
            }
        }
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
                $( "<img>" ).attr( "src", item.albumArt).attr("data-name", item.name).attr("data-id", item.id).attr("data-type", item.type).appendTo( $( "<li>").appendTo("#basic-list") );
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
                    name: img.data('name'),
                    type: img.data('type')
                }).done(function( data ) {
                    updateControls(data);
                });
            });
        });

    }());

    $("#musicControls").on('click', 'div', function() {
        var button = $( this );
        var selectItem = "selectItem.php";
        $.getJSON( selectItem, {
            position: false,
            id: button.data('id'),
            name: button.data('name'),
            type: button.data('type')
        }).done(function( data ) {
            updateControls(data);
        });
    });
});