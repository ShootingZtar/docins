<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'config/database.php';

$id = isset($_GET['id']) ? $_GET['id'] : "";
$group = isset($_GET['group']) ? $_GET['group'] : "";

$label_query = "SELECT l.label_name, d.data_value FROM label as l LEFT JOIN data as d ON l.label_key = d.label_key WHERE l.form_id = ? AND d.data_group_id = ? ORDER BY l.label_order";
$label_stmt = $con->prepare($label_query);
$label_stmt->bindParam(1, $id);
$label_stmt->bindParam(2, $group);
$label_stmt->execute();

$data_arr = [];
for ($i=0; $row = $label_stmt->fetch(PDO::FETCH_ASSOC); $i++){
    $data_arr[] = $row;
}

// echo '<pre>';
// print_r($data_arr);
// echo '</pre>';
$write_html = '
<style>
.container{
    font-family: "Garuda";
    font-size: 10pt;
}
p{
    text-align: justify;
}
h1{
    text-align: center;
}
</style>
<div class="container">
    <h1>ตัวอย่างการดึงข้อมูล</h1>';
foreach ($data_arr as $d) {
    $write_html .= $d['label_name'] . ' => ' . $d['data_value'] . '<br>';
}
$write_html .= '</div>';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($write_html);
$mpdf->Output();

?>