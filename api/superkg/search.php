<div style="display:none" id="superkg">

    <?php
    $qtxt = $_REQUEST [q];
    $pg = $_REQUEST [pg];
    echo file_get_contents('http://www.super.kg/media/?pg=' . $pg . '&media_search=1&main_search=' . $qtxt);

    ?>
</div>
<div id="content">

</div>
<script>
    $(document).ready(function() {
        var arr = [];

        //var item = $('table.seriy tbody:first tr:first td:nth-child(2)')[2].childNodes[1];
        //var item = $('table.seriy tbody:first tr:first td:nth-child(2)').find("div:not(:first-child)");
        //var item = $('table.seriy tbody:first tr:first td:nth-child(2) div:not(:first)')[0].children[1];
        var itemLength = $('table.seriy tbody:first tr:first td:nth-child(2) div:not(:first)').length;
        var item = $('table.seriy tbody:first tr:first td:nth-child(2) div:not(:first)');

        console.log("aa", item)


        for (var i=0; i<item.length; i++) {
            console.log("gg", item[i].childNodes[2])
        }
        $(item).each(function( index ) {

            var obj = {};
            obj.index = index;
            console.log("lan",$(this).attr("href"))
            obj.id = $(this).attr("href").split("/")[3];
            obj.song = $(this).text().split('"')[1];
            obj.artist = $(this).text().split('"')[0];
            arr.push(obj);
        });

        console.log(item);
        $("#superkg").html("");
        $("#content").html(item);
/*
        return false;
        $(item).each(function( index ) {
            var obj = {};
            obj.index = index;
            obj.id = $(this).attr("href").split("/")[3];
            obj.song = $(this).text().split('"')[1];
            obj.artist = $(this).text().split('"')[0];
            arr.push(obj);
        });
        var jsonObject = new Object();
        jsonObject.title = "Super Kg New MP3";
        jsonObject.length = arr.length;
        jsonObject.list = arr;
        $("#superkg").html("");

        $.ajax
        ({
            type: "POST",
            beforeSend: function( xhr ) {
                xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
            },
            dataType : 'json',
            async: false,
            url: 'result.php',
            contentType: "application/x-www-form-urlencoded;charset=ISO-8859-15",
            data: { data: JSON.stringify(jsonObject) },
            success: function () {window.location.href = 'result.json'; },
            failure: function() {alert("Error!");}
        });
        */
    })
</script>