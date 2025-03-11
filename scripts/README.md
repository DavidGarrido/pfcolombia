# jquery-funnel
An extremely small, pure HTML funnel chart based on jQuery. It uses a CSS margin trick to render a funnel chart with 
equal height segments. The values provided will define the aperture of the bottom of each segment as a percentage of the sum of all values.

# Usage
```
  var funnelData = [
    {
        value: 3000,
        color:"#F7464A"
    },
    {
        value: 2500,
        color: "#46BFBD"
    },
    {
        value: 1340,
        color: "#FDB45C"
    },
    {
        value: 700,
        color: "#949FB1"
    },
    {
        value: 10,
        color: "#4D5360"
    }
  ];
  
  $('#funnel-container').drawFunnel(funnelData, {
            width: $(this).width(), // Container height, i.e. height of #funnel-container
            height: $(this).height(),  // Container width, i.e. width of #funnel-container
            padding: 1, // Padding between segments, in pixels
            half: false,  // Render only a half funnel
            minSegmentSize: 0,  // Width of a segment can't be smaller than this, in pixels
            label: function () { return "Label!"; }  // A label generation function 
  });
```
