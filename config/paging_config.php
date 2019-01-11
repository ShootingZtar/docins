<?php 
// page is the current page, if there's nothing set, default is page 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;
 
// set records or rows of data per page
$records_per_page = isset($_GET['perpage']) ? $_GET['perpage'] : 20;
 
// calculate for the query LIMIT clause
$from_record_num = ($records_per_page * ( $page-1 ) );
?>