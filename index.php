<?php

error_reporting(0);
date_default_timezone_set("Asia/Kolkata");

/*
CREATE TABLE  `pasteit` ( `id` INT NOT NULL AUTO_INCREMENT , `datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `text` TEXT NULL , `link` TEXT NULL , `file` TEXT NULL , PRIMARY KEY (`id`)) 
*/
$url = getenv("CLEARDB_DATABASE_URL") != NULL ? getenv("CLEARDB_DATABASE_URL") : "mysql://ba2a05339b82cb:45979acf@us-cdbr-east-04.cleardb.com/heroku_483aed96e617ebc?reconnect=true";
$creds = parse_url($url);

$GLOBALS["mysql_hostname"] = $creds["host"];
$GLOBALS["mysql_port"] = "5432";
$GLOBALS["mysql_username"] = $creds["user"];
$GLOBALS["mysql_password"] = $creds["pass"];
$GLOBALS["mysql_database"] = substr($creds["path"], 1);

/*
$GLOBALS["mysql_hostname"] = "127.0.0.1";
$GLOBALS["mysql_username"] = "root";
$GLOBALS["mysql_password"] = "";
$GLOBALS["mysql_database"] = "mydb";
*/
$table = "clip";
function mysqlc()
{

    $mysqli = new mysqli($GLOBALS["mysql_hostname"], $GLOBALS["mysql_username"], $GLOBALS["mysql_password"], $GLOBALS["mysql_database"]);
    if ($mysqli->connect_error)
        throw new Exception("MySQL connection could not be established: " . $mysqli->connect_error);
    return $mysqli;
}
function truncate($string, $length = 500,  $dots = "...")
{
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}

function fileUpload()
{
    if (!empty($_FILES['uploaded_file']) && !empty($_FILES['uploaded_file']['name'])) {
        $path = "uploads/";
        $path = $path . time() . '_' . basename($_FILES['uploaded_file']['name']);

        if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $path)) {
            echo "<div style='font-size:3vh;padding:6px;width:100%;background-color:green;color:white;'>The file " .  basename($_FILES['uploaded_file']['name']) .
                " has been uploaded</div>";
            return '' . $path;
        } else {
            echo "There was an error uploading the file, please try again!";
        }
    }
}

function execute($q)
{
    $mysqli = mysqlc();
    $query = $mysqli->query($q);
    // echo $mysqli->insert_id;
    $mysqli->close();
    return $query;
}

function write($text, $link, $file)
{
    if ($text == NULL && $link == NULL && $file == NULL) {
        return;
    }
    if ($text == NULL) {
        $text = "";
    }
    if ($link == NULL) {
        $link = "";
    } else {
        if (substr($link, 0, 4) === "http") {
        } else {
            $link = 'http://' . $link;
        }
    }
    if ($file == NULL) {
        $file = "";
    }
    $dbc = mysqlc();
    $text = mysqli_real_escape_string($dbc, $text);
    $link = mysqli_real_escape_string($dbc, $link);
    $file = mysqli_real_escape_string($dbc, $file);

    $sql =  "INSERT INTO `pasteit` (`id`, `datetime`, `text`, `link`, `file`) VALUES (NULL, current_timestamp(), '$text', '$link', '$file')";
    // echo $sql;
    execute($sql);
}

function getClips()
{
    $results = (execute("SELECT * FROM  `pasteit`  ORDER BY  `datetime` DESC "));

    $clips = array();
    while ($row = $results->fetch_assoc()) {
        array_push($clips, $row);
    }
    // echo json_encode($clips);

    return $clips;
}

$data = $_POST;
if ($data != NULL) {
    function prompt($prompt_msg)
    {
        echo ("<script type='text/javascript'> var answer = prompt('" . $prompt_msg . "'); </script>");
        $answer = "<script type='text/javascript'> document.write(answer); </script>";
        return ($answer);
    }

    $prompt_msg = "Capthca : Enter aaa to prove you are not a robot.";

    write($data["text"], $data["link"], fileUpload());
}

if (isset($_GET["view"])) {
    $sql = 'SELECT * FROM `pasteit` where  id=' . $_GET["view"];
    $results = (execute($sql));
    if ($results->num_rows > 0) {
        $to_del = $results->fetch_object();
        echo '<p>' . $to_del->text . '</p>';
        echo '<br>';
        echo  '<p>' . $to_del->link . '</p>';
        echo '<br>';
        echo  '<p>' . $to_del->file . '</p>';
        die;
    }
}
if (isset($_GET["del"])) {
    $sql = 'SELECT * FROM `pasteit` where  id=' . $_GET["del"];
    $results = (execute($sql));
    if ($results->num_rows > 0) {

?>

        <div style=" padding:1px;text-align:center; background-color:black;width:100%;">


            <p style=" color:#ff4949">DELETED <br>


                <?php

                $to_del = $results->fetch_object();
                echo $to_del->text;
                echo '<br>';
                echo  $to_del->link;
                echo '<br>';

                $file_pointer =  $to_del->file;
                if (!empty($file_pointer)) {
                    if (!unlink($file_pointer)) {
                        echo ("$file_pointer cannot be deleted due to an error");
                    } else {
                        echo ("$file_pointer has been deleted");
                    }
                }
                $sql = 'DELETE FROM `pasteit` where  id=' . $_GET["del"];
                execute($sql);
                ?>
                <br>
            </p>

        </div>
<?php
    }
}
$clips = getClips();
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
            <a class="weighed weight2 rounded" style="background-color: transparent;" href="/">
                <div  onclick="location.href='/';" >
                <h2 style="font-family: 'Monospace', serif;">Paste It</h2></div></a>

                <div onclick="location.href='/';" class="weighed weight1 rounded">
                    <h2>Refresh</h2>
                </div>
            </div>
        </div>

        <div class="inputarea">

            <form enctype="multipart/form-data" action="" method="post" id="clip">

                <label for="text">Paste Text</label>
                <textarea id="text" name="text"></textarea>
                <br>
                <br>
                <label for="link">Paste Link</label>
                <textarea id="link" name="link"></textarea>
                <br>
                <br>
                <label for="file">Upload File</label>
                <br><input style="font-size: 2.5vh;" id="file" type="file" name="uploaded_file"></input>
                <div onclick="document.getElementById('file').click();" class="parent">
                    <span class="child"><br></span>
                </div>
                <div>



                </div>
                <br>
                <button style="font-size: 2.5vh;width:98%;background-color: #43A047;" class="weighed weight1 rounded" type="submit">Paste It</button>
            </form>

        </div>
        <hr>
        <div class="outputarea">

            <table>
                <?php
                function link_p_if($data)
                {
                    if ($data != NULL && strlen($data) > 0) {
                        echo "<p><a href='$data'>$data</a></p>";
                    }
                }
                function trunc_p_if($data)
                {
                    if ($data != NULL && strlen($data) > 0) {
                        $trunc = truncate($data);
                        echo "<p>$trunc</p>";
                    }
                }
                foreach ($clips as $v) {
                    $link = $v['link'];
                    $text = $v['text'];
                    $file = $v['file'];
                ?>
                    <ul style="width: auto;">
                        <div> <span class="subtitle"><?php echo $v["datetime"]; ?><br></span>
                            <?php trunc_p_if($text) ?>
                            <?php link_p_if($link) ?>
                            <?php link_p_if($file) ?>

                        </div>
                        <div class="center wrapper">
                            <a style="margin-right: 1vh;" class="weight1 button blue" href='?view=<?php echo $v['id']; ?>'>VIEW</a>
                            <a class="weight1 button red" href='?del=<?php echo $v['id']; ?>'>DELETE</a>

                        </div>
                    </ul>
                    <hr>

                <?php
                }
                ?>
            </table>



        </div>


    </div>

</body>


<style>
    ul {
        margin-left: 7px;
        padding-left: 7px;
        margin-bottom: 4px;
        margin-top: 4px;
    }

    .center {
        text-align: center;
        align-items: center;
    }

    .outputarea {
        margin: 1vh;
    }

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
        flex-wrap: wrap;
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
        flex: 3;
    }

    .green {
        background-color: #66BB6A;
    }

    .blue {
        background-color: #1565C0;
    }

    .red {
        background-color: #F44336;
    }

    a {
        text-decoration: none;
    }

    .button {
        text-decoration: none;
        color: white;
        align-self: center;
        min-width: 200px;
        max-height: 100px;
        max-width: 80vh;
        padding: 12px 25px;
        font-size: 12px;
        letter-spacing: 1px;
        text-transform: uppercase;
        border: 0;
        border-radius: 7px;
        outline: 0;
        box-shadow: 3px 3px 20px rgba(0, 0, 0, 0.2);
        -webkit-transition: all .2s;
        transition: all .2s;
        cursor: pointer;
    }

    .subtitle {
        font-size: 2vh;
        color: #616161;
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

    .button {
        font-size: 2.5vh;

    }
</style>


</html>