<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Form - Final Form</title>
     
    <link rel="stylesheet" href="lib/css/bootstrap.css"/>
    <link rel="stylesheet" href="lib/css/style.css"/>
         
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Create Form</h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Form</li>
            </ol>
        </nav>
        <?php 
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
                    $query = "INSERT INTO form SET form_name = :form_name";
                    $stmt = $con->prepare( $query );
                    $stmt->bindParam(':form_name', $form_name);
                    try {
                        $stmt->execute();
                    } catch (PDOException $exception) {
                        die('ERROR: ' . $exception->getMessage());
                    }

                    $query = "SELECT form_id FROM form ORDER BY form_id DESC LIMIT 0,1";
                    $stmt = $con->prepare( $query );
                    try {
                        $stmt->execute();
                    } catch (PDOException $exception) {
                        die('ERROR: ' . $exception->getMessage());
                    }
                    $form_fetch = $stmt->fetch(PDO::FETCH_ASSOC);

                    $last_form_id = $form_fetch['form_id'];

                    $order_arr = [];
                    $label_key_list = [];
                    $question_marks = str_repeat('( ?, ?, ?, ? ), ', count($name_list)-1) . '( ?, ?, ?, ? )';
                    $query = "INSERT INTO label (form_id, label_key, label_name, label_order) VALUE $question_marks";
                    $stmt = $con->prepare( $query );
                    for ($i=0; $i<count($name_list); $i++) {
                        array_push($label_key_list, str_replace(" ", "_", strtoupper($name_list[$i])));
                        array_push($order_arr, $i+1);

                        $index_count = $i * 4;
                        $index_first = $index_count + 1;
                        $index_second = $index_count + 2;
                        $index_third = $index_count + 3;
                        $index_fourth = $index_count + 4;

                        $stmt->bindParam($index_first, $last_form_id);
                        $stmt->bindParam($index_second, $label_key_list[$i]);
                        $stmt->bindParam($index_third, $name_list[$i]);
                        $stmt->bindParam($index_fourth, $order_arr[$i]);
                    }
                    try {
                        if ($stmt->execute()) {
                            echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=index.php?action=create">';
                        }
                    } catch (PDOException $exception) {
                        die('ERROR: ' . $exception->getMessage());
                    }
                } else {
                    echo "<div class='alert alert-danger'>Form name can't be blank and should have at least 1 column with data filled.</div>";
                }
            }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <table class='table table-hover table-responsive table-bordered content-td-center'>
                <tr>
                    <td>Name</td>
                    <td colspan='2'><input type='text' name='name' class='form-control' /></td>
                </tr>
                <tr v-for="(row, index) in rows">
                    <td class='text-right'>
                        # {{ index + 1  }}
                    </td>
                    <td>
                        <input type='text' name='label_name[]' v-model="row.text" class='form-control' />
                    </td>
                    <td>
                        <input type="button" class="btn btn-danger" @click="deleteRow(index)" v-bind:disabled="disable_delete" value="Delete"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan='2'><input type='button' v-bind:value='addRowBtnText' class='btn btn-info'  @click="addRow" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan='2'>
                        <input type='submit' value='Create' class='btn btn-primary' />
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
                disable_delete: true,
                rows: [{text: ''}]
            },
            methods: {
                addRow () {
                    this.rows.push({
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
                },
                checkForm: function (e) {
                    this.rows.forEach(function(item, index) {
                        console.log(index)
                        console.log(item.text)
                    })
                }
            }
        })
    </script>
</body>
</html>