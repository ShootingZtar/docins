<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document - Final Form</title>
     
    <link rel="stylesheet" href="lib/css/bootstrap.css"/>
    <link rel="stylesheet" href="lib/css/style.css"/>
 
</head>
<body>
    <?php

        include 'config/database.php';
        include_once 'config/paging_config.php';
        
        $id = isset($_GET['id']) ? $_GET['id'] : "";

        $query = "SELECT form_name FROM form WHERE form_id = ? ";
        $stmt = $con->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
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

        <?php

        $action = isset($_GET['action']) ? $_GET['action'] : "";
                
        // if it was redirected with action
        switch ($action) {
            case 'deleted' : echo "<div class='alert alert-warning'>Record was deleted.</div>"; break;
            case 'activate' : echo "<div class='alert alert-success'>Record was activated.</div>"; break;
            case 'create' : echo "<div class='alert alert-success'>Created success.</div>"; break;
            case 'update' : echo "<div class='alert alert-success'>Updated success.</div>"; break;
        }
        echo "<a href='doc_create.php?id=$id' class='btn btn-primary m-b-1em'>Create New Document</a>";

        $query = "SELECT * FROM label WHERE form_id = ? ORDER BY label_order";
        $stmt = $con->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $order_arr = [];
        $label_showed = ['ชื่อ-สกุล_ผู้ซื้อ', 'ชื่อ-สกุล_ผู้ขาย', 'เลขที่ห้องชุด', 'เนื้อที่รวม_(ตร.ม.)', 'ชื่ออาคารชุด', 'ราคา', 'จำนวนเงินมัดจำ', 'จำนวนเงินผ่อนชำระแต่ละงวด_'];
        
        echo "<table class='table table-hover table-responsive table-bordered'>";//start table

            echo "<tr>";
                echo "<th>#</th>";
                for ($i=1; $row = $stmt->fetch(PDO::FETCH_ASSOC); $i++){
                    if(in_array($row['label_key'], $label_showed)) {
                        echo "<th>{$row['label_name']}</th>";
                        array_push($order_arr, $row['label_key']);
                    }
                }
                echo "<th>Actions</th>";
            echo "</tr>";

            // $query = "SELECT d.label_key as label_key, d.data_group_id as data_group_id, d.data_value as data_value, dg.data_group_status as data_group_status, l.label_order FROM data as d INNER JOIN data_group as dg ON (d.data_group_id = dg.data_group_id) INNER JOIN label as l ON (d.label_key=l.label_key) WHERE d.form_id = ? AND dg.data_group_status = 1 ORDER BY d.data_group_id, l.label_order";

            $query = "SELECT d.label_key as label_key, d.data_group_id as data_group_id, d.data_value as data_value, dg.data_group_status as data_group_status, l.label_order FROM data as d INNER JOIN data_group as dg ON (d.data_group_id = dg.data_group_id) INNER JOIN label as l ON (d.label_key=l.label_key) WHERE d.form_id = ? ORDER BY d.data_group_id, l.label_order";

            $stmt = $con->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $data_num = $stmt->rowCount();

            $data_arr = [];

            //check if more than 0 record found
            if($data_num>0){
                for ($i=1; $row = $stmt->fetch(PDO::FETCH_ASSOC); $i++) {
                    // collect data to an array
                    $data_arr[$row['data_group_id']]['status'] = $row['data_group_status'];
                    $data_arr[$row['data_group_id']][$row['label_key']] = $row['data_value'];
                }

                // key of $data_arr maybe start by other
                $data_key = 1;
                foreach ($data_arr as $key => $data_row) {
                    echo "<tr>";
                        echo "<td>{$data_key}</td>";
                        foreach ($order_arr as $order_id) {
                            echo "<td>";
                                if (!empty($data_row[$order_id])) {
                                    echo $data_row[$order_id];
                                }
                            echo "</td>";
                        }
                        echo "<td>";
                            if ($data_row['status'] == 1) {
                                echo "<a href='example_pdf.php?id={$id}&group={$key}' target='_blank' class='btn btn-info m-r-1em'>Document</a>";
                                echo "<a href='doc_update.php?id={$id}&group={$key}' class='btn btn-primary m-r-1em'>Edit</a>";
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
                $total_rows = count($data_arr);
                include_once "paging.php";

            } else {
                // if no records found
                echo "<div class='alert alert-danger'>No records found.</div>";
            }
        ?>
         
    </div> <!-- end .container -->
     
    <script type="text/javascript" src="lib/js/jquery.js"></script>
    <script type="text/javascript" src="lib/js/bootstrap.js"></script>
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