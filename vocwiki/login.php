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
<br><br>
<form action="index.php" method="post">
<table>
<tr><td>
<?php
print ("login").":"; 
?>
</td><td>
<input name="login_form" type="text" size="30">
</td></tr>
<tr><td>
<?php
print ("password").":"; 
?>
</td><td>
<input name="pwd_form" type="password" size="30">
</td></tr>
<tr><td>
<input type="submit" value="<?php print (" send "); ?>">
</td></tr>
</table>
<br>
<?php
    print "<a href=\"register.php\">".("register")."</a> ";
?>
<br><br></div>

<?php

include("footer.php");
?>
