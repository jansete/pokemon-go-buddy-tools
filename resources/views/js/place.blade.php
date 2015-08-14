function initialize_{{ $place->id }}() {
    var map_{{ $place->id }};
    var element_{{ $place->id }} = document.getElementById('map-{{ $place->id }}');

    var myLatlng_{{ $place->id }} = new google.maps.LatLng({{ $place->geo_lat }},{{ $place->geo_lng }});
    var mapOptions_{{ $place->id }} = {
        zoom: 16,
        center: myLatlng_{{ $place->id }},
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoomControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL,
            position: google.maps.ControlPosition.RIGHT_BOTTOM
        },
        mapTypeControl: false,
        //panControl: false,
        //zoomControl: false,
        streetViewControl: false
    };
    map_{{ $place->id }} = new google.maps.Map(element_{{ $place->id }}, mapOptions_{{ $place->id }})

    var marker_{{ $place->id }} = new google.maps.Marker({
        position: myLatlng_{{ $place->id }},
        map: map_{{ $place->id }},
    });
}

google.maps.event.addDomListener(window, 'load', initialize_{{ $place->id }});

$("#place-stars-points-{{ $place->id }}").width("{{ $place->starForPlace()['width'] }}");

$("#delete-place-{{ $place->id }}").click(delete_place_confirm);

@foreach($place->comments as $comment)
    $("#delete-comment-{{ $comment->id }}").click(delete_comment_confirm);
@endforeach
