function geo_weather(n) {
    var city = n[4];
    var country_code = n[2];

    $.ajax({
        url: "/m/weather/WeatherBlock",
        type: "GET",
        data: "city="+city+"&country_code="+country_code,
        success: function(data, textStatus, jqXHR){
            $('#weather_block').replaceWith(data);
        },
        error: function (jqXHR, textStatus, errorThrown){
            //alert(errorThrown);
        }
    });

}
