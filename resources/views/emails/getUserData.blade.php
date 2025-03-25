


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Track Your Game</title>

    <!-- Responsive Metatag -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <link href="//netdna.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" />
    <script defer type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBwYHAmh_ZyTweutm9YhY9I2otTzfjkU3U&libraries=places&callback=initialize"></script>
     <script type="text/javascript">

//Old Method
// function initMap() {
//     const map = new google.maps.Map(document.getElementById("map"), {
//         zoom: 10,
//         center: { lat: 22.309425, lng: 72.136230 },
// });
// setMarkers(map);
// }

// const locations = <?php print json_encode($markers) ?>;
// //console.log(locations);

// function setMarkers(map) {

// const image = {
//     url: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
//     // This marker is 20 pixels wide by 32 pixels high.
//     size: new google.maps.Size(20, 32),
//     // The origin for this image is (0, 0).
//     origin: new google.maps.Point(0, 0),
//     // The anchor for this image is the base of the flagpole at (0, 32).
//     anchor: new google.maps.Point(0, 32),
// };

// const shape = {
//     coords: [1, 1, 1, 20, 18, 20, 18, 1],
//     type: "poly",
// };

// for (let i = 0; i < locations.length; i++) {
//     const location = locations[i];

//     new google.maps.Marker({
//     position: { lat: parseFloat(location[1]), lng: parseFloat(location[2]) },
//     map,
//     icon: image,
//     shape: shape,
//     title: location[0],
//     zIndex: location[3],
//     animation: google.maps.Animation.BOUNCE
//     });

//     //console.log(locations[i]);
//     }
// }

    function initialize() {
    var locations = <?php print json_encode($markers) ?>;
    window.map = new google.maps.Map(document.getElementById('map'), {
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    const image = {
    url: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
    // This marker is 20 pixels wide by 32 pixels high.
    size: new google.maps.Size(20, 32),
    // The origin for this image is (0, 0).
    origin: new google.maps.Point(0, 0),
    // The anchor for this image is the base of the flagpole at (0, 32).
    anchor: new google.maps.Point(0, 32),
    };

    const shape = {
    coords: [1, 1, 1, 20, 18, 20, 18, 1],
    type: "poly",
    };

    var infowindow = new google.maps.InfoWindow({
     ariaLabel: "User Info",
    });

    var bounds = new google.maps.LatLngBounds();

    for (i = 0; i < locations.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map,
            icon: image,
            shape: shape,
            title: location[0],
            zIndex: location[3],
            animation: google.maps.Animation.BOUNCE
        });

        bounds.extend(marker.position);

        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                infowindow.setContent("Username:" +  locations[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));
    }

    map.fitBounds(bounds);

    var listener = google.maps.event.addListener(map, "idle", function () {
        map.setZoom(3);
        google.maps.event.removeListener(listener);
    });
}


</script>

</head>
<body>
    <div><h1>This is test map data</h1></div>


<div class="col-12">
    <div class="card">
         <div class="card-body">
               <div id="map" style="height: 800px;"></div>
         </div>
    </div>
</div>
</body>
<footer>
       
</footer>
