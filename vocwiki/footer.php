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
?>

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