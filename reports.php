<?php
session_cache_limiter('nocache');
session_start();
include("scripts/settings.php");
page_header();
?>
<?php if($_SESSION['username']=='sadmin'){ ?>
<div id="container" class="ltr">
       	<div id="icon" onclick="load_wind('index.php')" >
         <a href="index.php" style="padding:0px; margin:0px; width:80px;">HOME</a>
        </div>
       <div id="icon" onclick="load_wind('report_sale.php')" >
         <a href="report_monthly.php" style="padding:0px; margin:0px; width:80px;">Monthly Bill Report</a>
        </div>
       	<div id="icon" onclick="load_wind('report_purchase.php')" >
         <a href="report_allotment.php" style="padding:0px; margin:0px; width:80px;">Allotment Report</a>
        </div>
        <div id="icon" onclick="load_wind('customer_ledger.php')" >
         <a href="customer_ledger.php" style="padding:0px; margin:0px; width:80px;">Customer Ledger</a>
        </div>
         <?php
        }
else{
	page_logvalidate($_SESSION['username']);
	}
		?>
       	<div id="icon" onClick="load_wind('signout.php')">
            <a href="signout.php" style="text-decoration:none;"><img src="images/signout.png" /></a>
        </div>
        
        	
</div>
<?php
page_footer();
?>
