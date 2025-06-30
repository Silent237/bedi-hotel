<?php 
session_cache_limiter('nocache');
session_start();
//error_reporting(0);

include ("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');
$response=1;
$msg='';
page_header();
navigation('');
page_footer();
if(isset($_GET['del'])){
	$sql="DELETE from customer where sno ='".$_GET['del']."'";
	$run=execute_query($sql);
}


?>
 <div id="container">
	
<h2>Search About Customer</h2>
	<form action="" method="post">
	<table><tr style="background:#ccc;"><td>Guest Name</td><td><input type="text" name="customer" class="field text medium"></td><td>Company Name</td><td><input type="text" name="bill_to"></td></tr>
		<tr><td>ID Number</td><td><input type="text" name="id_no"></td>
		
			<td>Mob Number</td><td><input type="text" name="mob_no"></td></tr>
		<tr style="background:#66AAAA"><td colspan="2"><input type="submit" name="submit"></td>
			<td colspan="2"><input type="submit" name="reset_form" value="Reset"></td></tr></table>
		</form>
		
<table width="100%">
    <tr style="background:#000; color:#FFF;">
        <th>S.No.</th>
        <!--<th>Photo</th>-->
        <th>Guest Name</th>
        <th>Company Name</th>
        <th>Address</th>
        <th>SAC/HSN</th>
        <th>GSTIN</th>
        <th>ID Type</th>
        <th>ID Number</th>
        <th>Mobile</th>
        <th>Occupation</th>
        <th>City</th>
        <th>Edit</th>
        <th>Delete</th>
        <th>View ID</th>
        <th>Allot Room</th>
    </tr>
<?php
    // Pagination setup
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    // Base SQL
   $sql = 'SELECT * FROM customer WHERE 1=1';
    $condition = array();

    if (isset($_POST['submit'])) {
        if (!empty($_POST['customer'])) {
            $condition[] = " AND cust_name LIKE '%" . $_POST['customer'] . "%'";
        }
        if (!empty($_POST['bill_to'])) {
            $condition[] = " AND company_name LIKE '%" . $_POST['bill_to'] . "%'";
        }
      if (!empty($_POST['id_no'])) {
    $condition[] = " AND id_3 = " . $_POST['id_no'] . "";
}
if (!empty($_POST['mob_no'])) {
    $condition[] = " AND mobile = " . $_POST['mob_no'] . "";
}

    }
 
    $sqql = $sql;
    if (count($condition) > 0) {
        $sqql .= implode($condition);
    }
// echo $sqql;
    // Count total records for pagination
    $count_sql = $sqql;
    $count_result = execute_query($count_sql);
    $total_records = mysqli_num_rows($count_result);
    $total_pages = ceil($total_records / $limit);

    // Add ordering and limit for pagination
    $sqql .= ' ORDER BY sno DESC LIMIT ' . $limit . ' OFFSET ' . $offset;

    // Fetch paginated data
    $result = execute_query($sqql);
    $i = $offset + 1;

    foreach ($result as $row) {
        $col = ($i % 2 == 0) ? '#CCC' : '#EEE';
        echo '<tr style="background:' . $col . '">';
        echo '<th>' . $i++ . '</th>';
        // echo '<td><a href="' . $row['photo'] . '" target="_blank"><img src="' . $row['photo'] . '" style="height:50px;"></a></td>';
        echo '<td>' . $row['cust_name'] . '</td>
        <td>' . $row['company_name'] . '</td>
        <td>' . $row['address'] . '</td>
        <td>' . $row['id_1'] . '</td>
        <td>' . $row['id_2'] . '</td>
        <td>' . $row['id_type'] . '</td>
        <td>' . $row['id_3'] . '</td>
        <td>' . $row['mobile'] . '</td>
        <td>' . $row['occupation'] . '</td>
        <td>' . $row['city'] . '</td>
        <td><a href="admin_customers.php?id=' . $row['sno'] . '">Edit</a></td>
        <td><a href="customer_report.php?del=' . $row['sno'] . '" onclick="return confirm(\'Are you sure?\');">Delete</a></td>
        <td><a href="id_viewer.php?id=' . $row['sno'] . '">View ID</a></td>
        <td><a href="allotment.php?alt=' . $row['sno'] . '">Allot Room</a></td>';
        echo '</tr>';
    }
?>
</table>

<!-- Pagination Links -->
<div style="margin-top:20px;">
    Pages:
    <?php
    for ($p = 1; $p <= $total_pages; $p++) {
        if ($p == $page) {
            echo '<strong>' . $p . '</strong> ';
        } else {
            echo '<a href="?page=' . $p . '">' . $p . '</a> ';
        }
    }
    ?>
</div>

</div>

