<!DOCTYPE HTML>
<html>
<head>
    <title>Create Form - Final Form</title>
     
    <link rel="stylesheet" href="lib/css/bootstrap.css"/>
         
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Update Form</h1>
        </div>
     
        <?php
        include 'config/database.php';

        if ($_POST) {
            $is_dup = false;
            try {
                $query = "SELECT * FROM form";
                $stmt = $con->prepare( $query );
                $stmt->execute();
                for ($i=1; $row = $stmt->fetch(PDO::FETCH_ASSOC); $i++) {
                    if ($_POST['name'] === $row['form_name']) {
                        $is_dup = true;
                    }
                }
                
                $name = $row['form_name'];
            } catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }

            if (!$is_dup) {
                try {
                    $query = "INSERT INTO form SET form_name=:name, form_status=:status";
                    $stmt = $con->prepare($query);
                    $name=htmlspecialchars(strip_tags($_POST['name']));
                    $status=1;

                    // bind the parameters
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':status', $status);
                    
                    // Execute the query
                    if($stmt->execute()){
                        echo "<div class='alert alert-success'>Record was updated.</div>";
                        header( "location: index.php" );
                    }else{
                        echo "<div class='alert alert-danger'>Error. Please contact administrator.</div>";
                    }
                    
                } catch (PDOException $exception) {
                    die('ERROR: ' . $exception->getMessage());
                }
            } else {
                echo "<div class='alert alert-warning'>The name is dupplicate. Please try another.</div>";
            }
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td>Name</td>
                    <td><input type='text' name='name' class='form-control' /></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='submit' value='Save Changes' class='btn btn-primary' />
                        <a href='index.php' class='btn btn-danger'>Back to Home</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <script type="text/javascript" src="lib/js/jquery.js"></script>
    <script type="text/javascript" src="lib/js/bootstrap.js"></script>
</body>
</html>