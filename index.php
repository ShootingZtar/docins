<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home - Final Form</title>
     
    <link rel="stylesheet" href="lib/css/bootstrap.css"/>
    <link rel="stylesheet" href="lib/css/style.css"/>
 
</head>
<body>
 
    <div class="container"> 
        <div class="page-header">
            <h1>Form</h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Home</li>
            </ol>
        </nav>
        <a href='form_create.php' class='btn btn-primary m-b-1em'>Create New Form</a>
<?php

include 'config/database.php';
include_once 'config/paging_config.php';
 
$action = isset($_GET['action']) ? $_GET['action'] : "";
 
// if it was redirected with action
switch ($action) {
    case 'deleted' : echo "<div class='alert alert-warning'>Record was deleted.</div>"; break;
    case 'activate' : echo "<div class='alert alert-success'>Record was activated.</div>"; break;
    case 'create' : echo "<div class='alert alert-success'>Created success.</div>"; break;
    case 'update' : echo "<div class='alert alert-success'>Updated success.</div>"; break;
}
 
$query = "SELECT * FROM form ORDER BY form_last_modified DESC LIMIT :from_record_num, :records_per_page";
 
$stmt = $con->prepare($query);
$stmt->bindParam(":from_record_num", $from_record_num, PDO::PARAM_INT);
$stmt->bindParam(":records_per_page", $records_per_page, PDO::PARAM_INT);
$stmt->execute();

$num = $stmt->rowCount();
 
//check if more than 0 record found
if($num>0){
 
    echo "<table class='table table-hover table-responsive table-bordered'>";
    echo "<tr>";
        echo "<th>#</th>";
        echo "<th>Form Name</th>";
        echo "<th>Create By</th>";
        echo "<th>Create Time</th>";
        echo "<th>Last Modified By</th>";
        echo "<th>Last Modified Time</th>";
        echo "<th>Actions</th>";
    echo "</tr>";

    for ($i=1; $row = $stmt->fetch(PDO::FETCH_ASSOC); $i++){
        // extract row
        // this will make $row['firstname'] to
        // just $firstname only
        extract($row);
        
        // creating new table row per record
        echo "<tr>";
            echo "<td>{$i}</td>";
            echo "<td>{$form_name}</td>";
            echo "<td>{$form_created_by}</td>";
            echo "<td>{$form_created_time}</td>";
            echo "<td>{$form_modified_by}</td>";
            echo "<td>{$form_last_modified}</td>";
            echo "<td>";
                if ($form_status == '1') {
                    echo "<a href='document.php?id={$form_id}' class='btn btn-info m-r-1em'>Manage</a>";
                    echo "<a href='form_update.php?id={$form_id}' class='btn btn-primary m-r-1em'>Edit</a>";
                    echo "<a href='#' onclick='delete_form({$form_id});'  class='btn btn-danger'>Delete</a>";
                } else {
                    echo "<a href='#' onclick='activate_form({$form_id});'  class='btn btn-success'>Activate</a>";
                }
            echo "</td>";
        echo "</tr>";
    }

    echo "</table>";
        
    // PAGINATION
    // count total number of rows
    $query = "SELECT COUNT(*) as total_rows FROM form";
    $stmt = $con->prepare($query);
    
    // execute query
    $stmt->execute();
    
    // get total rows
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_rows = $row['total_rows'];

    // paginate records
    include_once "paging.php";

}
 
// if no records found
else{
    echo "<div class='alert alert-danger'>No records found.</div>";
}
?>
         
    </div> <!-- end .container -->
     
<script type="text/javascript" src="lib/js/jquery.js"></script>
<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<script type='text/javascript'>
    function delete_form( id ){
        var answer = confirm('Are you sure?');
        if (answer){
            window.location = 'form_delete.php?id=' + id;
        } 
    }

    function activate_form( id ){
        var answer = confirm('Are you sure?');
        if (answer){
            window.location = 'form_activate.php?id=' + id;
        } 
    }
</script>
 
</body>
</html>