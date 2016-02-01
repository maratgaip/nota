
<div style="display:none" id="superkg">
    <?php
    # there are 322 pages in super.kg
    for ($x = 1; $x <= 2; $x++) {
    echo file_get_contents('http://www.super.kg/media/audio/?pg=' . $x);
    }
    ?>
</div>
<script>
    $(document).ready(function() {
        var arr = [];
        var item = $('.audioItemFP a');
        $(item).each(function( index ) {
            var obj = {};
            obj.index = index;
            obj.id = $(this).attr("href").split("/")[3];
            obj.song = $(this).text().split('"')[1];
            var artistsAll = $(this).text().split('"')[0];
            // deleting extra space in string
            artistsAll = artistsAll.slice(0, artistsAll.length-1);
            var artist = artistsAll.split(',');
            var artistArr = [];
            if (artist.length > 1){
                artistArr[0] = artist[0];
                for (var i=1; i < artist.length; i++) {
                    // deleting extra space in string
                    var tempArtist = artist[i].slice(1,artist[i].length);
                    artistArr[i] = tempArtist;
                }
            } else {
                var tempArtist = '';
                if (artist[0].charAt(artist[0].length-1) == " "){
                    tempArtist = artist[0].slice(0,artist[0].length-1)
                } else if (artist[0].charAt(0) == " "){
                    tempArtist = artist[0].slice(1,artist[0].length)
                } else {
                    tempArtist = artist[0];
                }
                artistArr[0] = tempArtist;
            }
            obj.artist = artistArr;
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
            dataType : 'json',
            async: false,
            url: 'result.php',
            data: { data: JSON.stringify(jsonObject) },
            success: function () {//window.location.href = 'result.json';
             },
            failure: function() {alert("Error!");}
        });
    })

</script>