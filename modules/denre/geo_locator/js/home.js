function update_map(n)
{
    var map_lat = n[0];
    var map_lng = n[1];

    var latLng = new google.maps.LatLng(map_lat, map_lng);

    glMapPartHome.panTo(latLng);
    BxWmap.prototype.updateLocations();
}

