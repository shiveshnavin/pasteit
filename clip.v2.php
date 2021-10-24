<?php

error_reporting(E_ALL);
date_default_timezone_set("Asia/Kolkata");


$data = $_POST;
if ($data != NULL) {
    function prompt($prompt_msg)
    {
        echo ("<script type='text/javascript'> var answer = prompt('" . $prompt_msg . "'); </script>");
        $answer = "<script type='text/javascript'> document.write(answer); </script>";
        return ($answer);
    }

    $prompt_msg = "Capthca : Enter aaa to prove you are not a robot.";
}


if ($_GET["cliph"]) {
    if (!$_GET["secure"] || $_GET["secure"] == "0") {
        echo "<script> alert('Seems You Are A BOT !!'); window.location.href=`index.php?_cliph=" . $_GET["cliph"] . "` </script>";
        die;
    }
} else if ($_GET["clip"]) {
    if (!$_GET["secure"] || $_GET["secure"] == "0") {

        echo "<script> alert('Seems You Are A BOT !!'); window.location.href=`index.php?_clip=" . $_GET["clip"] . "` </script>";

        die;
    }
}

$url = getenv("CLEARDB_DATABASE_URL") != NULL ? getenv("CLEARDB_DATABASE_URL") : "mysql://ba2a05339b82cb:45979acf@us-cdbr-east-04.cleardb.com/heroku_483aed96e617ebc?reconnect=true";
$creds = parse_url($url);
/*
$GLOBALS["mysql_hostname"] = $creds["host"];
$GLOBALS["mysql_port"] = "5432";
$GLOBALS["mysql_username"] = $creds["user"];
$GLOBALS["mysql_password"] = $creds["pass"];
$GLOBALS["mysql_database"] = substr($creds["path"], 1);
*/

$GLOBALS["mysql_hostname"] = "127.0.0.1";
$GLOBALS["mysql_username"] = "root";
$GLOBALS["mysql_password"] = "";
$GLOBALS["mysql_database"] = "mydb";

$table = "clip";
function mysqlc()
{

    $mysqli = new mysqli($GLOBALS["mysql_hostname"], $GLOBALS["mysql_username"], $GLOBALS["mysql_password"], $GLOBALS["mysql_database"]);
    if ($mysqli->connect_error)
        throw new Exception("MySQL connection could not be established: " . $mysqli->connect_error);
    return $mysqli;
}

function execute($q)
{

    $mysqli = mysqlc();
    $query = $mysqli->query($q);
    $mysqli->close();
    return $query;
}

?>
<html>

<head>
    <title>PasteIt</title>
    <meta name="theme-color" content="#00796B" />
</head>

<body>

    <div class="main">

        <div class="m_navbar">
            <div style="background-color: #00796B;" class="wrapper">
                <div style="background-color: transparent;" onclick="location.href='';" class="weighed weight2 rounded">
                    <h2>Paste It</h2>
                </div>
                <div onclick="location.href='';" class="weighed weight1 rounded">
                    <h2>Refresh</h2>
                </div>
            </div>
        </div>

        <div class="inputarea">

            <form action="" method="post" id="clip">

                <label for="text">Paste Text</label>
                <textarea id="text" name="text"></textarea>
                <br>
                <br>
                <label for="link">Paste Link</label>
                <textarea id="link" name="link"></textarea>
                <br>
                <br>
                <label for="file">Upload File</label>
                <br>
                <div onclick="document.getElementById('file').click();" class="parent">
                    <span class="child"><br><input style="font-size: 2.5vh;" id="file" type="file" name="file"></input></span>
                </div>
                <div>


                </div>
                <br>
                <button style="font-size: 2.5vh;width:98%;background-color: #43A047;" class="weighed weight1 rounded" type="submit">Paste It</button>
            </form>

        </div>

    </div>

</body>


<style>
    body {
        margin: 0px;
        font-size: 2.5vh;
        font-family: Helvetica, sans-serif;
    }

    textarea {
        width: 100%;
        height: 10vh;
    }

    .inputarea {
        display: block;
        margin: 1vh;
    }

    .parent {

        border-style: solid;
        border-color: #00796B;
        border-width: 2px;
        border-radius: 5px;
        height: 10vh;
        background-color: #EEEEEE;
        position: relative;
    }

    .child {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        margin: auto;

    }

    .m_navbar {
        margin-bottom: 10px;
    }

    .main {
        display: block;
        height: 100%;
        width: 100%;
    }

    .rounded {
        border-radius: 3px;
    }

    .wrapper {
        display: flex;
        padding: 4px;
    }

    .weighed {
        margin: 5px;
        background-color: #00695C;
        cursor: pointer;
        color: white;
        text-align: center;
        height: 8vh;
        border: 5px solid transparent;
    }

    .weight1 {
        flex: 1;
    }

    .weight2 {
        flex: 2;
    }

    .weight3 {
        flex: 2;
    }



    /****FOTNS****/
    h1 {
        font-size: 5.9vw;
    }

    h2 {
        font-size: 3.0vh;
    }

    p {
        font-size: 3.0vh;
    }
</style>


</html>