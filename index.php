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

function ParseToIrc($text) {
	$text = preg_replace('|\[b\](.*)\[/b\]|Uism','$1',$text);
	$text = preg_replace('|\[u\](.*)\[/u\]|Uism','$1',$text);
	$text = preg_replace('|\[i\](.*)\[/i\]|Uism','$1',$text);
	$text = preg_replace('|\[r\](.*)\[/r\]|Uism','$1',$text);
	
	//$text = preg_replace('|\[c=\d+\](.*)\[/c\]|Uism','$1$2',$text);
	return $text;
}

function ParseToText($text) {
	$text = preg_replace("|(.*)|Uism","[b]$1[/b]",$text);
	$text = preg_replace("|(.*)|Uism","[u]$1[/u]",$text);
	$text = preg_replace("|(.*)|Uism","[i]$1[/i]",$text);
	$text = preg_replace("|(.*)|Uism","[r]$1[/r]",$text);
	
	//$text = preg_replace('|\d+(.*)|Uism','[c=$1]$2[/c]',$text);
	return $text;
}

function ParseToHtml($text) {
	$text = preg_replace("|(.*)|Uism","<b>$1</b>",$text);
	$text = preg_replace("|(.*)|Uism","<u>$1</u>",$text);
	$text = preg_replace("|(.*)|Uism","<i>$1</i>",$text);
	$text = preg_replace("|(.*)|Uism","$1",$text);
	
	//$text = preg_replace('|\d+(.*)|Uism','<font color="getHTMLcolor($1)">$2</font>',$text);
	return $text;
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


if ( (!$view) && (!$serie) )
{
echo <<<ECHOENDE
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<TITLE>FansubRCS</TITLE>
<link href="main.css" rel="stylesheet" type="text/css">
</HEAD>

<table border=0>
<br><br>
ECHOENDE;

$result = mysql_query("SELECT * FROM Serien WHERE active='1'");
while($row = mysql_fetch_row($result)) 
{
	$serienid = $row[0];
	$serienname = $row[2];
	$aktuelle = getaktuelle($serienid);
	
	echo <<<ECHOENDE
	<tr>
		<td width=50><b><a href="?view=serie&id=$serienid">$serienname</a></b></td>
		<td width=*>Aktuell: Ep. $aktuelle</td>
	</tr>
	<tr>
		<td width=50></td>
		<td>
			<table border=0>
				<tr>
					<td width=30>Ep</td>
					<td width=100>Datum</td>
					<td width=110>Job</td>
					<td width=130>Benutzer</td>
					<td width=80>Status</td>
					<td width=200>Info</td>
				</tr>
ECHOENDE;
	$result2 = mysql_query("SELECT * FROM Folgen WHERE Serie='$serienid' AND active='1' ORDER BY FolgenNummer DESC, Aufgabe DESC, Status");
	while($row2 = mysql_fetch_row($result2)) 
	{
		$id = $row2[0];
		$episodennummer = $row2[2];
		$datum = getdatum($row2[3]);
		$zeit = getzeit($row2[3]);
		$aufgabe = getaufgabe($row2[4]);
		$benutzer = getuser($row2[5]);
		$status = getstatus($row2[6]);
		$comment = $row2[7];
		$rowcolor = getrowcolor($row2[6]);
		
		echo <<<ECHOENDE
				<tr bgcolor="$rowcolor">
					<td valign="top">$episodennummer</td>
					<td valign="top">$datum<br>$zeit</td>
					<td valign="top">$aufgabe</td>
					<td valign="top">$benutzer<br><small><a href="?view=benutzer&id=$id">aendern</a></small></td>
					<td valign="top">$status<br><small><a href="?view=status&id=$id">aendern</a></small></td>
					<td valign="top"><small><a href="?view=comment&id=$id">...</a> $comment</small></td>
ECHOENDE;
}
echo <<<ECHOENDE
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<br>
		</td>
	</tr>
ECHOENDE;

}
echo <<<ECHOENDE
</table>
ECHOENDE;
}

else if ($view == "serie")

{

$BenutzerSelect .= '<option value="0">----';
$result = mysql_query("SELECT ID,Name FROM Benutzer WHERE active='1' ORDER BY Gast, Name");
while($row = mysql_fetch_row($result))
{
$BenutzerSelect .= '<option value="'.$row[0].'">'.$row[1];
}

for($x=1;$x<=18;$x++){
	$ArbeitSelect .= '<option value="'.$x.'">'.getaufgabe($x);
}

$result = mysql_query("SELECT * FROM Serien WHERE ID='$id'");
while($row = mysql_fetch_row($result)) 
{
	$serienid = $row[0];
	$serienname = $row[1];
	$folgenzahl = $row[3];
}

$lastrelease = getlastrelease($serienid);
$nextfolgennummer = getnextfolgennummer($serienid);

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
<h1>Serienstatus: $serienname</h1><hr>
Letztes Release: $lastrelease / $folgenzahl<br>
<br><br>
<form action="?view=serie&id=$id&doedit=1&ct=$time" method="post">
Neue Episode anlegen: Nummer <input type="text" size=4 name="folgennummer" value="$nextfolgennummer"> : 
<select name="neuaufgabe">
$ArbeitSelect
</select>
 fuer 
<select name="neubenutzer">
$BenutzerSelect
</select> <input type="submit" name="Anlegen" value="Anlegen">
</form><br><br>

<table border=0>
	<tr>
		<td width=30>Ep</td>
		<td width=100>Datum</td>
		<td width=110>Job</td>
		<td width=130>Benutzer</td>
		<td width=80>Status</td>
		<td width=200>Info</td>
	</tr>
ECHOENDE;
	
	$result2 = mysql_query("SELECT * FROM Folgen WHERE Serie='$serienid' ORDER BY FolgenNummer DESC, Status, ID DESC, Aufgabe DESC");
	while($row2 = mysql_fetch_row($result2)) 
	{
		$id = $row2[0];
		$episodennummer = $row2[2];
		$datum = getdatum($row2[3]);
		$zeit = getzeit($row2[3]);
		$aufgabe = getaufgabe($row2[4]);
		$benutzer = getuser($row2[5]);
		$status = getstatus($row2[6]);
		$comment = $row2[7];
		$rowcolor = getrowcolor($row2[6]);
		
		if ( ($tempep <> "") && ($tempep != $episodennummer) ) {
			$platzhalter = "<tr><td><br></td></tr>";
		} else {
			$platzhalter = "";
		}
		$tempep = $episodennummer;
		
		echo <<<ECHOENDE
				$platzhalter
				<tr bgcolor="$rowcolor">
					<td valign="top">$episodennummer</td>
					<td valign="top">$datum<br>$zeit</td>
					<td valign="top">$aufgabe</td>
					<td valign="top">$benutzer<br><small><a href="?view=benutzer&id=$id">aendern</a></small></td>
					<td valign="top">$status<br><small><a href="?view=status&id=$id">aendern</a></small></td>
					<td valign="top"><small><a href="?view=comment&id=$id">...</a> $comment</small></td>
				</tr>
ECHOENDE;

}

echo <<<ECHOENDE
</table>
ECHOENDE;

} else {
	if (!checktime($ct)) die($timeout);
	if ($neuaufgabe == $release) {
		mysql_query("INSERT INTO Folgen (Serie,FolgenNummer,Datum,Aufgabe,Benutzer,Status,active) VALUES ($serienid,$folgennummer,$time,$neuaufgabe,$neubenutzer,'3','0')");
	} else {
		mysql_query("INSERT INTO Folgen (Serie,FolgenNummer,Datum,Aufgabe,Benutzer,Status,active) VALUES ($serienid,$folgennummer,$time,$neuaufgabe,$neubenutzer,'1','1')");
	}
	header("Location: ?view=serie&id=$serienid");
}

}

else if ($view == "benutzer")

{

$result = mysql_query("SELECT * FROM Folgen WHERE ID='$id'");
while($row = mysql_fetch_row($result))
{
	$serienid = $row[1];
	$id = $row[0];
	$episodennummer = $row[2];
	$datum = getdatum($row[3]);
	$zeit = getzeit($row[3]);
	$aufgabe = getaufgabe($row[4]);
	$benutzer = getuser($row[5]);
	$benutzerid = $row[5];
	$status = getstatus($row[6]);
	$comment = $row[7];
	$rowcolor = getrowcolor($row[6]);
	$aufgabeid = $row[4];
}

$result = mysql_query("SELECT Name FROM Serien WHERE ID='$serienid'");
while($row = mysql_fetch_row($result)) 
{
	$serienname = $row[0];
}


$result = mysql_query("SELECT ID,Name FROM Benutzer WHERE active='1' ORDER BY Gast, Name");
$i = ceil(mysql_num_rows($result)/4);
$BenutzerListing .= '<td width=150 valign="top">';
$a=0;
while($row = mysql_fetch_row($result)) {
	if ($a == $i) {
		$BenutzerListing .= '</td>';
		$BenutzerListing .= '<td width=150 valign="top">';
		$a=0;
	}
	$BenutzerListing .= '<a href="?view=benutzer&id='.$id.'&doedit='.$row[0].'&ct='.$time.'">'.$row[1].'</a><br>';
	$a++;
}
$BenutzerListing .= '</td>';

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
<h1>Episodenstatus: $serienname $episodennummer</h1><hr>
<br>
<table border=0>
	<tr>
		<td width=30>Ep</td>
		<td width=100>Datum</td>
		<td width=110>Job</td>
		<td width=130>Benutzer</td>
		<td width=80>Status</td>
		<td width=200>Info</td>
	</tr>
	<tr bgcolor="$rowcolor">
		<td valign="top">$episodennummer</td>
		<td valign="top">$datum<br>$zeit</td>
		<td valign="top">$aufgabe</td>
		<td valign="top">$benutzer<br><small><a href="?view=benutzer&id=$id">aendern</a></small></td>
		<td valign="top">$status<br><small><a href="?view=status&id=$id">aendern</a></small></td>
		<td valign="top"><small><a href="?view=comment&id=$id">...</a> $comment</small></td>
	</tr>
</table>
<br><br>
Aendern des Benutzers zu:
<br><br>
<table border=0>
	<tr>
		$BenutzerListing
	</tr>
	<tr>
		<td>
			<br><a href="?view=benutzer&id=$id&doedit=x&ct=$time">Keine Zuordnung</a>
		</td>
	</tr>
</table>

ECHOENDE;

} else {
	if (!checktime($ct)) die($timeout);
	if ($doedit == 'x') $doedit = 0;
	if ($benutzerid != "0") {
		mysql_query("UPDATE Folgen SET Datum=$time,active='0',Status='3' WHERE ID='$id'");
		mysql_query("INSERT INTO Folgen (Serie,FolgenNummer,Datum,Aufgabe,Benutzer,Status,active) VALUES ($serienid,$episodennummer,$time,$aufgabeid,$doedit,'1','1')");
	} else if ($benutzerid == "0") {
		mysql_query("UPDATE Folgen SET Benutzer='$doedit',Datum=$time,active='1',Status='1' WHERE ID='$id'");
	}
	header("Location: ?view=serie&id=$serienid");
}
}
else if ($view == "status")

{

$result = mysql_query("SELECT * FROM Folgen WHERE ID='$id'");
while($row = mysql_fetch_row($result))
{
	$serienid = $row[1];
	$id = $row[0];
	$episodennummer = $row[2];
	$datum = getdatum($row[3]);
	$zeit = getzeit($row[3]);
	$aufgabe = getaufgabe($row[4]);
	$benutzer = getuser($row[5]);
	$status = getstatus($row[6]);
	$statuszahl = $row[6];
	$comment = $row[7];
	$rowcolor = getrowcolor($row[6]);
	$aufgabeid = $row[4];
}

$result = mysql_query("SELECT Name FROM Serien WHERE ID='$serienid'");
while($row = mysql_fetch_row($result)) 
{
	$serienname = $row[0];
}

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
<h1>Episodenstatus: $serienname $episodennummer</h1><hr>
<br>
<table border=0>
	<tr>
		<td width=30>Ep</td>
		<td width=100>Datum</td>
		<td width=110>Job</td>
		<td width=130>Benutzer</td>
		<td width=80>Status</td>
		<td width=200>Info</td>
	</tr>
	<tr bgcolor="$rowcolor">
		<td valign="top">$episodennummer</td>
		<td valign="top">$datum<br>$zeit</td>
		<td valign="top">$aufgabe</td>
		<td valign="top">$benutzer<br><small><a href="?view=benutzer&id=$id">aendern</a></small></td>
		<td valign="top">$status<br><small><a href="?view=status&id=$id">aendern</a></small></td>
		<td valign="top"><small><a href="?view=comment&id=$id">...</a> $comment</small></td>
	</tr>
</table>
<br>
ECHOENDE;

if ( ($statuszahl == "1") || ($statuszahl == "3") ) echo '<a href="?view=status&id='.$id.'&doedit=2&ct='.$time.'">Job annehmen</a><br><br>';
if ( ($statuszahl == "2") || ($statuszahl == "3") ) echo '<a href="?view=status&id='.$id.'&doedit=1&ct='.$time.'">Auf \'wartend\' zurueckstellen</a><br><br>';
if ($statuszahl == "3") echo '<a href="?view=status&id='.$id.'&doedit=4&ct='.$time.'">Diesen Job loeschen</a><br><br>';
if ($statuszahl == "2") echo '<a href="?view=status&id='.$id.'&doedit=5&ct='.$time.'">Diesen Job beenden</a><br><br>';

for($x=1;$x<=18;$x++){
	if ($x-1 == $aufgabeid) {$select = ' selected';} else {$select = '';}
	$ArbeitSelect .= '<option value="'.$x.'"'.$select.'>'.getaufgabe($x);
}

$BenutzerSelect .= '<option value="0">----';
$result = mysql_query("SELECT ID,Name FROM Benutzer WHERE active='1' ORDER BY Gast, Name");
while($row = mysql_fetch_row($result))
{
$BenutzerSelect .= '<option value="'.$row[0].'">'.$row[1];
}

if ($statuszahl != 3)
{
echo <<<ECHOENDE
<form action="?view=status&id=$id&doedit=3&ct=$time" method="post">
Job beenden und naechsten oeffnen:<br>
<select name="neuaufgabe">
$ArbeitSelect
</select> fuer 
<select name="neubenutzer">
$BenutzerSelect
</select> <input type="submit" name="Ok" value="Ok">
</form>
ECHOENDE;
} else if ($statuszahl == 3)
{
echo <<<ECHOENDE
<form action="?view=status&id=$id&doedit=6&ct=$time" method="post">
Naechsten Job oeffnen:<br>
<select name="neuaufgabe">
$ArbeitSelect
</select> fuer 
<select name="neubenutzer">
$BenutzerSelect
</select> <input type="submit" name="Ok" value="Ok">
</form>
ECHOENDE;
}

} else if ($doedit == "1") {
	if (!checktime($ct)) die($timeout);
	mysql_query("UPDATE Folgen SET Status='1',Datum=$time,active='1' WHERE ID='$id'");
	header("Location: ?view=status&id=$id");
} else if ($doedit == "2") {
	if (!checktime($ct)) die($timeout);
	mysql_query("UPDATE Folgen SET Status='2',Datum=$time,active='1' WHERE ID='$id'");
	header("Location: ?view=status&id=$id");
} else if ($doedit == "3") {
	if (!checktime($ct)) die($timeout);
	mysql_query("UPDATE Folgen SET Status='3',Datum=$time,active='0' WHERE ID='$id'");
	if ($neuaufgabe == $release) {
		mysql_query("INSERT INTO Folgen (Serie,FolgenNummer,Datum,Aufgabe,Benutzer,Status,active) VALUES ($serienid,$episodennummer,$time,$neuaufgabe,$neubenutzer,'3','0')");
	} else {
		mysql_query("INSERT INTO Folgen (Serie,FolgenNummer,Datum,Aufgabe,Benutzer,Status,active) VALUES ($serienid,$episodennummer,$time,$neuaufgabe,$neubenutzer,'1','1')");
	}
	header("Location: ?view=serie&id=$serienid");
} else if ($doedit == "4") {
	if (!checktime($ct)) die($timeout);
	mysql_query("DELETE FROM Folgen WHERE ID='$id'");

	header("Location: ?view=serie&id=$serienid");
} else if ($doedit == "5") {
	if (!checktime($ct)) die($timeout);
	mysql_query("UPDATE Folgen SET Status='3',Datum=$time,active='0' WHERE ID='$id'");
	header("Location: ?view=status&id=$id");
} else if ($doedit == "6") {
	if (!checktime($ct)) die($timeout);
	if ($neuaufgabe == $release) {
		mysql_query("INSERT INTO Folgen (Serie,FolgenNummer,Datum,Aufgabe,Benutzer,Status,active) VALUES ($serienid,$episodennummer,$time,$neuaufgabe,$neubenutzer,'3','0')");
	} else {
		mysql_query("INSERT INTO Folgen (Serie,FolgenNummer,Datum,Aufgabe,Benutzer,Status,active) VALUES ($serienid,$episodennummer,$time,$neuaufgabe,$neubenutzer,'1','1')");
	}

	header("Location: ?view=serie&id=$serienid");
}

}

else if ($view == "comment")

{

$result = mysql_query("SELECT * FROM Folgen WHERE ID='$id'");
while($row = mysql_fetch_row($result))
{
	$serienid = $row[1];
	$id = $row[0];
	$episodennummer = $row[2];
	$datum = getdatum($row[3]);
	$zeit = getzeit($row[3]);
	$aufgabe = getaufgabe($row[4]);
	$benutzer = getuser($row[5]);
	$status = getstatus($row[6]);
	$comment = $row[7];
	$commentedit = ereg_replace("<br>","\n",$row[7]);
	$rowcolor = getrowcolor($row[6]);
}

$result = mysql_query("SELECT Name FROM Serien WHERE ID='$serienid'");
while($row = mysql_fetch_row($result)) 
{
	$serienname = $row[0];
}

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
<h1>Episodenstatus: $serienname $episodennummer</h1><hr>
<br>
<table border=0>
	<tr>
		<td width=30>Ep</td>
		<td width=100>Datum</td>
		<td width=110>Job</td>
		<td width=130>Benutzer</td>
		<td width=80>Status</td>
		<td width=200>Info</td>
	</tr>
	<tr bgcolor="$rowcolor">
		<td valign="top">$episodennummer</td>
		<td valign="top">$datum<br>$zeit</td>
		<td valign="top">$aufgabe</td>
		<td valign="top">$benutzer<br><small><a href="?view=benutzer&id=$id">aendern</a></small></td>
		<td valign="top">$status<br><small><a href="?view=status&id=$id">aendern</a></small></td>
		<td valign="top"><small><a href="?view=comment&id=$id">...</a> $comment</small></td>
	</tr>
</table>
<br>
<form action="?view=comment&id=$id&doedit=1&ct=$time" method="post">
<textarea cols="60" rows="15" name="newcomment">$commentedit</textarea>
<input type="submit" name="Abspeichern" value="Abspeichern">
</form>
ECHOENDE;
}
else
{
	if (!checktime($ct)) die($timeout);
	$newcomment = ereg_replace("\n","<br>",$newcomment);
	mysql_query("UPDATE Folgen SET comment='$newcomment',Datum='$time' WHERE ID='$id'");
	header("Location: ?view=comment&id=$id");
}

}
?>