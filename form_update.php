<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Update Form - Final Form</title>
     
    <link rel="stylesheet" href="lib/css/bootstrap.css"/>
    <link rel="stylesheet" href="lib/css/style.css"/>
         
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Update Form</h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Form</li>
            </ol>
        </nav>
        <?php
            $id=isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

            include 'config/database.php';

            if ($_POST) {

                $form_name = $_POST['name'];
                $raw_name_list = $_POST['label_name'];
                $name_list = [];
                foreach ($raw_name_list as $l_name) {
                    if ($l_name != '' && $l_name != NULL) {
                        array_push($name_list, $l_name);
                    }
                }

                if ($form_name !== '' && count($name_list) > 0) {
                    $query = "UPDATE form SET form_name = ? WHERE form_id = ?";
                    $stmt = $con->prepare( $query );
                    $stmt->bindParam(1, $form_name);
                    $stmt->bindParam(2, $id);
                    try {
                        $stmt->execute();
                    } catch (PDOException $exception) {
                        die('ERROR: ' . $exception->getMessage());
                    }

                    $id_list = $_POST['label_id'];
                    // $name_list = $_POST['label_name'];

                    try {
                        $query = "SELECT label_id, label_order FROM label WHERE form_id = ? ORDER BY label_id DESC";
                        $stmt = $con->prepare( $query );
                        $stmt->bindParam(1, $id);
                        $stmt->execute();
                    } catch (PDOException $exception) {
                        die('ERROR: ' . $exception->getMessage());
                    }
                    $old_count = $stmt->rowCount();
                    $old_last_row = $stmt->fetch(PDO::FETCH_ASSOC);

                    $last_order = intval($old_last_row['label_order']);

                    // update exist rows equal or less than new 
                    $update_count = ($old_count < count($name_list))? $old_count : count($name_list);
                    for ($i=0; $i<$update_count; $i++) {
                        try{
                            $query = "UPDATE label SET label_name = :name, label_order = :order, label_last_modified = :time WHERE label_id = :id";
                            $stmt = $con->prepare($query);
                            $name = htmlspecialchars(strip_tags($name_list[$i]));
                            $order = $i + 1;
                            $time = date("Y-m-d H:i:s");
                            $label_id = $id_list[$i];

                            $stmt->bindParam(':name', $name);
                            $stmt->bindParam(':order', $order);
                            $stmt->bindParam(':time', $time);
                            $stmt->bindParam(':id', $id_list[$i]);

                            $stmt->execute();
                        } catch (PDOException $exception) {
                            die('ERROR: ' . $exception->getMessage());
                        }
                    }

                    // if new data rows less than existing, delete excess
                    if ($old_count > count($name_list)) {
                        try {
                            $in  = str_repeat('?, ', count($id_list) - 1) . '?';
                            $query = "SELECT label_id FROM label WHERE form_id = ? AND label_id NOT IN ($in)";
                            $stmt = $con->prepare( $query );
                            $stmt->bindParam(1, $id);
                            for ($i=2; $i<count($id_list)+2 ;$i++) {
                                $stmt->bindParam($i, $id_list[$i-2]);
                            }
                            $stmt->execute();

                        } catch (PDOException $exception) {
                            die('ERROR: ' . $exception->getMessage());
                        }
                        $delete_count = $stmt->rowCount();
                        $in  = str_repeat('?, ', $delete_count - 1) . '?';
                        $query = "DELETE FROM label WHERE label_id IN ($in)";
                        $delete_stmt = $con->prepare( $query );
                        for ($i=1; $row = $stmt->fetch(PDO::FETCH_ASSOC); $i++) {
                            $delete_stmt->bindParam($i, $row['label_id'], PDO::PARAM_INT);
                        }
                        try {
                            $delete_stmt->execute();
                        } catch (PDOException $exception) {
                            die('ERROR: ' . $exception->getMessage());
                        }
                    }

                    // if new data rows more than existing, insert new record(s)
                    if ($old_count < count($name_list)) {
                        try {
                            $order_arr = [];
                            $question_marks = str_repeat('( ?, ?, ? ), ', count($name_list)-$old_count-1) . '( ?, ?, ? )';
                            $query = "INSERT INTO label (form_id, label_name, label_order) VALUE $question_marks";
                            $stmt = $con->prepare( $query );
                            for ($i=$update_count; $i<count($name_list); $i++) {
                                array_push($order_arr, $i+1);
                                // $query = "INSERT INTO label SET form_id=:form_id, label_name=:label_name, label_order=:label_order";
                                $form_id_param_index = ( ( $i - $update_count ) * 3 ) + 1;
                                $label_name_param_index = ( ( $i - $update_count ) * 3 ) + 2;
                                $label_order_param_index = ( ( $i - $update_count ) * 3 ) + 3;

                                $stmt->bindParam($form_id_param_index, $id);
                                $stmt->bindParam($label_name_param_index, $name_list[$i]);
                                $stmt->bindParam($label_order_param_index, $order_arr[$i-$update_count]);
                            }
                            $stmt->execute();
                        } catch (PDOException $exception) {
                            die('ERROR: ' . $exception->getMessage());
                        }
                    }
                    echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=index.php?action=update">';
                } else {
                    echo "<div class='alert alert-danger'>Form name can't be blank and should have at least 1 column with data filled.</div>";
                }

            }
            // end if ($_POST) 
            
            try {
                $query = "SELECT * FROM form WHERE form_id = ? LIMIT 0,1";
                $stmt = $con->prepare( $query );
                $stmt->bindParam(1, $id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $name = $row['form_name'];

                $query = "SELECT * FROM label WHERE form_id = ? ORDER BY label_order";
                $stmt = $con->prepare( $query );
                $stmt->bindParam(1, $id);
                $stmt->execute();

                // $row_count = isset($_GET['r']) ? $_GET['r'] : $stmt->rowCount();
                
                $v_arr = [];
                $v_count = 0;
                for ($i=1; $row = $stmt->fetch(PDO::FETCH_ASSOC); $i++) {
                    $v_obj = new stdClass();
                    $v_obj->id = $row['label_id'];
                    $v_obj->text = $row['label_name'];
                    array_push($v_arr, $v_obj);
                    $v_count = $i;
                }

                // usign in vue
                $v_json = json_encode($v_arr);
            } catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}");?>" method="post">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td>Name</td>
                    <td colspan='2'><input type='text' name='name' value="<?php echo htmlspecialchars($name, ENT_QUOTES);  ?>" class='form-control' /></td>
                </tr>
                <tr v-for="(row, index) in rows">
                    <td class='text-right'>
                        # {{ index + 1  }}
                    </td>
                    <td>
                        <input type='hidden' name='label_id[]' v-model="row.id" class='form-control' />
                        <input type='text' name='label_name[]' v-model="row.text" class='form-control' />
                    </td>
                    <td>
                        <input type="button" class="btn btn-danger" @click="deleteRow(index)" v-bind:disabled="disable_delete" value="Delete"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan='2'><input type='button' v-bind:value='addRowBtnText' class='btn btn-info' @click="addRow" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan='2'>
                        <input type='submit' value='Update' class='btn btn-primary' />
                        <a href='index.php' class='btn btn-danger'>Back to Home</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <script type="text/javascript" src="lib/js/jquery.js"></script>
    <script type="text/javascript" src="lib/js/bootstrap.js"></script>
    <script type="text/javascript" src="lib/js/vue.js"></script>
    <script>
        var vm = new Vue({
            el: '.container',
            data: {
                addRowBtnText: 'Add Row',
                rows: <?php echo $v_json; ?>
            },
            computed: {
                disable_delete: function(){
                    if (this.rows.length > 1) {
                        return false
                    } else {
                        return true
                    }
                }
            },
            methods: {
                addRow () {
                    this.rows.push({
                        id: '',
                        text: ''
                    })
                    if (this.rows.length > 1) {
                        this.disable_delete = false
                    }
                },
                deleteRow(index) {
                    if (this.rows.length > 1) {
                        this.rows.splice(index,1)
                    }
                    if (this.rows.length <= 1) {
                        this.disable_delete = true
                    }
                }
            }
        })
    </script>
</body>
</html>