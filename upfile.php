<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');   
header('Content-Type: text/html;charset=utf-8'); 

require 'UploadFile.class.php';
if(isset($_POST['sf']) && $_POST['sf']=='sf'){
  if ($_FILES["file"]["error"] > 0){
    echo "Error: " . $_FILES["file"]["error"] . "<br />";
  }else{
    $file = $_FILES['file']; 
    $upload = new UploadFile(true, './images/', array('jpg', 'jpeg', 'png', 'rvt', 'so', 'ifc'), $callback);
    $upload->upload_file($file);
    echo json_encode($upload->get_msg());
  }
}
?>