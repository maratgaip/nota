<?php
@session_start();
@ob_start();
@ob_implicit_flush(0);

@error_reporting(E_ALL ^ E_NOTICE);
@ini_set('display_errors', true);
@ini_set('html_errors', false);
@ini_set('error_reporting', E_ALL ^ E_NOTICE);

define('ROOT_DIR', "..");

define('INCLUDE_DIR', ROOT_DIR . '/includes');
@include (INCLUDE_DIR . '/config.inc.php');
require_once INCLUDE_DIR . '/class/_class_mysql.php';
require_once ROOT_DIR . '/modules/functions.php';
require_once INCLUDE_DIR . '/db.php';


if (isset($_GET['song'])) {
    $songid = $_GET['song'];
    $songid = base64_decode($songid);
    $sql_result = $db->query("SELECT vass_songs.title,vass_songs.album_id,vass_albums.name FROM vass_songs LEFT JOIN vass_albums ON vass_albums.id = vass_songs.album_id WHERE vass_songs.id =  '" . $songid . "'");
    while (($row = $db->get_row($sql_result))) {

        $songs ['album'] = $row ['name'];
        $songs ['url'] = stream($songid);
        $songs ['image'] = songlist_images($row ['album_id']);

        $songs ['title'] = $row ['title'];
        $songs ['sources'] = sources($songid);
        $songs ['id'] = $songid;
        $songs['album_id'] = $row ['album_id'];
        $result ['songs'] [] = $songs;
    }
    $result ['status_text'] = "OK";
    $result ['status_code'] = "200";

    //		$result ['start'] = $start;
    $result ['total'] = 1;
    $buffer = $result;
} elseif (isset($_GET['album'])) {

    $albumid = $_GET['album'];

    $albumid = base64_decode($albumid);
    $sql_query = $db->query("SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_songs LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.album_id = '$albumid' ");

    $total_results = $db->super_query("SELECT COUNT(*) AS count FROM vass_songs WHERE album_id = '$albumid'");
//    $i = 0;
    while ($row = $db->get_row($sql_query)) {

        $songs ['album'] = $row ['song_album'];
        $songs ['url'] = stream($row ['song_id']);
        $songs ['image'] = songlist_images($row ['album_id'], $row['artist_id']);

        $songs ['title'] = $row ['song_title'];
        $songs ['sources'] = sources($row ['song_id']);
        $songs ['id'] = $row ['song_id'];
        $songs['album_id'] = $albumid;
        $result ['songs'] [] = $songs;
//        $i++;
    }
    $result ['status_text'] = "OK";
    $result ['status_code'] = "200";

    //		$result ['start'] = $start;
    $result ['total'] = $total_results['count'];


    $buffer = $result;
//            echo "<pre>";  print_r ( $buffer );"<pre>";die;
} elseif (isset($_GET['playlist'])) {

    $playlistid = $_GET['playlist'];
    $playlistid = base64_decode($playlistid);

    $owner = $db->super_query("SELECT user_id FROM vass_playlists WHERE id = '$playlistid'");

    $sql_query = $db->query("SELECT vass_songs.artist_id, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
				vass_artists.id AS artist_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id 
				FROM vass_song_playlist LEFT JOIN vass_songs ON vass_song_playlist.song_id = vass_songs.id LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_song_playlist.playlist_id = '" . $playlistid . "'");

    $total_results = $db->super_query("SELECT COUNT(*) AS count 
				FROM vass_song_playlist LEFT JOIN vass_songs ON vass_song_playlist.song_id = vass_songs.id LEFT JOIN vass_albums ON vass_songs.album_id = vass_albums.id LEFT JOIN 
				vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_song_playlist.playlist_id = '" . $playlistid . "'");

    while ($row = $db->get_row($sql_query)) {

        $songs ['album'] = $row ['song_album'];
        $songs ['url'] = stream($row ['song_id']);
        $songs ['image'] = songlist_images($row ['album_id'], $row['artist_id']);

        $songs ['title'] = $row ['song_title'];
        $songs ['sources'] = sources($row ['song_id']);
        $songs ['id'] = $row ['song_id'];
        $songs['album_id'] = $albumid;
        $result ['songs'] [] = $songs;
    }
    $result ['status_text'] = "OK";
    $result ['status_code'] = "200";

    //		$result ['start'] = $start;
    $result ['total'] = $total_results['count'];

    $buffer = $result;
}
$songarray = [];
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Player</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/bootstrap.min.css" type="text/css" rel="stylesheet">
        <link href="css/font-awesome.min.css" type="text/css" rel="stylesheet">
        <link href="css/jquery.mCustomScrollbar.css" type="text/css" rel="stylesheet">

        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.mCustomScrollbar.js"></script>


    </head>

    <body>
        <div class="container">
            <audio class="hide"  id="music" controls>
                <source src="" type="audio/mp3" />
            </audio>
            <div class="player">

                <div class="player-options">
                    <div class="play-button text-center top-to-bottom"> 
                        <img src="<?php echo $buffer['songs'][0]['image']['small']; ?>" width="80px" height="80px">
                        <div class="circle text-center">
                            <a  id="pAudio" href="#" ><span id="pButton" class="fa fa-2x fa-play-circle-o "></span></a>
                        </div>
                    </div>

                    <div class="track-details">
                        <span class="text-white"><strong></strong></span><br>
                        <span class="text-gray"><small></small></span>
                        <div class="track-progress">
                            <div id="progress-bar" class="progress progress-custom">
                                <div id="bar" class="progress-bar progress-bar-custom" style="width:0%">
                                </div>
                            </div>
                            <span id="audio-time" class="time-details text-white font-9 "></span>

                        </div>
                    </div>
                </div>

                <div class="player-album">
                    <ul class="list-group border0 list-unstyled">
                        <?php
                        $imgo = 0;
                        for ($play = 0; $play < $buffer['total']; $play++) {
                            ?>
                            <li class="paddingLR-sm border0 borderTB">

                                <ul class="list-unstyled list-inline">
                                    <li class="top-m10px"><a class="play-btn" href="#"><span class="fa fa-play"></span></a></li>
                                    <li class="w68" url="<?php echo $buffer['songs'][$play]['url'];
                        $songarray[$imgo] = $buffer['songs'][$play]['url']; ?>" id="<?php echo $buffer['songs'][$play]['image']['small']; ?>" data-count="<?php echo $play?>"><span class="text-white" data-title="<?php echo $buffer['songs'][$play]['title'] ?>"><?php echo $buffer['songs'][$play]['title'] ?></span><br>
                                        <span class="text-gray" data-album="<?php echo $buffer['songs'][$play]['album'] ?>"><small><?php echo $buffer['songs'][$play]['album'] ?></small></span>
                                    </li>
                                    <li class="top-m10px" ><span class="badge"></span></li>
                                </ul>
                            </li>

                            <?php
                            $imgo++;
                        }
                        ?>
                    </ul>
                </div>


            </div>	


        </div>

    </body>

    <script>
        $(document).ready(function(){
            var songid = [];
            var title = [];
            var album = [];
            var img = [];
            var list;
            $(".w68").each(function(){
                songid.push($(this).attr('url'));
                title.push($(this).children().attr('data-title'));
                album.push($(this).children('.text-gray').attr('data-album'));
                img.push($(this).attr('id'));
                
            });
            console.log(songid);
            var music = document.getElementById('music');
            var duration;
            var size =songid.length;
            var timelineWidth = document.getElementById('progress-bar').offsetWidth - document.getElementById('bar').offsetWidth;
            var playhead = document.getElementById('bar'); // playhead
            var timeline = document.getElementById('progress-bar'); // timeline
            var cursor = 0;
        
           // console.log(songarray);
            music.addEventListener('ended', updateSongs,true );
            
            function updateSongs(){
                if(cursor<size-1){
                cursor++;
                console.log(songid[cursor])
                music.src = songid[cursor];
                music.play();
                 list=document.getElementsByClassName('track-details')[0]
                list.getElementsByClassName('text-white')[0].innerHTML= title[cursor];
                list.getElementsByClassName('text-gray')[0].innerHTML= album[cursor];
                $('.play-button > img').attr('src',img[cursor]); 
                }else{
                    $('#pButton').removeClass().addClass('fa fa-2x fa-play-circle-o')
                }
                }
                
           
           
                
            document.getElementById('pAudio').onclick = function () { 
                if (music.src == ""){
                     music.src = songid[cursor];
                      music.play();
                    $('#pButton').removeClass().addClass('fa fa-2x fa-pause')
                    music.addEventListener('timeupdate',updateProgressBar,true);
                    list=document.getElementsByClassName('track-details')[0]
                list.getElementsByClassName('text-white')[0].innerHTML= title[cursor];
                list.getElementsByClassName('text-gray')[0].innerHTML= album[cursor];
                  $('.play-button > img').attr('src',img[cursor]);  
                }
               else{ if (music.paused) {
                    music.play();
                    $('#pButton').removeClass().addClass('fa fa-2x fa-pause')
                    music.addEventListener('timeupdate',updateProgressBar,true);
                      
                   
                } else {
                    music.pause();
                    $('#pButton').removeClass().addClass('fa fa-2x fa-play-circle-o')  
                }
               }                 
            }
            function updateProgressBar() {
                var progressBar = document.getElementById('progress-bar');
                var percentage = Math.floor((100 / music.duration) * music.currentTime);
                $('#bar').css({'width':percentage+'%'});
            }



            document.getElementById('progress-bar').addEventListener("click",function(event){
                //moveplayhead(event);
                music.currentTime = duration * clickPercent(event);
            })
		
            function clickPercent(e) {
                return (e.pageX - document.getElementById('progress-bar').offsetLeft) / timelineWidth;
            }

            music.addEventListener("canplaythrough", function () {
                duration = music.duration;  
                document.getElementById('audio-time').innerHTML=parseInt( duration / 60 ) % 60 +':'+parseInt(duration % 60);
            }, false);

            $(".w68").bind("click" , function(){
                var data=$(this).attr('url');
                var type=$(this).attr('id');
                cursor = $(this).attr('data-count');
                var title=$(this).children().attr('data-title');
                var album=$(this).children('.text-gray').attr('data-album');
                var list=document.getElementsByClassName('track-details')[0]
                list.getElementsByClassName('text-white')[0].innerHTML= title;
                list.getElementsByClassName('text-gray')[0].innerHTML= album;
                music.setAttribute("src",data); 
                document.getElementById("pAudio").click();
       
                $('.play-button > img').attr('src',type);
                //        var time = data.duration;  
                //	document.getElementByClassName('badge').innerHTML=parseInt( time / 60 ) % 60 +':'+parseInt(time % 60);
                //        alert(music.getAttribute('src'));
            });
    
        });
    </script>
    <script>
        (function($){
            $(window).load(function(){
				
                $.mCustomScrollbar.defaults.theme="light-2"; //set "light-2" as the default theme
				
                $(".player-album").mCustomScrollbar({
					
                });
						
                $(".scrollTo a").click(function(e){
                    e.preventDefault();
                    var $this=$(this),
                    rel=$this.attr("rel"),
                    el=rel==="content-y" ? ".demo-y" : rel==="content-x" ? ".demo-x" : ".demo-yx",
                    data=$this.data("scroll-to"),
                    href=$this.attr("href").split(/#(.+)/)[1],
                    to=data ? $(el).find(".mCSB_container").find(data) : el===".demo-yx" ? eval("("+href+")") : href,
                    output=$("#info > p code"),
                    outputTXTdata=el===".demo-yx" ? data : "'"+data+"'",
                    outputTXThref=el===".demo-yx" ? href : "'"+href+"'",
                    outputTXT=data ? "$('"+el+"').find('.mCSB_container').find("+outputTXTdata+")" : outputTXThref;
                    $(el).mCustomScrollbar("scrollTo",to);
                    output.text("$('"+el+"').mCustomScrollbar('scrollTo',"+outputTXT+");");
                });
				
            });
        })(jQuery);
    </script>
</html>
