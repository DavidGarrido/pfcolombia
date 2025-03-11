(function ( $ ) {

    $.fn.drawFunnel = function ( data, options ) {
        var settings = $.extend({
            width: $(this).width(),
            height: $(this).height(),
            padding: 1,
            half: false,
            minSegmentSize: 0,
            label: function () { return ""; }
        }, options);

        settings.width = settings.half ? settings.width * 2 : settings.width;


        var segmentCount = data.length;
        var segmentSum = data.reduce(function (sum, item) { return sum + item.value }, 0);
        var segmentHeight = Math.round((settings.height - settings.padding * segmentCount) / segmentCount);


        var container = $('<div id="funnel" style="width: '+settings.width+'px; height: '+settings.height+'px; overflow: hidden;"></div>')
            .appendTo(this);

        var previousBottom = settings.width;
        var previousPercentage = 1;
        var marginSize = 0;
        $.each(data, function (key, item) {
            var segmentColor = item.color;
            var segmentPercentage = item.value / segmentSum;
            var segmentBottomWidth = previousBottom - Math.round(segmentPercentage * settings.width);
            segmentBottomWidth = segmentBottomWidth > settings.minSegmentSize ? segmentBottomWidth : settings.minSegmentSize;
            var segmentStyle = 'border-'+(previousPercentage > segmentPercentage ? 'top':'bottom')+': '+segmentHeight+'px solid ' + segmentColor + ';';
            var borderSize = Math.ceil((previousBottom - segmentBottomWidth) / 2);

            segmentStyle += 'height: 0;';
            segmentStyle += 'margin-bottom: '+settings.padding+'px;';
            segmentStyle += 'border-right: '+borderSize+'px solid transparent;';
            segmentStyle += 'border-left: '+borderSize+'px solid transparent;';

            var marginLeft = settings.half ?  marginSize - Math.round(settings.width / 2) : marginSize - (previousPercentage < segmentPercentage ? borderSize : 0);
            segmentStyle += 'margin-left: '+marginLeft+'px;';

            if (previousPercentage < segmentPercentage) {
                segmentStyle += 'width: '+(previousBottom)+'px;';
            }
            else {
                segmentStyle += 'width: '+segmentBottomWidth+'px;';
            }
            
            var segmentDom = $('<div class="funnel-segment" style="'+segmentStyle+' box-sizing: content-box; -webkit-box-sizing: content-box; -moz-box-sizing: content-box;"></div>').appendTo(container);
            var labelDom = $('<div class="funnel-label" style="display: inline">'+item.labeled+'</div>').appendTo(segmentDom);
            var labelTop = (segmentHeight / 2) + (labelDom.height() / 2);
            var labelLeft = ((settings.half ? previousBottom + (settings.width / 2) : previousBottom) / 2) - (labelDom.width() / 2) - borderSize;
            labelDom.css({top: "-"+Math.round(labelTop)+"px", left: Math.round(labelLeft)+"px", position: "relative"});

            previousBottom = segmentBottomWidth;
            marginSize += borderSize ;
            previousPercentage = segmentPercentage;
        });

        return this;
    }

}( jQuery ));

