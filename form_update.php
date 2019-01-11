<!DOCTYPE HTML>
<html>
<head>
    <title>Update Form - Final Form</title>
     
    <link rel="stylesheet" href="lib/css/bootstrap.css"/>
         
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Update Form</h1>
        </div>
     
        <?php
        $id=isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');
 
        include 'config/database.php';
        
        try {
            $query = "SELECT * FROM form WHERE form_id = ? LIMIT 0,1";
            $stmt = $con->prepare( $query );
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $name = $row['form_name'];
        } catch (PDOException $exception) {
            die('ERROR: ' . $exception->getMessage());
        }

        if ($_POST) {
            try {
                $query = "UPDATE form SET form_name=:name, form_last_modified=:time WHERE form_id = :id";
                $stmt = $con->prepare($query);
                $name=htmlspecialchars(strip_tags($_POST['name']));
                $time=date("Y-m-d H:i:s");

                // bind the parameters
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':time', $time);
                $stmt->bindParam(':id', $id);
                
                // Execute the query
                if($stmt->execute()){
                    echo "<div class='alert alert-success'>Record was updated.</div>";
                }else{
                    echo "<div class='alert alert-danger'>Unable to update record. Please try again.</div>";
                }
                
            } catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$id}");?>" method="post">
            <table class='table table-hover table-responsive table-bordered'>
                <tr>
                    <td>Name</td>
                    <td><input type='text' name='name' value="<?php echo htmlspecialchars($name, ENT_QUOTES);  ?>" class='form-control' /></td>
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