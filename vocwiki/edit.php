<?php

/*
    Copyright (C) 2004 Andreas Madsack

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

include("config.php");
include("header.php");

?>

<div class="wikitext">

<?php
if(isset($HTTP_SESSION_VARS["session_file"]) and isset($HTTP_SESSION_VARS["session_login"]))
{
    $file_id=$HTTP_SESSION_VARS["session_file"];
    $author=$HTTP_SESSION_VARS["session_login"];

    $link = mysql_connect($DBParams["mysql_server"], $DBParams["mysql_user"], $DBParams["mysql_pwd"])
    or die("Keine Verbindung moeglich: " . mysql_error());
    mysql_select_db($DBParams["mysql_db"]) or die("Auswahl der Datenbank fehlgeschlagen");

    //
    if(isset($submit))
    {
      if(!isset($element_id) and $element_id==0)
      {
	$query = "insert into ".$DBParams["mysql_prefix"]."element (fk_file,lastchange) values (".$file_id.",NOW());";
	$result = mysql_query($query);
	if (mysql_affected_rows()>0) 
	{ 
	  $element_id=mysql_insert_id();
	}
	$version=1;
      }
      else
      {
	$query="SELECT distinct d.version FROM ".$DBParams["mysql_prefix"]."data d left join ".$DBParams["mysql_prefix"]."element e on e.element_id=d.fk_element and e.version=d.version where d.fk_element=".$element_id." order by d.version desc";
	
	$result = mysql_query($query);
	
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	if (mysql_affected_rows()>0) 
	{
	  $version=$row["version"];  // test if more than one?
	  
	  $version+=1;
	}
	else
	{
	  print ("Error: element not found!");
	}

	
	$query = "update ".$DBParams["mysql_prefix"]."element set version=".$version." where element_id=".$element_id.";";
	
	$result = mysql_query($query);
	if (mysql_affected_rows()==0) 
	{
	  print ("Error: update failed!");
	}
      }

      for($i=0;$i<$counter;$i++)
      {
	if($typ[$i]!="-" and $content[$i]!="")
	{
	  $content_x=$content[$i];
	  if($typ[$i]=="original")
	  {
	    $content_x=utf8ToUnicodeEntities($content_x);
	  }

	  $query = "insert into ".$DBParams["mysql_prefix"]."data (fk_element,version,typ,subtype,content,author,lastchange) values (".$element_id.",".$version.",'".$typ[$i]."','".$subtype[$i]."','".$content_x."','".$author."',NOW());";
	  
//	  print $query."<br>";
	  $result = mysql_query($query);
	  if (mysql_affected_rows()==0) 
	  {
	    print ("Error: an update failed!")." - ".$query;
	  }
	}
      }
    }

    if (isset($element_id))
    {
	$query="SELECT distinct d.version FROM ".$DBParams["mysql_prefix"]."data d left join ".$DBParams["mysql_prefix"]."element e on e.element_id=d.fk_element and e.version=d.version where d.fk_element=".$element_id." order by d.version desc";
	
	$result = mysql_query($query);
	
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$version=$row["version"];  // test if more than one?
    }
    else
    {
	$version=0;
    }
    

    print "<form action=\"edit.php\" method=\"post\">";
    print "<table>";
    
    if (!isset($element_id))
    {
      print "<tr><td><b>new data record</b></td></tr>";
    }
    
    // edit fields ...
    
// ---------------------
    function oneline($t,$st,$c,$cnt)
    {
	$typs=array("-","source","type","original","translation");
	
	print "<tr><td>";
	print "<select name=\"typ[$cnt]\">";
	foreach ($typs as $a)
	{
	  print "<option";
	  if($t==$a) {
	    print " selected>";
	  } 
	  else {
	    print ">";
	  }
	  print "$a</option>";
	}
	print "</select>\n</td><td>";
	
	print "<input name=\"subtype[$cnt]\" type=\"text\" size=\"20\" maxlength=\"20\" value=\"".$st."\">\n";
	print "</td><td>";
	print "<input name=\"content[$cnt]\" type=\"text\" size=\"40\" maxlength=\"70\" value=\"".$c."\">\n";
	
	print "</td></tr>\n";
	
    }
// ------------------------
    
    
    print "<tr><td valign=\"top\"><table>";
    
    $i=0;
    print "<tr><td><b>".("typ")."</b></td><td><b>".("subtyp")."</b></td><td><b>".("content")."</b></td></tr>\n";
    
    if(isset($element_id))
    {
      $query="SELECT d.fk_element,d.subtype,d.version,d.typ,d.content,d.author,d.lastchange FROM ".$DBParams["mysql_prefix"]."data d where d.fk_element=".$element_id." and d.version=".$version." order by d.typ='translation', d.typ='original', d.typ='type',d.typ='source';";
      
      $result = mysql_query($query);
      
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
//	  $datetime=getdatetime($row["lastchange"]);
	
	oneline($row["typ"],$row["subtype"],$row["content"],$i++);
      }
      $imax=$i+5;
    }
    else
    {
	$i=0;
	oneline("source","","",$i++);
	oneline("type","","",$i++);
	oneline("original","","",$i++);
	oneline("translation","","",$i++);
	
	$imax=8;
    }
    
    for($j=$i;$j<$imax;$j++)
    {
	oneline("","","",$j);
    }
    
    
    print "</table>";
    
    print "</td><td valign=\"top\" rowspan=\"7\">";
    
// show version-1
    if($version>0)
    {
      $query="SELECT fk_element,d.subtype,d.version,d.typ,d.content,d.author,d.lastchange FROM ".$DBParams["mysql_prefix"]."data d where d.fk_element=".$element_id." and d.version>".$version."-2 order by d.version desc,d.typ='translation', d.typ='original', d.typ='type',d.typ='source';";
      
      $result = mysql_query($query);
      
      print "<table>";
      print "<tr><td><em>version</em></td>";
      print "<td><em>typ</em></td>";
      print "<td><em>subtype</em></td>";
      print "<td><em>content</em></td>";
      print "<td><em>author</em></td><td><em>date/time</em></td></tr>";
      while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$datetime=getdatetime($row["lastchange"]);
	print "<tr><td>".$row["version"]."</td><td>".$row["typ"]."</td><td>".$row["subtype"]."</td><td>".$row["content"]."</td><td>".$row["author"]."</td><td>".$datetime."</td></tr>"; 
      }
      print "</table>";
      
    }
    print "</td></tr>";
    
    print "<tr><td align=\"right\" valign=\"top\">";
    
    if (isset($element_id))
    {
      print "<input type=\"hidden\" name=\"element_id\" value=\"".$element_id."\">";
    }
    print "<input type=\"hidden\" name=\"counter\" value=\"".$imax."\">";

    print "<input type=\"submit\" name=\"submit\" value=\" Save \">";
    print "</form>";
    print "</td></tr>";
    if (isset($element_id))
    {
      print "<tr><td valign=\"top\">";
      print "<a href=\"edit.php\">";
      print ("insert new data record")."</a>";
      print "</td><tr>";
    }
    
    print "<tr><td valign=\"top\">";
    print "<br><b>".("Info: ")."</b><br>";
    print ("On 'Save' all values will be interted, under YOUR id!"); 
    print "</td><tr>";
/*    print "<tr><td valign=\"top\">";
    print "<pre>subtype kann bei original/translation ein example paar sein, z.b.:\n";
    print "original     example1  Peter loves Mary.\n";
    print "translation  example1  Peter liebt Maria.\n";
    print "original     example2  Peter loves his life.\n";
    print "translation  example2  Peter liebt sein Leben.\n";
    print "</pre>";
    print "</td><tr>";
*/
    print "</table>";

    if(isset($element_id))
    {
      mysql_free_result($result);
      mysql_close($link);
    }
}
else
{
  print "<b>";
  print ("you must be logged in and a language-file must be selected!!");	
  print "</b><br><br>";
}

?>
<br>
</div>


<?php

include("footer.php");
?>
