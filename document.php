<!DOCTYPE HTML>
<html>
<head>
    <title>Document - Final Form</title>
     
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="lib/css/style.css"/>
 
</head>
<body>
    <?php

    include 'config/database.php';
    include_once 'config/paging_config.php';
    
    $id = isset($_GET['id']) ? $_GET['id'] : "";

    // select data for current page
    $query = "SELECT form_name FROM form WHERE form_id = ? ";
    $stmt = $con->prepare($query);
    // this is the first question mark
    $stmt->bindParam(1, $id);
    $stmt->execute();
    // store retrieved row to a variable
    $row_form = $stmt->fetch(PDO::FETCH_ASSOC);
    
    ?>
    <!-- container -->
    <div class="container">
        <div class="page-header">
            <h1>Document</h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $row_form['form_name']; ?></li>
            </ol>
        </nav>

        <a href='create.php' class='btn btn-primary m-b-1em'>Create New Document</a>

        <?php
        $query = "SELECT * FROM label WHERE form_id = ? ORDER BY label_order";
        $stmt = $con->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $order_arr = [];
        
        echo "<table class='table table-hover table-responsive table-bordered'>";//start table

            echo "<tr>";
                echo "<th>#</th>";
                for ($i=1; $row = $stmt->fetch(PDO::FETCH_ASSOC); $i++){
                    echo "<th>{$row['label_name']}</th>";
                    array_push($order_arr, $row['label_id']);
                }
                echo "<th>Actions</th>";
            echo "</tr>";

// $query = "SELECT label_id, data_group_id, data_value FROM data WHERE form_id = ? ORDER BY data_group_id, label_id";

$query = "SELECT d.label_id as label_id, d.data_group_id as data_group_id, d.data_value as data_value, dg.data_group_status as data_group_status FROM data as d LEFT JOIN data_group as dg ON d.data_group_id = dg.data_group_id WHERE d.form_id = ? ORDER BY d.data_group_id, d.label_id";

$stmt = $con->prepare($query);
$stmt->bindParam(1, $id);
$stmt->execute();
$data_num = $stmt->rowCount();

$data_arr = [];

//check if more than 0 record found
if($data_num>0){
     
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    for ($i=1; $row = $stmt->fetch(PDO::FETCH_ASSOC); $i++) {
        // collect data to an array
        $data_arr[$row['data_group_id']]['status'] = $row['data_group_status'];
        $data_arr[$row['data_group_id']][$row['label_id']] = $row['data_value'];
    }

    // key of $data_arr may be not start by 1
    $data_key = 1;
    foreach ($data_arr as $key => $data_row) {
        echo "<tr>";
            echo "<td>{$data_key}</td>";
            foreach ($order_arr as $order_id) {
                echo "<td>{$data_row[$order_id]}</td>";
            }
            echo "<td>";
                if ($data_row['status'] == 1) {
                    echo "<a href='read_one.php?id={$key}' class='btn btn-info m-r-1em'>Manage</a>";
                    echo "<a href='form_update.php?id={$key}' class='btn btn-primary m-r-1em'>Edit</a>";
                    echo "<a href='#' onclick='delete_doc({$key}, {$id});'  class='btn btn-danger'>Delete</a>";
                } else {
                    echo "<a href='#' onclick='activate_doc({$key}, {$id});'  class='btn btn-success'>Activate</a>";
                }
            echo "</td>";
        echo "</tr>";
        $data_key++;
    }
    
    echo "</table>";
        
    // PAGINATION
    $total_rows = $data_num;
    include_once "paging.php";

} else {
    // if no records found
    echo "<div class='alert alert-danger'>No records found.</div>";
}
?>
         
    </div> <!-- end .container -->
     
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type='text/javascript'>

        function delete_doc( doc_id, form_id ){
            var answer = confirm('Are you sure?');
            if (answer){
                window.location = 'doc_delete.php?did=' + doc_id + '&fid=' + form_id;
            } 
        }

        function activate_doc( doc_id, form_id ){        
            var answer = confirm('Are you sure?');
            if (answer){
                window.location = 'doc_activate.php?did=' + doc_id + '&fid=' + form_id;
            } 
        }
    </script>
 
</body>
</html>