<?php
include 'config/database.php';
 
try {
    $did=isset($_GET['did']) ? $_GET['did'] : die('ERROR: Document ID not found.');
    $fid=isset($_GET['fid']) ? $_GET['fid'] : die('ERROR: Form ID not found.');

    $query = "UPDATE data_group SET data_group_status='0' WHERE data_group_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bindParam(1, $did);

    if($stmt->execute()){
        header('Location: document.php?id=' . $fid . '&action=deleted');
    }else{
        die('Unable to delete record.');
    }
} catch (PDOException $exception) {
    die('ERROR: ' . $exception->getMessage());
}
?>