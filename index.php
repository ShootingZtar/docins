<!DOCTYPE HTML>
<html>
<head>
    <title>Home - Final Form</title>
     
    <!-- Latest compiled and minified Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
         
    <!-- custom css -->
    <link rel="stylesheet" href="lib/css/style.css"/>
 
</head>
<body>
 
    <!-- container -->
    <div class="container">
  
        <div class="page-header">
            <h1>All Document Form</h1>
        </div>
     
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Home</li>
            </ol>
        </nav>
<?php

// include database connection
include 'config/database.php';

// PAGINATION VARIABLES
include_once 'config/paging_config.php';
 
$action = isset($_GET['action']) ? $_GET['action'] : "";
 
// if it was redirected from delete.php
if($action=='deleted'){
    echo "<div class='alert alert-success'>Record was deleted.</div>";
}
 
// select data for current page
$query = "SELECT * FROM form ORDER BY form_last_modified DESC
    LIMIT :from_record_num, :records_per_page";
 
$stmt = $con->prepare($query);
$stmt->bindParam(":from_record_num", $from_record_num, PDO::PARAM_INT);
$stmt->bindParam(":records_per_page", $records_per_page, PDO::PARAM_INT);
$stmt->execute();
 
// this is how to get number of rows returned
$num = $stmt->rowCount();
 
// link to create record form
echo "<a href='create.php' class='btn btn-primary m-b-1em'>Create New Form</a>";
 
//check if more than 0 record found
if($num>0){
 
    echo "<table class='table table-hover table-responsive table-bordered'>";//start table
 
    //creating our table heading
    echo "<tr>";
        echo "<th>#</th>";
        echo "<th>Form Name</th>";
        echo "<th>Create By</th>";
        echo "<th>Create Time</th>";
        echo "<th>Last Modified By</th>";
        echo "<th>Last Modified Time</th>";
        echo "<th>Actions</th>";
    echo "</tr>";
     
    // retrieve our table contents
// fetch() is faster than fetchAll()
// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
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
                // read one record 
                echo "<a href='read_one.php?id={$form_id}' class='btn btn-info m-r-1em'>Manage</a>";
                
                // we will use this links on next part of this post
                echo "<a href='form_update.php?id={$form_id}' class='btn btn-primary m-r-1em'>Edit</a>";
    
                // we will use this links on next part of this post
                echo "<a href='#' onclick='delete_user({$form_id});'  class='btn btn-danger'>Delete</a>";
            } else {
                echo "<a href='#' onclick='activate_user({$form_id});'  class='btn btn-success'>Activate</a>";
            }
        echo "</td>";
    echo "</tr>";
}
 
// end table
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
$page_url="index.php?";
include_once "paging.php";

}
 
// if no records found
else{
    echo "<div class='alert alert-danger'>No records found.</div>";
}
?>
         
    </div> <!-- end .container -->
     
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
   
<!-- Latest compiled and minified Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 
<script type='text/javascript'>
// confirm record deletion
function delete_user( id ){
     
    var answer = confirm('Are you sure?');
    if (answer){
        // if user clicked ok, 
        // pass the id to delete.php and execute the delete query
        window.location = 'delete.php?id=' + id;
    } 
}
// confirm record deletion
function activate_user( id ){
     
    var answer = confirm('Are you sure?');
    if (answer){
        // if user clicked ok, 
        // pass the id to delete.php and execute the delete query
        window.location = 'activate.php?id=' + id;
    } 
}
</script>
 
</body>
</html>