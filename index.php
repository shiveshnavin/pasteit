<?php

error_reporting(0);


date_default_timezone_set("Asia/Kolkata");



 
if($_GET["cliph"])
{
 if(!$_GET["secure"] || $_GET["secure"]=="0")
       {
           echo "<script> alert('Seems You Are A BOT !!'); window.location.href=`index.php?_cliph=".$_GET["cliph"]."` </script>";
           die;
       }

}
else if($_GET["clip"])
{
 if(!$_GET["secure"] || $_GET["secure"]=="0")
       {
       
       echo "<script> alert('Seems You Are A BOT !!'); window.location.href=`index.php?_clip=".$_GET["clip"]."` </script>";

            die;
       }

}
 
//mysql://ba2a05339b82cb:45979acf@us-cdbr-east-04.cleardb.com/heroku_483aed96e617ebc?reconnect=true
$url = getenv("CLEARDB_DATABASE_URL")!= NULL ? getenv("CLEARDB_DATABASE_URL") : "mysql://ba2a05339b82cb:45979acf@us-cdbr-east-04.cleardb.com/heroku_483aed96e617ebc?reconnect=true";
$creds = parse_url($url);

$GLOBALS["mysql_hostname"] = $creds["host"];
$GLOBALS["mysql_port"] = "5432";
$GLOBALS["mysql_username"] = $creds["user"];
$GLOBALS["mysql_password"] = $creds["pass"];
$GLOBALS["mysql_database"] = substr($creds["path"], 1);

/*
$GLOBALS["mysql_hostname"] = "127.0.0.1";
$GLOBALS["mysql_username"] = "root";
$GLOBALS["mysql_password"] = "pass"; 
$GLOBALS["mysql_database"] = "test";*/

$table="clip";
function mysqlc()
{

            $mysqli = new mysqli($GLOBALS["mysql_hostname"], $GLOBALS["mysql_username"], $GLOBALS["mysql_password"], $GLOBALS["mysql_database"]);
			if( $mysqli->connect_error )
				throw new Exception("MySQL connection could not be established: ".$mysqli->connect_error);
            return $mysqli;

}

function execute($q){

        $mysqli=mysqlc();
        $query = $mysqli->query( $q); 
        $mysqli->close();
        return $query;
}

 
if(isset($_GET["view"]))
{


    $sql='SELECT * FROM `clip` where  id='.$_GET["view"];
    $results =( execute($sql)); 
    
     echo  $results->fetch_object()->blob;  
     die;

}
if(isset($_GET["del"]))
{
    ?>
 
    <div style=" padding:1px;text-align:center; background-color:black;width:100%;">


    <p style=" color:#ff4949">DELETED <br>
    
    
    <?php 
    $sql='SELECT * FROM `clip` where  id='.$_GET["del"];
    $results =( execute($sql)); 
    
     echo  $results->fetch_object()->blob; 
     
     
     ?>
     <br> 
     </p>
     
    </div>
     <?php
    

    $sql='DELETE FROM `clip` where  id='.$_GET["del"];
    $results =( execute($sql)); 
    
     

}
?>


<html>


 
 
 
 <table style="width:100%; background-color: #efefef;   ">

    <tr>

        <th> <a href='/'><h2>HOME</h2></a> </th>
        <th> <a href='http://35.206.65.195:3000/'><h2>UPLOAD</h2></a> </th>
        <th> <a href='http://hoptech.in/save.php'><h2>SAVE</h2></a> </th>
        <th> <a href='http://35.206.65.195:3000/secure'><h2>Secure-SAVE</h2></a> </th> 
       

    </tr>


 </table>

 <br> 
<div style="height:50px ;padding:1px;text-align:center; background-color:black;width:100%;">


<p style="height:11px;color:#42f483">Paste Bin </p>

</div>
<br> 
 <form action="index.php" method="get"  >
  
 
 Text:<br> 
 
  <textarea style="width:100%; background-color: white;"  type="text" name="clip" value="flush"  rows="5" cols="40"><?php 
  echo $_GET['_clip']?$_GET['_clip']:"";?></textarea><br>
  <select name="secure">

          <option value="0">I am Bot</option>
          <option value="1">I am Human</option>
  </select><br> 
  <br><input type="submit" value="Paste">
</form> 


 <form action="index.php" method="get"   >
  
LINK:<br> 
  <input style="width:100%; background-color: white;" type="text" name="cliph" value="<?php
  echo $_GET['_cliph']?$_GET['_cliph']:"LINK";?>"  rows="5" cols="40"><br>
  <select name="secure">

          <option value="0">I am Bot</option>
          <option value="1">I am Human</option>
  </select>
  <br><br> 
  <input type="submit" value="Paste"/>
</form> 

 <br> <br> 
 
<?php

$us= $_SERVER['HTTP_USER_AGENT'];
if(strpos($us,'bot') !==false || strpos($us,'Bot') !==false )
{

    echo "You a bot !! ".$ua;
    die;

}

 
function sp()
{
        echo '&nbsp;';
}
function flushClip()
{

        echo "FLUSHED";$date=date('Y_m_d_hh_mm');
 
        file_put_contents('clip/clip_'.$date.'.html',json_encode(getClips()));
                
            $sql='DELETE FROM `clip` where 1';
            $results =( execute($sql)); 
        write('http://snsk.co.nf/clip/'.'clip_'.$date.'.html','link','flusher');
}

function getClips()
{
      $results =(execute("SELECT * FROM  `clip`  ORDER BY  `date` DESC "));

      //echo json_encode($results);
        $clips=array();
        while($row = $results->fetch_assoc()) {
            array_push($clips, $row);
        }  
        return $clips;
       
}
function show()
{
      $clips= getClips();
        ?>

                <table id="clip" style="width:100%">
                    <tr>
                        <th>Blob</th>
                        <th>Date</th>
                        <th>View</th>
                        <th>Delete</th>
                    </tr>

        <?php
            
        $i = 0;  
        foreach ($clips as $v) {
            
            echo '<tr >';

             
            if($v["type"]=='link')
                echo sprintf('<td style="text-align:center"><a href="%s">%s</a></td>
                <td style="text-align:center">%s</td>
                <td style="text-align:center"><a href="?view=%s">VIEW</a></td>
                <td style="text-align:center"><a href="?del=%s">DELETE</a></td>'
                ,$v["blob"],$v["blob"],$v["date"],$v["id"],$v["id"]);
            else
                echo sprintf('<td style="text-align:center" >   %s</td>
                <td style="text-align:center">%s</td>
                <td style="text-align:center"><a href="?view=%s">VIEW</a></td>
                <td style="text-align:center"><a href="?del=%s">DELETE</a></td>',str_replace('\n','<br>',$v["blob"]),$v["date"],$v["id"],$v["id"]);

                 
            echo '</tr>';

            $i++;
        }

        ?>
        </table>
        <?php

}

function write ($blob,$type,$user)
{

    $mysqli=mysqlc();
    $sql=sprintf('INSERT INTO   `clip` (`type`,`blob`,`user`) VALUES ("%s","%s","%s");',
    mysqli_real_escape_string($mysqli ,$type),
    mysqli_real_escape_string($mysqli ,$blob),
    mysqli_real_escape_string($mysqli ,$user));

  //  echo $sql;

    $results =( execute($sql)); 
}

 


if(isset($_GET["clip"]) && $_GET["clip"]){

 
            if(strcmp($_GET["clip"],"flush")==0)
            {
                flushClip();
            }
            else{

                write($_GET["clip"],'text', (isset($_GET["user"])?$_GET["user"]:'default' ));

            }

 
}
else if(isset($_GET["cliph"]) && $_GET["cliph"]){


 

            if(strcmp($_GET["cliph"],"flush")==0)
            {

                flushClip();

            }
            else{
                if(substr( $_GET["cliph"], 0, 4 ) === "http")
                {}
                else{
                    $_GET["cliph"]='http://'.$_GET["cliph"];
                }

                write($_GET["cliph"],'link', (isset($_GET["user"])?$_GET["user"]:'default' ));
            }


}
 

show();

?>



 <title>ClipBoard : Hoptec Solutions </title>


<style>

    table#clip  tr:nth-child(even) {
        background-color: #eee;
    }
    table#clip  tr:nth-child(odd) {
        background-color: #fff;
    }
    table#clip th {
        color: white;
        background-color: black;
    }
    table#clip td {
        padding-top:20px;
        padding-bottom:20px;
        padding-right:20px; 
        padding-left:20px; 
    }
    table#clip, th, td {
    border: 0.6px solid black; 
}
</style> </html>