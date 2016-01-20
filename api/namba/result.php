
<?php
$stringData = file_get_contents("http://namba.kg/api/?service=home&action=search&type=mp3&query=мирбек&page=1&sort=desc&country_id=0&city_id=0");
echo $stringData;
?>