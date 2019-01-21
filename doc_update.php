<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Update Document - Final Form</title>
     
    <link rel="stylesheet" href="lib/css/bootstrap.css"/>
    <link rel="stylesheet" href="lib/css/style.css"/>
         
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Update Document</h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Document</li>
            </ol>
        </nav>
        <?php 
            include 'config/database.php';

            $id = isset($_GET['id']) ? $_GET['id'] : "";
            $group = isset($_GET['group']) ? $_GET['group'] : "";

            if ($_POST) {
                $query = "UPDATE data SET data_value = :data_value WHERE form_id = :form_id AND label_key = :label_key AND data_group_id = :data_group_id";
                for ($i=0; $i<count($_POST['label_key']); $i++) {
                    $stmt = $con->prepare( $query );

                    $stmt->bindParam(':data_value', $_POST['data_value'][$i]);
                    $stmt->bindParam(':form_id', $id);
                    $stmt->bindParam(':label_key', $_POST['label_key'][$i]);
                    $stmt->bindParam(':data_group_id', $group);
                    
                    if ($stmt->execute()) {
                        echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=document.php?id=$id&action=update'>";
                    } else {
                        echo "<div class='alert alert-danger'>Server error. Please contact administrator.</div>";
                    }
                }
            }
            // end if ($_POST

            $label_query = "SELECT l.label_key, l.label_name, d.data_value FROM label as l LEFT JOIN data as d ON l.label_key = d.label_key WHERE l.form_id = ? AND d.data_group_id = ? ORDER BY l.label_order";
            $label_stmt = $con->prepare($label_query);
            $label_stmt->bindParam(1, $id);
            $label_stmt->bindParam(2, $group);
            $label_stmt->execute();

            $data_arr = [];
            for ($i=0; $row = $label_stmt->fetch(PDO::FETCH_ASSOC); $i++){
                $data_arr[] = $row;
            }

        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]);?>" method="post">
            <table class='table table-hover table-responsive table-bordered'>
                <?php
                    foreach ($data_arr as $row) {
                        echo "<tr>";
                            echo "<td class='text-right'>{$row['label_name']}</td>";
                            echo "<td>";
                                echo "<input type='hidden' name='label_key[]' value='{$row['label_key']}' class='form-control' />";
                                echo "<input type='text' name='data_value[]' value='{$row['data_value']}' class='form-control' />";
                            echo "</td>";
                        echo "</tr>";
                    }
                ?>
                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Update' class='btn btn-primary' />
                        <a href='document.php?id=<?=$id?>' class='btn btn-danger'>Back to Document</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <script type="text/javascript" src="lib/js/jquery.js"></script>
    <script type="text/javascript" src="lib/js/bootstrap.js"></script>
</body>
</html>