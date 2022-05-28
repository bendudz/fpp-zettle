<?php
//if(isset($_POST['submit']))
//{
//    $name = htmlspecialchars($_POST['station']);
//    if (strlen($name) >8)
//        {
//                echo "Station ID must be less than 8 characters";
//        }
//        else
//        {
//    echo exec("python /home/fpp/media/plugins/fpp-edmrds/rds-song.py -c $name 2>&1 ");
//        }
//}
//?>

<html>
<head>
<script type="text/javascript">
$(document).ready(function(){
$("#nowplaying").load('/plugin.php?nopage=1&plugin=fpp-edmrds&page=nowplaying.php');
});

</script>
</head>

<div id="rds" class="settings">
<fieldset>
<legend>EDM-LCD-RDS-EP Support Instructions</legend>

<p>Before you use the RDS capabilities of your EDM FM transmitter you must be comfortable
with soldering and connect the SCL and SDA pins from the RDS chip located within the EDM FM transmitter
to two pins on the raspberry Pi. Currently the two PINs to use are pin 23 and 24 for SCL and SDA respectively. <br><br>
<br>
Configuration of the RDS settings for the EDM transmitter can be found here: http://www.edmdesign.com/docs/EDM-TX-RDS.pdf.
Information on the RDS chip inside the EDM transmitter can be found here: http://pira.cz/rds/mrds192.pdf
. Once this connection is made than you can read and set the RDS information on the unit.
</p>

<p>When you create your MP3 and OGG files, make sure you tag them with Artist and Title fields. You can upload the MP3s and OGG files through the
File Manager in the Content Setup menu. Once the tags are set, this plug in will automatically update the RDS text when the file is played!</p>
<p>Known Issues:
<ul>
<li>NONE</li>
</ul>

<form method="post" action="/plugin.php?plugin=fpp-edmrds&page=plugin_setup.php">
Manually Set Station ID<br>
<p><label for="station_ID">Station ID:</label>
<input type="text" name="station" id="station_ID"></input>
(Expected format: up to 8 characters)
<input id="submit_button" name="submit" type="submit" class="buttons" value="Set Station ID">
</p>
</form>

<span id="nowplaying">Now Playing:<br>
Station ID:</span>

<p>To report a bug, please file it against the fpp-vastfmt plugin project here:
<a href="https://github.com/FalconChristmas/fpp-edmrds/issues/new" target="_new">fpp-edmrds GitHub Issues</a></p>

</fieldset>
</div>
<br />
</html>
