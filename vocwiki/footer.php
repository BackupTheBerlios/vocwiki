

<hr />
<table width="100%">
<tr>
<td align="left">
<?
/*
  <a href="http://validator.w3.org/check/referer">
    <img src="http://www.w3.org/Icons/valid-xhtml10" 
         alt="Valid XHTML 1.0!" height="31" width="88" />
  </a>
*/
?>
</td><td align="right" valign="top">
<?php
if(strlen($HTTP_SESSION_VARS["session_login"])>0)
{
//    print "<a href=\"prefs.php\">".("preferences")."</a> ";
    print ("logged in as: ").$HTTP_SESSION_VARS["session_login"];
    print " <a href=\"logout.php\">".("sign out")."</a> ";

}
else
{
    print "<a href=\"login.php\">".("sign in")."</a> ";
}

?>
</td></tr>

</table>
</body>
</html>