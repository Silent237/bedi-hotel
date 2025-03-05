<?php
session_cache_limiter('nocache');
session_start();

include ("scripts/settings.php");
	logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
	logvalidate('admin');
$response=1;
$msg='';
date_default_timezone_set('Asia/Calcutta');
page_header();

?>
<style type="text/css">
	#table_box{
    display: inline-block;
    margin: 5px;
    font-size: 15px;
    background: url(images/box.gif) no-repeat;
    width: 100px;
    height: 90px;
    text-align: center;
    vertical-align: middle;
    border:none;

	}
	#subdiv {
    display: table-cell;
    height: 60px;
    width: 105px;
    border: 0px solid;
    vertical-align: middle;
    border:none;
    font-size: 40px;
}
button{
	
  border: none;
  outline: none;
  padding: 12px 16px;
  background-color:#66AAAA;
  color: white;
  height:70px;
  width:110px;
  cursor: pointer;
  margin: 5px;
  padding:20px;
  font-size: 30px;

}
</style>
<div id="container">
        <h2>Table</h2>	
		<?php echo '<ul><h4>'.$msg.'</h4></ul>'; ?>
		<form action="" class="wufoo leftLabel page1"  name="addnewdesignation" enctype="multipart/form-data" method="post" onSubmit="" >
			<?php
				$sql="SELECT * FROM `res_table`";
				$res=execute_query($sql);
				while($row=mysqli_fetch_array($res)){
					$id=$row['sno'];
                    if($row['booked_status'] ==1){
				        echo '<a href="dine_in_order.php?table_id='.$id.'"><button type="button" id="table_'.$id.'" style="background-color:red;">'.$row['table_number'].'</button></a>';
				    }
                    else{
                         echo '<a href="dine_in_order.php?table_id='.$id.'"><button type="button" id="table_'.$id.'">'.$row['table_number'].'</button></a>';
                    }
                }

			?>
		</form>
</div>

<?php
page_footer();
?>

