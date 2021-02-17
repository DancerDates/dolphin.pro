function update_form(n)
{
    var frm_country = n[2];
    var frm_region = n[3];
    var frm_city = n[4];

    var d = document.getElementsByName("Country[]");

    for (var i = 0; i < d.length; i++)
        d[i].value = frm_country;

    for (var i = 0; i < 10; i++) {
        for (var j = 0; j < document.getElementsByName("Country[" + i + "]").length; j++) {
            document.getElementsByName("Country[" + i + "]")[j].value = frm_country;
        }
    }
    for (var i = 0; i < 10; i++) {
        for (var j = 0; j < document.getElementsByName("country[" + i + "]").length; j++) {
            document.getElementsByName("country[" + i + "]")[j].value = frm_country;
        }
    }
    for (var i = 0; i < 10; i++) {
        for (var j = 0; j < document.getElementsByName("Region[" + i + "]").length; j++) {
            document.getElementsByName("Region[" + i + "]")[j].value = frm_region;
        }
    }
    for (var i = 0; i < 10; i++) {
        for (var j = 0; j < document.getElementsByName("City[" + i + "]").length; j++) {
            document.getElementsByName("City[" + i + "]")[j].value = frm_city;
        }
    }
}
