<?php
$serverIp = "192.168.0.10";
$companyName = "АИФ";
require_once ('MysqliDb.php');

header("Content-type: text/xml; charset=utf-8");
//header("Content-Type: text/html; charset=utf-8");
header("Connection: close");
header("Expires: -1");

if (empty ($_GET['directory'])) {
	echo "<?xml version='1.0' encoding='UTF-8'?> 
		<CiscoIPPhoneMenu>
		<MenuItem>
			<Name>{$companyName} phone book</Name>
			<URL>http://{$serverIp}/ciscoAbook/directory.php?directory=contacts</URL>
		</MenuItem>
		<MenuItem>
		    <Name>Search in {$companyName}</Name>
			<URL>http://{$serverIp}/ciscoAbook/directory.php?directory=search</URL>
			
		</MenuItem>
		</CiscoIPPhoneMenu>";
	exit;
} else if ($_GET['directory'] == 'search') {
	echo "<?xml version='1.0' encoding='UTF-8'?> 
		  <CiscoIPPhoneInput>
		  <Title>Directory search</Title>
		  <Prompt>Enter person name: </Prompt>
		  <URL>http://{$serverIp}/ciscoAbook/directory.php?directory=contacts</URL>
		  <InputItem>
			<DisplayName>First Name</DisplayName>
			<QueryStringParam>firstName</QueryStringParam>
			<InputFlags>U</InputFlags>
		  </InputItem>
		  <InputItem>
			<DisplayName>Last Name</DisplayName>
			<QueryStringParam>lastName</QueryStringParam>
			<InputFlags>U</InputFlags>
		  </InputItem>
		  <InputItem>
			<DisplayName>Extension</DisplayName>
			<QueryStringParam>extNum</QueryStringParam>
			<InputFlags>T</InputFlags>
		  </InputItem>

		</CiscoIPPhoneInput>";
	exit;
}


$db = new Mysqlidb ('localhost', 'root', 'mahapharata', 'phonebook');
//mysqli_set_charset($db, 'utf8');
//mysqli_query($db, "SET NAMES 'utf8'");

$name = $_GET['firstName'] . "% ". $_GET['lastName'] . "%";
$db->where ("description", Array ('LIKE'=> $name));
if (!empty ($_GET['extNum']))
	$db->where ("id", Array ('LIKE' => $_GET['extNum']."%"));

$users = $db->get("devices", 32);
foreach ($users as $u) {
	$list .= "<DirectoryEntry> 
          <Name>{$u['description']}</Name> 
          <Telephone>{$u['id']}</Telephone> 
	     </DirectoryEntry>";
}
echo "<?xml version='1.0' encoding='UTF-8'?> 
    <CiscoIPPhoneDirectory> 
    <Title>{$companyName} Phone Directory</Title> 
     <Prompt>People reachable via VoIP</Prompt>";
echo $list;
echo "</CiscoIPPhoneDirectory>";
