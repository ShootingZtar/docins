<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Document - Final Form</title>
     
    <link rel="stylesheet" href="lib/css/bootstrap.css"/>
    <link rel="stylesheet" href="lib/css/style.css"/>
         
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Create Document</h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Document</li>
            </ol>
        </nav>
        <?php 
            include 'config/database.php';

            $id = isset($_GET['id']) ? $_GET['id'] : "";

            if ($_POST) {
                $raw_label_key_list = $_POST['label_key'];
                $raw_data_list = $_POST['data_value'];
                $label_key_list = [];
                $data_list = [];
                for ($i=0; $i<count($raw_data_list); $i++) {
                    if (isset($raw_data_list[$i]) && $raw_data_list[$i] != '' && $raw_data_list[$i] != NULL) {
                        array_push($label_key_list, $raw_label_key_list[$i]);
                        array_push($data_list, $raw_data_list[$i]);
                    }
                }
                
                if (count($data_list) > 0) {
                    $query = "INSERT INTO data_group SET data_group_status = 1";
                    $stmt = $con->prepare($query);
                    $stmt->execute();

                    $query = "SELECT data_group_id FROM data_group ORDER BY data_group_id DESC LIMIT 0,1";
                    $stmt = $con->prepare($query);
                    $stmt->execute();
                    $data_group_fetch = $stmt->fetch(PDO::FETCH_ASSOC);
                    $last_data_group_id = $data_group_fetch['data_group_id'];


                    $question_marks = str_repeat('( ?, ?, ?, ? ), ', count($data_list)-1) . '( ?, ?, ?, ? )';
                    $query = "INSERT INTO data (form_id, data_group_id, label_key, data_value) VALUE $question_marks";
                    $stmt = $con->prepare( $query );
                    for ($i=0; $i<count($data_list); $i++) {
                        $index_counter = ( $i * 4 );

                        $index_first = $index_counter + 1;
                        $index_second = $index_counter + 2;
                        $index_third = $index_counter + 3;
                        $index_fourth = $index_counter + 4;

                        $stmt->bindParam($index_first, $id);
                        $stmt->bindParam($index_second, $last_data_group_id);
                        $stmt->bindParam($index_third, $label_key_list[$i]);
                        $stmt->bindParam($index_fourth, $data_list[$i]);
                    }
                    try {
                        $stmt->execute();
                    } catch (PDOException $exception) {
                        die('ERROR: ' . $exception->getMessage());
                    }
                    echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=document.php?id=$id&action=create'>";
                } else {
                    echo "<div class='alert alert-danger'>Form name can't be blank and should have at least 1 column with data filled.</div>";
                }
            }
            // end if ($_POST)

            $label_query = "SELECT label_key, label_name FROM label WHERE form_id = ? ORDER BY label_order";
            $label_stmt = $con->prepare($label_query);
            $label_stmt->bindParam(1, $id);
            $label_stmt->execute();

        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]);?>" method="post">
            <table class='table table-hover table-responsive table-bordered'>
                <?php
                    for ($i=1; $row = $label_stmt->fetch(PDO::FETCH_ASSOC); $i++){ 
                        echo "<tr>";
                            echo "<td class='text-right'>{$row['label_name']}</td>";
                            echo "<td>";
                                echo "<input type='hidden' name='label_key[]' value='{$row['label_key']}' class='form-control' />";
                                echo "<input type='text' name='data_value[]' class='form-control' />";
                            echo "</td>";
                        echo "</tr>";
                    }
                ?>
                <tr>
                    <td></td>
                    <td colspan='2'>
                        <input type='submit' value='Create' class='btn btn-primary' />
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