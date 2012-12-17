<?php

switch ($_REQUEST['action']) {
    case 'sample1':
        echo 'Sample 1 - success';
        break;
    case 'sample2':
        echo 'Sample 2 - success, name = ' . $_POST['name'] . ', nickname= ' . $_POST['nickname'];
        break;
    case 'sample3':
        echo "$('.results').html('Sample 3 - javascript evaluating');";
        break;
    case 'sample4':
        header ('Content-Type: application/xml; charset=UTF-8');

        echo <<<XML
<?xml version='1.0' standalone='yes'?>
<items>
<item>Item 1</item>
<item>Item 2</item>
<item>Item 3</item>
<item>Item 4</item>
<item>Item 5</item>
</items>
XML;
        break;
    case 'sample5':
        $aRes = array('name' => 'Andrew', 'nickname' => 'Aramis');

        require_once('Services_JSON.php');
        $oJson = new Services_JSON();
        echo $oJson->encode($aRes);
        break;
}

?>
