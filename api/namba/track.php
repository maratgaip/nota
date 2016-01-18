<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<div style="display:none" id="namba">
    <?php
        $musicId = $_GET['id'];
        echo file_get_contents('http://namba.kg/files/download.php?id=' . $musicId);
    ?>
</div>
<script>
    $(document).ready(function() {
         var item = $('#content .startdownload')[0].children[2].href;
         console.log("aa", item)
        var audio = $("#player");
        $("#source").attr("src", item);
        /****************/
        audio[0].pause();
        audio[0].load();//suspends and restores all audio element

        //audio[0].play(); changed based on Sprachprofi's comment below
        audio[0].oncanplaythrough = audio[0].play();
        /****************/
    })

</script>

<audio controls id="player">
    <source id="source" src="" type="audio/ogg">
</audio>