<?php
include("config.php");
include("header.php");

?>
<div class="wikitext">
<table><tr><td valign="top">
<br/><br/>
<?php
print ("choose vocabulary file:<br/>");

$link = mysql_connect($DBParams["mysql_server"], $DBParams["mysql_user"], $DBParams["mysql_pwd"])
    or die("Keine Verbindung moeglich: " . mysql_error());
mysql_select_db($DBParams["mysql_db"]) or die("Auswahl der Datenbank fehlgeschlagen");

$result = mysql_query("SELECT file_id, title,original_name,original_code,translation_name,translation_code FROM ".$DBParams["mysql_prefix"]."file order by original_code");

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  if($row["file_id"]==3)
  {
    print "<a href=\"list.php?file_id=".$row["file_id"]."\">testarea</a><br/>"; 
  }
  else
  {
    print "<a href=\"list.php?file_id=".$row["file_id"]."\">".$row["original_name"]." (".$row["original_code"].") - ". $row["translation_name"]." (".$row["translation_code"].")</a><br/>"; 
  }
}
?>
</td>
<td width="70">
</td>
<td valign="top">
<div class="xxx">
Nun ist das vocwiki auf berlios beheimatet.<br>
erstmal gibt es keine registration, da das formular dazu nicht mehr aktuell ist.<br>
bugreports/feature request/forum gibt es unter: <a href="http://developer.berlios.de/projects/vocwiki/">http://developer.berlios.de/projects/vocwiki/</a><br>
<br>
<a href="http://developer.berlios.de">
<img src="http://developer.berlios.de/bslogo.php?group_id=0&type=1" width="124" height="32" border="0" alt="BerliOS Logo"></A>
</div>
</td></tr></table>
<?php
print "<br/><br/></div>";

mysql_free_result($result);
mysql_close($link);


include("footer.php");
?>
