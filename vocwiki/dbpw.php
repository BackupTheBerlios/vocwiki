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


// configuration for vocwiki
//
// $Id: dbpw.php,v 1.2 2004/05/05 21:36:06 bolsog Exp $	

// only mysql is supported at moment
$DBParams = 
array(
      "mysql_server" => "localhost",
      "mysql_user" => "root",
      "mysql_pwd" => "",
      "mysql_db" => "vocwiki",
      "mysql_prefix" => "vocwiki_"
      );


?>
