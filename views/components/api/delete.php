<?php

require_once __DIR__ . '/../../../utils/db_conn.php';
$method = $_SERVER['REQUEST_METHOD'];
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));

if ($method === 'GET') {

    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }else{
        $id=$_GET['id'];
        $type=$_GET['type'];
        if($type=='notice'){
         $sql="DELETE FROM notices WHERE `id` = $id";
            $stmt = $conn->prepare($sql);
            $success = $stmt->execute();
            if($success){
                header("Location: /Journal/views/dashboard.php?page=notice&status=delete_success");
                
            }else{
                header("Location: /Journal/views/dashboard.php?page=notice&status=delete_error");
                
            }
        }else if($type=='article'){
         $sql="UPDATE article SET status='deleted' WHERE `article_id` = $id";
            $stmt = $conn->prepare($sql);
            $success = $stmt->execute();
            if($success){
                header("Location: /Journal/views/dashboard.php?page=article&status=delete_success");
                
            }else{
                header("Location: /Journal/views/dashboard.php?page=article&status=delete_error");
                
            }

        }
    }
}
