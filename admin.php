<?PHP
foreach($_POST AS $key => $value) {
    ${$key} = $value;
}

foreach($_GET AS $key => $value) {
    ${$key} = $value;
}

foreach($_REQUEST AS $key => $value) {
    ${$key} = $value;
}

// FANSUB RCS
// Arbeits-Übersicht

// Infos
// Benutzerhirarchie:
// 0:   Nichts Könner
// 1:   Translator
// 2:   Timer
// 4:   Editor
// 8:   Quality Checker
// 16:  Encoder
// 32:  Releaser
// 64:  Trial
// 128: Admin
// if( USERRECHT & RECHTEZAHL ) BEFEHL;

// Einstellungen
// Datenbank
$dbserver = "localhost";
$dbuser = "";
$dbpass = "";
$dbname = "";

// Sonstige
$release = "17";
$aufgaben = "18";

$time = time();

$timeout = "Timeout nach 60 Sekunden.";

// Verbindung zur Datenbank aufbauen
@mysql_connect($dbserver, $dbuser, $dbpass);
@mysql_select_db($dbname);

// Funktionen

function getuser($i) {
	if ($i == "0") {
		return "----";
	} else {
		$getuserresult = mysql_query("SELECT Name FROM Benutzer WHERE ID='$i'");
			while($getuserrow = mysql_fetch_row($getuserresult)) return $getuserrow[0];
	}
}

function getkurzuser($i) {
	if ($i == "0") {
		return "?";
	} else {
		$getuserresult = mysql_query("SELECT KurzName FROM Benutzer WHERE ID='$i'");
			while($getuserrow = mysql_fetch_row($getuserresult)) return $getuserrow[0];
	}
}

function getstatus($i) {
	switch($i)
	{
		case 1: $status = "wartend"; break;
		case 2: $status = "begonnen"; break;
		case 3: $status = "fertig"; break;
	}
	return $status;
}

function getaufgabe($i) {
	switch($i)
	{
		case 1: $aufgabe = "RAW"; break;
		case 2: $aufgabe = "MQ-Encode"; break;
		case 3: $aufgabe = "Translation"; break;
		case 4: $aufgabe = "Translation Check"; break;
		case 5: $aufgabe = "Karaoke"; break;
		case 6: $aufgabe = "Timing"; break;
		case 7: $aufgabe = "Typeset"; break;
		case 8: $aufgabe = "Edit 1"; break;
		case 9: $aufgabe = "Edit 2"; break;
		case 10: $aufgabe = "Edit 3"; break;
		case 11: $aufgabe = "QC-Encode"; break;
		case 12: $aufgabe = "Quality Check 1"; break;
		case 13: $aufgabe = "Quality Check 2"; break;
		case 14: $aufgabe = "Quality Check 3"; break;
		case 15: $aufgabe = "Encode"; break;
		case 16: $aufgabe = "Upload"; break;
		case 17: $aufgabe = "Release"; break;
		case 18: $aufgabe = "Anderes"; break;
	}
	return $aufgabe;
}

function getkurzaufgabe($i) {
	switch($i)
	{
		case 1: $aufgabe = "RAW"; break;
		case 2: $aufgabe = "MQ-Enc"; break;
		case 3: $aufgabe = "TL"; break;
		case 4: $aufgabe = "TLC"; break;
		case 5: $aufgabe = "Kar"; break;
		case 6: $aufgabe = "Time"; break;
		case 7: $aufgabe = "Type"; break;
		case 8: $aufgabe = "Ed1"; break;
		case 9: $aufgabe = "Ed2"; break;
		case 10: $aufgabe = "Ed3"; break;
		case 11: $aufgabe = "QC-Enc"; break;
		case 12: $aufgabe = "QC1"; break;
		case 13: $aufgabe = "QC2"; break;
		case 14: $aufgabe = "QC3"; break;
		case 15: $aufgabe = "Enc"; break;
		case 16: $aufgabe = "Upl"; break;
		case 17: $aufgabe = "Rel"; break;
		case 18: $aufgabe = "?"; break;
	}
	return $aufgabe;
}

function getdatum($date) {
	return $date = date("d.m.Y",$date);
}

function getzeit($time) {
	return $time = date("H:i:s",$time);
}

function getaktuelle($serie) {
	$getaktuelleresult = mysql_query("SELECT FolgenNummer FROM Folgen WHERE Serie='$serie' AND active='1' ORDER BY FolgenNummer");
		while($getaktuellerow = mysql_fetch_row($getaktuelleresult)) return $var = $getaktuellerow[0];
}

function getlastrelease($serie) {
	global $release;
	$getreleaseresult = mysql_query("SELECT FolgenNummer FROM Folgen WHERE Serie='$serie' AND Aufgabe='$release'");
		while($getreleaserow = mysql_fetch_row($getreleaseresult)) $var = $getreleaserow[0];
		if ($var == '') {$var = "noch keine";}
		return $var;
}

function getrowcolor($i) {
	switch($i)
	{
		case 1: $rowcolor = "#ff6600"; break;
		case 2: $rowcolor = "#f0f0f0"; break;
		case 3: $rowcolor = "#00e050"; break;
	}
	return $rowcolor;
}

function getnextfolgennummer($serie) {
	$getnextresult = mysql_query("SELECT FolgenNummer FROM Folgen WHERE Serie='$serie' ORDER BY FolgenNummer");
		while($getnextrow = mysql_fetch_row($getnextresult)) $var = $getnextrow[0];
		if ($var == '') {$var = "1";} else {$var = $var+1;}
		return $var;
}

function checktime($ct) {
	global $time;
	
	if (($time-$ct) < 60) {
		return true;
	} else {
		return false;
	}
}

// Seiten Start

if ( (!$view) || ($view=="main") )
{
echo <<<ECHOENDE
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<TITLE>FansubRCS</TITLE>
<link href="main.css" rel="stylesheet" type="text/css">
</HEAD>

<br>
<table width=80% align=center border=0>
  <tr>
    <td align=center colspan=2>
      <a href="?view=newuser">Neuer User</a>
    </td>
  </tr>
  <tr>
    <td align=center colspan=2>
      feste User:
    </td>
  </tr>
  <tr valign=top>
    <td align=center >aktive User:
<table border=1 cellpadding=3px>
ECHOENDE;
$result = mysql_query("SELECT ID,Name FROM Benutzer WHERE active='1' AND Gast='0' ORDER BY Name");
while($row = mysql_fetch_row($result)) 
  {
    echo <<<ECHOENDE
  <tr>
    <td>$row[1]</td>
    <td><a href="?view=edituser&id=$row[0]">ED</a></td>
    <td><a href="?view=deactuser&id=$row[0]">DA</a></td>
  </tr>
ECHOENDE;
  }
  echo <<<ECHOENDE
</table>
ECHOENDE;

echo <<<ECHOENDE
</td><td align=center >inaktive User:
<table border=1 cellpadding=3px>
ECHOENDE;
$result = mysql_query("SELECT ID,Name FROM Benutzer WHERE active='0' AND Gast='0' ORDER BY Name");
while($row = mysql_fetch_row($result)) 
  {
    echo <<<ECHOENDE
  <tr>
    <td>$row[1]</td>
    <td><a href="?view=edituser&id=$row[0]">ED</a></td>
    <td><a href="?view=actuser&id=$row[0]">AC</a></td>
  </tr>
ECHOENDE;
  }
  echo <<<ECHOENDE
</table>
ECHOENDE;

echo <<<ECHOENDE
    </td>
  </tr>
  <tr>
    <td align=center colspan=2>
      Gast User:
    </td>
  </tr>
  <tr valign=top>
    <td align=center >aktive User:
<table border=1 cellpadding=3px>
ECHOENDE;
$result = mysql_query("SELECT ID,Name FROM Benutzer WHERE active='1' AND Gast='1' ORDER BY Name");
while($row = mysql_fetch_row($result)) 
  {
    echo <<<ECHOENDE
  <tr>
    <td>$row[1]</td>
    <td><a href="?view=edituser&id=$row[0]">ED</a></td>
    <td><a href="?view=deactuser&id=$row[0]">DA</a></td>
  </tr>
ECHOENDE;
  }
  echo <<<ECHOENDE
</table>
ECHOENDE;

echo <<<ECHOENDE
</td><td align=center >inaktive User:
<table border=1 cellpadding=3px>
ECHOENDE;
$result = mysql_query("SELECT ID,Name FROM Benutzer WHERE active='0' AND Gast='1' ORDER BY Name");
while($row = mysql_fetch_row($result)) 
  {
    echo <<<ECHOENDE
  <tr>
    <td>$row[1]</td>
    <td><a href="?view=edituser&id=$row[0]">ED</a></td>
    <td><a href="?view=actuser&id=$row[0]">AC</a></td>
  </tr>
ECHOENDE;
  }
  echo <<<ECHOENDE
</table>
ECHOENDE;

echo <<<ECHOENDE
</td>
</tr>
</table>
ECHOENDE;

echo <<<ECHOENDE
<table width=80% align=center border=0>
  <tr>
    <td align=center colspan=2>
      <a href="?view=newproj">Neues Projekt</a>
    </td>
  </tr>
  <tr valign=top>
    <td align=center >aktive Projekte:
<table border=1 cellpadding=3px>
ECHOENDE;
$result = mysql_query("SELECT ID,Name FROM Serien WHERE active='1' ORDER BY Name");
while($row = mysql_fetch_row($result)) 
  {
    echo <<<ECHOENDE
  <tr>
    <td>$row[1]</td>
    <td><a href="?view=editproj&id=$row[0]">ED</a></td>
    <td><a href="?view=deactproj&id=$row[0]">DA</a></td>
  </tr>
ECHOENDE;
  }
  echo <<<ECHOENDE
</table>
ECHOENDE;

echo <<<ECHOENDE
    </td>
    <td align=center >inaktive Projekte:
<table border=1 cellpadding=3px>
ECHOENDE;
$result = mysql_query("SELECT ID,Name FROM Serien WHERE active='0' ORDER BY Name");
while($row = mysql_fetch_row($result)) 
  {
    echo <<<ECHOENDE
  <tr>
    <td>$row[1]</td>
    <td><a href="?view=editproj&id=$row[0]">ED</a></td>
    <td><a href="?view=actproj&id=$row[0]">AC</a></td>
  </tr>
ECHOENDE;
  }
  echo <<<ECHOENDE
</table>
ECHOENDE;

echo <<<ECHOENDE
</td>
</tr>
</table>
ECHOENDE;
}

else if ($view == "newuser")
{
if (!$doedit) {

echo <<<ECHOENDE
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<TITLE>FansubRCS</TITLE>
<link href="main.css" rel="stylesheet" type="text/css">
</HEAD>
<center><small><a href="?">zurueck</a></small></center>
<br>
<table align=center>
  <tr>
    <td>
<form action="?view=newuser&id=$id&doedit=1&ct=$time" method="post">
Neuen User anlegen:<br><br>
Name: <input type="text" size=8 name="Name" value="$Name"><br>
KurzName: <input type="text" size=8 name="KurzName" value="$KurzName"><br>
Gast: <input type="checkbox" name="Gast"><br>
<input type="submit" name="Anlegen" value="Anlegen">
</form>
</td>
</tr>
</table>
ECHOENDE;

} else {
	//if (!checktime($ct)) die($timeout);
	mysql_query("INSERT INTO Benutzer (Name,KurzName,Gast,active) VALUES ('$Name','$KurzName','$Gast','1')");
	header("Location: ?view=main");
}

}
else if ($view == "edituser")
{

if (!$doedit) {
$result = mysql_query("SELECT * FROM Benutzer WHERE ID='$id'");
while($row = mysql_fetch_row($result)) 
{
	$id = $row[0];
	$Name = $row[1];
	$KurzName = $row[2];
	$Gast = $row[3];
}
$check=($Gast)?"checked":"";

echo <<<ECHOENDE
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<TITLE>FansubRCS</TITLE>
<link href="main.css" rel="stylesheet" type="text/css">
</HEAD>
<center><small><a href="?">zurueck</a></small></center>
<br>
<table align=center>
  <tr>
    <td>
<form action="?view=edituser&id=$id&doedit=1&ct=$time" method="post">
User bearbeiten:<br><br>
Name: <input type="text" size=8 name="Name" value="$Name"><br>
KurzName: <input type="text" size=8 name="KurzName" value="$KurzName"><br>
Gast: <input type="checkbox" name="Gast" $check><br>
<input type="submit" name="Bearbeiten" value="Bearbeiten">
</form>
</td>
</tr>
</table>
ECHOENDE;

} else {
	//if (!checktime($ct)) die($timeout);
	$Gast = ($Gast==on)?1:0;
	mysql_query("UPDATE Benutzer SET Name='$Name', KurzName='$KurzName', Gast='$Gast' WHERE ID=$id");
	header("Location: ?view=main");
}

}
else if ($view == "newproj")
{
if (!$doedit) {

echo <<<ECHOENDE
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<TITLE>FansubRCS</TITLE>
<link href="main.css" rel="stylesheet" type="text/css">
</HEAD>
<center><small><a href="?">zurueck</a></small></center>
<br>
<table align=center>
  <tr>
    <td>
<form action="?view=newproj&id=$id&doedit=1&ct=$time" method="post">
Neues Projekt anlegen:<br><br>
Name: <input type="text" size=8 name="Name" value="$Name"><br>
KurzName: <input type="text" size=8 name="KurzName" value="$KurzName"><br>
Folgen: <input type="text" size=8 name="Folgen" value="$Folgen"><br>
<input type="submit" name="Anlegen" value="Anlegen">
</form>
</td>
</tr>
</table>
ECHOENDE;

} else {
	//if (!checktime($ct)) die($timeout);
	mysql_query("INSERT INTO Serien (Name,KurzName,Folgen,active) VALUES ('$Name','$KurzName','$Folgen','1')");
	header("Location: ?view=main");
}

}
else if ($view == "editproj")
{

if (!$doedit) {
$result = mysql_query("SELECT * FROM Serien WHERE ID='$id'");
while($row = mysql_fetch_row($result)) 
{
	$id = $row[0];
	$Name = $row[1];
	$KurzName = $row[2];
	$Folgen = $row[3];
}

echo <<<ECHOENDE
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<TITLE>FansubRCS</TITLE>
<link href="main.css" rel="stylesheet" type="text/css">
</HEAD>
<center><small><a href="?">zurueck</a></small></center>
<br>
<table align=center>
  <tr>
    <td>
<form action="?view=editproj&id=$id&doedit=1&ct=$time" method="post">
Projekt bearbeiten:<br><br>
Name: <input type="text" size=8 name="Name" value="$Name"><br>
KurzName: <input type="text" size=8 name="KurzName" value="$KurzName"><br>
Folgen: <input type="text" size=8 name="Folgen" value="$Folgen"><br>
<input type="submit" name="Bearbeiten" value="Bearbeiten">
</form>
</td>
</tr>
</table>
ECHOENDE;

} else {
	//if (!checktime($ct)) die($timeout);
	mysql_query("UPDATE Serien SET Name='$Name', KurzName='$KurzName', Folgen='$Folgen' WHERE ID=$id");
	header("Location: ?view=main");
}

}
else if ($view == "actuser")
{
  mysql_query("UPDATE Benutzer SET active=1 WHERE ID=$id");
	header("Location: ?view=main");
}
else if ($view == "deactuser")
{
  mysql_query("UPDATE Benutzer SET active=0 WHERE ID=$id");
	header("Location: ?view=main");
}
else if ($view == "actproj")
{
  mysql_query("UPDATE Serien SET active=1 WHERE ID=$id");
	header("Location: ?view=main");
}
else if ($view == "deactproj")
{
  mysql_query("UPDATE Serien SET active=0 WHERE ID=$id");
	header("Location: ?view=main");
}
?>