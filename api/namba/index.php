<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<div style="display:none" id="namba">
    <?php    echo file_get_contents('http://namba.kg/#!/search/mp3/eminem'); ?>
</div>
<script>
    $(document).ready(function() {
        window.location.href = 'result.php';
        /*
        var arr = [];

        var item = $('.audioItemFP a');
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