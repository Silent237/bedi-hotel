<?php
include("settings_dbase.php");
set_time_limit(0);
error_reporting(E_ALL);
sethistory();

error_reporting(1);
session_cache_limiter('nocache');
session_start();
function page_header($title='WeKnow ERP') {

	if(!isset($_SESSION['username'])){
		header('location: index.php');
	}
	echo "<script>
function searchPage() {
    var input = document.getElementById('shortcut_command');
    var filter = input.value.toLowerCase();
    var nodes = document.querySelectorAll('#container');

    nodes.forEach(function(node) {
        if (node.textContent.toLowerCase().includes(filter)) {
            node.style.display = 'block';
        } else {
            node.style.display = 'none';
        }
    });
}
</script>";

	echo '<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <a class="navbar-brand page-title" href="#" style="font-size:24px; color:#F83A3D; margin-left:150px;"></a>
        </div>
        <button class="navbar-toggler navbar-toggler-right" type="button" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
            <span class="navbar-toggler-bar burger-lines"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="nav navbar-nav mr-auto" style="margin-right:600px;">
                <li>
                    <form class="navbar-form navbar-left navbar-search-form" role="search">
                        <div class="input-group">
                            <i class="fab fa-sistrix"></i>&nbsp;&nbsp;
                            <input style="width:200px;" type="text" value="" class="form-control mr-5" placeholder="Search... (Shortcut : Ctrl+/)" id="shortcut_command">
                        </div>
                    </form>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown"> 
                    <a class="" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                        <button class="btn btn-info"><i class="fa fa-user-lock"></i> ADMIN</button>
                    </a>&nbsp;|&nbsp; 
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="#">Profile</a>
                        <a class="dropdown-item" href="#">Activity Log</a>
                        <div class="divider"></div>
                        <a class="dropdown-item" href="signout.php"><i class="fas fa-sign-out-alt"></i>Signout</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="index.php">
                        <button class="btn btn-danger">
                            <i class="fa fa-backward"></i> Back
                        </button>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
';

	$sql = 'select * from general_settings where `desc`="company"';
	$company = mysqli_fetch_array(execute_query($sql));
	$company = explode(" ", $company['rate']);
	$company_name = '';
	foreach($company as $k=>$v){
		$company_name .= '<span>'.substr($v, 0, 1).'</span>'.substr($v, 1).' ';
		//echo $v.'<br>';
	}
	$current_file_name = basename($_SERVER['PHP_SELF']);
	
	$sql = 'select * from general_settings where `desc`="company"';
	$company_name = mysqli_fetch_assoc(execute_query($sql));
	$company_name = $company_name['rate'];

	$sql = 'select * from general_settings where `desc`="gstin"';
	$company_gstin = mysqli_fetch_assoc(execute_query($sql));
	$company_gstin = $company_gstin['rate'];

	$sql = 'select * from general_settings where `desc`="pan"';
	$company_pan = mysqli_fetch_assoc(execute_query($sql));
	$company_pan = $company_pan['rate'];
	
	$sql = 'select * from general_settings where `desc`="address"';
	$company_address = mysqli_fetch_assoc(execute_query($sql));
	$company_address = $company_address['rate'];
	
	if(isset($_SESSION['session_id'])) {
		$sql = 'select * from navigation where hyper_link="'.$current_file_name.'"';
		//echo $sql;
		$result = execute_query($sql);
		$file = mysqli_fetch_array($result);
		logvalidate($file['sno']);
		$title = $file['link_description'];
	}
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>'.$title.'</title>
<link href="css/light-bootstrap-dashboard.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/component.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/jcarousel.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/pagination.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/jquery.multiselect.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/jquery.datetimepicker.css" rel="stylesheet" type="text/css" media="all" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="css/fonts.css" rel="stylesheet">
<link href="css/fonts1.css" rel="stylesheet">
 <link href="css/bootstrap5.css" rel="stylesheet">
<link rel="stylesheet" href="css/fontAwesome.css">
<script src="js/jquery-1.11.2.min.js" language="javascript" type="text/javascript"></script>
<script src="js/calendar.js" language="javascript" type="text/javascript"></script>
<script src="js/jcarousel.js" language="javascript" type="text/javascript"></script>
<script src="js/bpopup.js" language="javascript" type="text/javascript"></script>
<script src="js/canvasjs.min.js"></script>
<script src="js/chart.js"></script>
<script src="jquery/jquerynew.js"></script>
<script src="js/popper.js"></script>
<script src="js/bootstrap5.js"></script>
<script src="js/fontawesome.js" type="text/javascript"></script>
<script src="js/light-bootstrap-dashboard.js"></script>
 <script src="js/bootstrap51.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="jquery/jquery-ui.min.js" type="text/javascript"></script>
<script src="jquery/jquery.stickyheader.js" type="text/javascript"></script>
<script src="jquery/jquery.ba-throttle-debouce.min.js" type="text/javascript"></script>
<script src="jquery/jquery.multiselect.js" language="javascript"></script>
<script type="text/javascript" language="javascript">

$(document).ready( 
	function() {
		// Add the "focus" value to class attribute
		$("input").focusin( 
			function() {
				$(this).addClass("focus");
			}
		);
		$("select").focusin( 
			function() {
				$(this).addClass("focus");
			}
		);
		$(":checkbox").focusin( 
			function() {
				$(this).addClass("focus");
			}
		);
		// Remove the "focus" value to class attribute
		$("input").focusout( 
			function() {
				$(this).removeClass("focus");
			}
		);
		$("select").focusout( 
			function() {
				$(this).removeClass("focus");
			}
		);
		$(":checkbox").focusout( 
			function() {
				$(this).removeClass("focus");
			}
		);
	}
);

</script>
<script language="javascript" type="text/javascript">
function check_prev_date(form_date){
	var cur_date = "'.date("Y-m-d").'";
	var warn = 0;
	$(".noblank").each(function(index, element){
		if($(element).val()==""){
			$( element ).css( "backgroundColor", "yellow" );
			warn = 1;
		}
		else{
			$( element ).css( "backgroundColor", "white" );
		}
	});
	if(warn!=0){
		alert("Please enter all complusory blocks");
		return false;
	}

	if(cur_date>form_date){
		var response = confirm("Entry date is old than today. Do you want to proceed. ?");
	}
	else{
		var response = confirm("Are you sure?");
	}
	return response;
}
</script>
<link href="jquery/jquery-ui.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body  style="background-color:#ccc"> 
		';
}
function title_bar(){
	echo'
	<div id="content" class="print-only">
            <div id="header">
			<img src="images/bedi.png" width="150px" height="120px" style="margin-right:50px;">
			';
	
	
		$sql = 'select * from general_settings where `desc`="restaurant_name"';
		$restaurant_name = mysqli_fetch_assoc(execute_query($sql));
		$restaurant_name = $restaurant_name['rate'];

		$sql = 'select * from general_settings where `desc`="restaurant_gstin"';
		$restaurant_gstin = mysqli_fetch_assoc(execute_query($sql));
		$restaurant_gstin = $restaurant_gstin['rate'];

		$sql = 'select * from general_settings where `desc`="restaurant_pan"';
		$restaurant_pan = mysqli_fetch_assoc(execute_query($sql));
		$restaurant_pan = $restaurant_pan['rate'];

		$sql = 'select * from general_settings where `desc`="company"';
		$hotel_name = mysqli_fetch_assoc(execute_query($sql));
		$hotel_name = $hotel_name['rate'];

		$sql = 'select * from general_settings where `desc`="gstin"';
		$hotel_gstin = mysqli_fetch_assoc(execute_query($sql));
		$hotel_gstin = $hotel_gstin['rate'];

		$sql = 'select * from general_settings where `desc`="pan"';
		$hotel_pan = mysqli_fetch_assoc(execute_query($sql));
		$hotel_pan = $hotel_pan['rate'];
	
		$file = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
		if(strrpos($file, "_restaurant")){
			echo '<h3 style="color:white;">'.$restaurant_name.'</h3>
				<h3 style="color:white;">Ayodhya</h3><br>'
				;
		}
		else{
			echo '<div><h3>'.$hotel_name.'</h3>
				<h3>(AYODHYA)</h3></div>'
				;
		}
	echo '
            </div>
        </div>

        <div class="clear"></div>

';
	
}
function page_footer($id = 'home') {
	if(isset($_SESSION['username'])){
		$user = $_SESSION['username'];
		$sql = 'select * from session where user="'.$user.'" order by s_start_date desc, s_start_time desc';
		$last = execute_query($sql);
		if(mysqli_num_rows($last)!=0){
			$last = mysqli_fetch_array($last);
			$last = $last['s_start_date'].' '.$last['s_start_time'];
		}
	
		else{
			$last = '';
		}
		$sql = 'select * from general_settings where `desc`="session_timeout"';
		$timeout = mysqli_fetch_array(execute_query($sql));
		if($timeout['rate']>0){
			$timeout = $timeout['rate']*60;
			$difference = time()-$timeout;
			$sql = 'select * from session where user!="'.$_SESSION['username'].'" and last_active>'.$difference;
			$session = execute_query($sql);
			if(mysqli_num_rows($session)!=0){
				$other = mysqli_num_rows($session);
			}
			else{
				$other = 0;
			}
		}
		
	}
	else{
		$user='Guest';
		$last='';
		$other='';
	}
	// echo '
	// <div class="clear" class="no-print"></div>
    //     <div id="footerstick" class="no-print">
    //         <div id="footercontent">
				
               
	// 			<img src="images/logo.gif" style="width:50px; margin-left:40px;">
    //         	<div id="support">
    //                 <strong>Helpdesk : <a href="http://www.weknowtech.in">WeKnow Technologies</a></strong><br/>
    //                 M : +91-9554969771 to 779<br />
    //                 E : info@weknowtech.in
    //             </div>
    //         </div>
    //     </div>
    // </div>';
?>	
	<script>
	$(".dropdown dt a").on('click', function() {
		$(".dropdown dd ul").slideToggle('fast');
	});

	$(".dropdown dd ul li a").on('click', function() {
		$(".dropdown dd ul").hide();
	});

	function getSelectedValue(id) {
	  return $("#" + id).find("dt a span.value").html();
	}

	$(document).bind('click', function(e) {
	  var $clicked = $(e.target);
	  if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
	});

	$('.mutliSelect input[type="checkbox"]').on('click', function() {

	  var title = $(this).closest('.mutliSelect').find('input[type="checkbox"]').val(),
		title = $(this).val() + ",";

	  if ($(this).is(':checked')) {
		var html = '<span title="' + title + '">' + title + '</span>';
		$('.multiSel').append(html);
		$(".hida").hide();
	  } else {
		$('span[title="' + title + '"]').remove();
		var ret = $(".hida");
		$('.dropdown dt a').append(ret);

	  }
	});		
		

// defining flags
var isCtrl = false;
// helpful function that outputs to the container
// the magic :)
$(document).ready(function() {
	// action on key up
	$(document).keyup(function(e) {
		if(e.which == 17) {
			isCtrl = false;
		}
	});
	// action on key down
	$(document).keydown(function(e) {
		if(e.which == 17) {
			isCtrl = true; 
		}
		if(e.which == 191 && isCtrl) { 
			$("#shortcut_command").focus();
		} 
	});
	
});
</script>

<style>
	/* Hide the sidebar by default on small screens */
@media (max-width: 480px) {
    #vertical-navbar {
        display: none;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background-color: #800000;
    }
	#header{
		margin-left: 0px;
		left: 0;
		width: 100%;
	}
    .dashboard{
		margin-left: 70px;
	}
	#container{
		width: 100%;
		margin-left: 0px;
	}
	#footerstick{
		margin-left: 100px;
	}
	.dashboard{
		margin-left: 250px;
		grid-template-columns: auto;
	}
    #vertical-navbar.expanded {
        display: block;
    }
}
@media (max-width: 980px) {
	#header{
		margin-left: 30px;
		width: 100%;
	}
	#footerstick{
		margin-left: 650px;
	}
	.dashboard{
		grid-template-columns: auto;
	}
	.box{
		width: 500px;
		height: 220px;
	}
	.box p{
		margin-top: 70px;
		height: 70px;
		font-size: 40px;
	}
}

</style>
<script>
	document.addEventListener("DOMContentLoaded", function() {
    var navbar = document.getElementById('vertical-navbar');
    var navbarToggler = document.querySelector('.navbar-toggler');

    // Handle small screen navbar toggle
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            if (window.innerWidth <= 980) {
                navbar.classList.toggle('expanded');
            }
        });
    }
});


</script>
</body>
</html>
<?php
}

function nav($code){
	$sql = 'select * from nav where code="'.$code.'"';
	$result = execute_query($sql);
	while($row = mysqli_fetch_array($result)){
		$link=$row['link'];
		echo '<div id="icon" onClick="location.href=\''.$link.'\'">
				<div id="subicon">
                    <a href="'.$row['link'].'" >'.$row['display'].'</a>
				</div>
               </div>';
	}
	echo '<div id="icon" onClick="load_wind(\'signout.php\')">
			<a href="signout.php" ><img src="images/signout.png" /></a>
		</div>';
}
?>
<?php

function navigation($parent='') {
    $isDashboard = basename($_SERVER['PHP_SELF']) == 'index.php';
    ?>
   
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var collapseToggles = document.querySelectorAll('[data-toggle="collapse"]');
            collapseToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    var targetId = this.getAttribute('href');
                    var targetCollapse = document.querySelector(targetId);
                    if (targetCollapse) {
                        targetCollapse.classList.toggle('hide');
                    }
                });
            });

            var navbar = document.getElementById('vertical-navbar');
            if (navbar && window.location.pathname.includes('index.php')) {
                navbar.classList.add('expanded');
            }
        });
    </script>
    
    <div class="sidebar no-print" id="vertical-navbar" style="background-color: #3a3f51;">
        <div class="sidebar-wrapper no-print">
            <div class="logo" style="margin-left:20px;display:flex;">
                <span><i class="fa fa-2x fa-atom"></i></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="#" class="simple-text  logo-normal">Hotel Management</a>
            </div>

            <ul class="nav no-print">
                <li routerlinkactive="active" class="nav-item<?php echo $isDashboard ? ' expanded' : ''; ?>">
                    <a class="nav-link" href="index.php">
                        <i class="fa fa-chart-pie"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <?php
				
                $sql = 'select * from navigation where (parent is null or parent="" or parent="P") and hyper_link!="index.php" order by abs(sort_no), sub_parent, link_description';
                $result = execute_query($sql);				
                $sub_parent = '';
                while($row = mysqli_fetch_array($result)){
                    $active = ($row['hyper_link'] == basename($_SERVER['PHP_SELF'])) ? ' active' : '';

                    if($_SESSION['usertype'] != 'sadmin'){
                        if($row['parent'] == 'P'){
                            $sql = 'select group_concat(sno) as sno from navigation where parent="'.$row['sno'].'" order by abs(sort_no), sub_parent, link_description';
                            $row_sub = mysqli_fetch_assoc(execute_query($sql));

                            $sql = 'select * from user_access where user_id="'.$_SESSION['usertype'].'" and file_name in ('.$row_sub['sno'].')';
                            $result_child_count = execute_query($sql);

                            if(mysqli_num_rows($result_child_count) != 0){
                                echo '
                                <li class="nav-item'.$active.'">
                                    <a data-toggle="collapse" href="#parent'.$row['sno'].'" class="nav-link"><i class="'.$row['icon_image'].'"></i><p>'.$row['link_description'].'<b class="caret"></b></p></a>
                                    <div class="collapse" id="parent'.$row['sno'].'">
                                        <ul class="nav">';

                                $sql = 'select * from navigation where parent="'.$row['sno'].'" order by abs(sort_no), sub_parent, link_description';
                                $result_sub = execute_query($sql);
                                while($row_sub = mysqli_fetch_assoc($result_sub)){
                                    $sql = 'select * from user_access where user_id="'.$_SESSION['usertype'].'" and file_name="'.$row_sub['sno'].'"';
                                    $result_access = execute_query($sql);
                                    if(mysqli_num_rows($result_access) == 1){
                                        echo '<li class="nav-item"><a class="nav-link" href="'.$row_sub['hyper_link'].'"><i class="'.$row_sub['icon_image'].'" style="font-size:20px; margin-left:15px; margin-right:0px;"></i><span class="sidebar-mini"></span><span class="sidebar-normal">'.$row_sub['link_description'].'</span></a></li>';
                                    }
                                }
                                echo '
                                        </ul>
                                    </div>
                                </li>';
                            }

                        } else {
                            $sql = 'select * from user_access where user_id="'.$_SESSION['usertype'].'" and file_name="'.$row['sno'].'"';
                            $result_access = execute_query($sql);
                            if(mysqli_num_rows($result_access) == 1){
                                echo '<li class="nav-item'.$active.'"><a class="nav-link" href="'.$row['hyper_link'].'"><i class="'.$row['icon_image'].'"></i><p>'.$row['link_description'].'</p></a></li>';
                            }
                        }
                    } else {
                        if($row['parent'] != "P"){
                            echo '<li class="nav-item'.$active.'"><a class="nav-link" href="'.$row['hyper_link'].'"><i class="'.$row['icon_image'].'"></i><p>'.$row['link_description'].'</p></a></li>';
                        } else {
                            echo '
                            <li class="nav-item'.$active.' no-print">
                                <a data-toggle="collapse" href="#parent'.$row['sno'].'" class="nav-link"><i class="'.$row['icon_image'].'"></i><p>'.$row['link_description'].'<b class="caret"></b></p></a>
                                <div class="collapse" id="parent'.$row['sno'].'">
                                    <ul class="nav">';

                            $sql = 'select * from navigation where parent="'.$row['sno'].'" order by abs(sort_no), sub_parent, link_description';
                            $result_sub = execute_query($sql);
                            while($row_sub = mysqli_fetch_assoc($result_sub)){
                                echo '<li class="nav-item"><a class="nav-link" href="'.$row_sub['hyper_link'].'"><i class="'.$row_sub['icon_image'].'" style="font-size:20px; margin-left:15px; margin-right:0px;"></i><span class="sidebar-mini"></span><span class="sidebar-normal">'.$row_sub['link_description'].'</span></a></li>';
                            }
                            echo '
                                    </ul>
                                </div>
                            </li>';
                        }    

                    }
                }
                ?>
            </ul>
        </div>
    </div>

    <?php
    $time_track[] = microtime(true);
}

?>


<?php


function subnav($code){
	$sql = 'select * from nav where code="'.$code.'"';
	$result = execute_query($sql);
	echo '<li><a href="index.php"><img src="images/home.png" width="30" /></a></li>';
	while($row = mysqli_fetch_array($result)){
		echo '<li><a href="'.$row['link'].'" >'.$row['display'].'</a></li>';
	}
	echo '<li><a href="signout.php" ><img src="images/signout.png" width="30" /></a></li>';

}


function pagecount($sql, $script, $active){
	$result = execute_query($sql);
	$count = mysqli_num_rows($result);
	$page = ceil($count/50);
	if($active>1 && $active<$page){
		$print = '<a href="'.$script.'">&lt;&lt;</a> | <a href="'.$script.'?pg='.($active-1).'"> &lt;</a> |';
	}
	else{
		$print = '';
	}
	for($i=1;$i<=$page;$i++){
		if($active==$i){
			$print .= $i.' | ';
		}
		else{
			$print .= '<a href="'.$script.'?pg='.$i.'">'.$i.'</a> | ';
		}
	}
	return $print;
}

function get_reference($id){
	$sql = 'select * from admin_reference where sno="'.$id.'"';
	$result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	return $row['name'];
}

// function get_reference($id){
  
//     if (empty($id) || !is_numeric($id)) {
//         throw new Exception("Invalid ID parameter");
//     }

//     // Sanitize the input (optional, but recommended)
//     $id = mysqli_real_escape_string($db, $id);

//     // Construct the SQL query
//     $sql = "SELECT * FROM admin_reference WHERE sno = $id";
//     $result = execute_query($sql);

//     if (mysqli_num_rows($result) > 0) {
//         $row = mysqli_fetch_assoc($result);
//         return $row['name'];
//     } else {
//         throw new Exception("No record found for ID: $id");
//     }
// }

function randomstring(){

	$length=16;

	$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	$char_length=(strlen($chars)-1);

	$string=$chars[rand(0,$char_length)];

	for($i=1;$i<$length;$i=strlen($string)){

		$r=$chars[rand(0,$char_length)];

		if($r!=$string[$i-1]){

			$string .= $r;

		}

	}

	return $string;	

}

function logout(){

	date_default_timezone_set('Asia/Calcutta');

	$_SESSION['enddate']=date('y-m-d');

	$time = localtime();

	$time = $time[2].':'.$time[1].':'.$time[0];

	$_SESSION['endtime']=$time;

	$sql = "update session set s_end_time='".$_SESSION['endtime']."' where s_id='".$_SESSION['id']."' and user='".$_SESSION['uname']."'";

	execute_query($sql);

	session_destroy();

	session_unset();

	session_write_close();
	

	echo '<div id="container" class="ltr">';

	 header("location: index.php");

	echo '</div>';

}

function add_customer($name, $address, $mobile, $tin , $company_name){
	$name = trim($name);
	$address = trim($address);
	$mobile = trim($mobile);
	$tin = trim($tin);
	$company_name = trim($company_name);

	if($mobile!=''){
		$sql = 'select * from customer where mobile="'.$mobile.'" or mob_2="'.$mobile.'" or mob_3="'.$mobile.'"';
		$result = execute_query($sql);
		if(mysqli_num_rows($result)!=0){
			$supplier = mysqli_fetch_array($result);
			$sql_update='update customer set 
			company_name="'.$company_name.'",
			cust_name="'.$name.'", 
			mobile="'.$mobile.'",
			id_2= "'.$tin.'",
			address="'.$address.'",
			edited_by="'.$_SESSION['username'].'", 
			edited_on="'.date('Y-m-d H:i:s').'"  
			where sno='.$supplier['sno'];
			execute_query($sql_update);
			return $supplier['sno'];
		}
	}
	if($tin!=''){
		$sql = 'select * from customer where id_2="'.$tin.'"';
		$result = execute_query($sql);
		if(mysqli_num_rows($result)!=0){
			$supplier = mysqli_fetch_array($result);
			$sql_update='update customer set 
			company_name="'.$company_name.'",
			cust_name="'.$name.'", 
			mobile="'.$mobile.'",
			id_2= "'.$tin.'",
			address="'.$address.'",
			edited_by="'.$_SESSION['username'].'", 
			edited_on="'.date('Y-m-d H:i:s').'"  
			where sno='.$supplier['sno'];
			execute_query($sql_update);
			return $supplier['sno'];
		}
	}
	if($company_name!=''){
		$sql = 'select * from customer where company_name="'.$company_name.'" and address = "'.$address.'"';
		$result = execute_query($sql);
		if(mysqli_num_rows($result)!=0){
			$supplier = mysqli_fetch_array($result);
			$sql_update='update customer set 
			company_name="'.$company_name.'",
			cust_name="'.$name.'", 
			mobile="'.$mobile.'",
			id_2= "'.$tin.'",
			address="'.$address.'",
			edited_by="'.$_SESSION['username'].'", 
			edited_on="'.date('Y-m-d H:i:s').'"  
			where sno='.$supplier['sno'];
			execute_query($sql_update);
			return $supplier['sno'];
		}
	}
	
	if ($company_name == '' AND $name != '') {
		$sql = 'SELECT * FROM customer WHERE cust_name="' . $name . '" AND address = "' . $address . '"';
		$result = execute_query($sql);  // Assuming $db is your database connection
		
		if (mysqli_num_rows($result) != 0) {
			$supplier = mysqli_fetch_array($result);
			
			$sql_update = 'UPDATE customer SET 
				company_name="' . $company_name . '",
				cust_name="' . $name . '", 
				mobile="' . $mobile . '",
				id_2="' . $tin . '",
				address="' . $address . '",
				edited_by="' . $_SESSION['username'] . '", 
				edited_on="' . date('Y-m-d H:i:s') . '"  
				WHERE sno=' . $supplier['sno'];
			
			execute_query($sql_update);
			return $supplier['sno'];
		}
	}
	
	$sql = 'insert into customer (cust_name, address, id_2, mobile , company_name) values ("'.$name.'", "'.$address.'", "'.$tin.'", "'.$mobile.'" , "'.$company_name.'")';
	execute_query($sql);
	global $db;
	if(mysqli_error($db)){
		echo "Error # 142 : ".mysqli_error($db).' >> '.$sql;
	}
	return mysqli_insert_id($db);
}

function sethistory(){

	// make sure the container array exists

	// the paranoid will also check here that sessions are even being used 

	if(!isset($_SESSION['history'])){

	  $_SESSION['history'] = array();

	}

	

	// make an easier to use reference to the container

	$h =& $_SESSION['history'];



	// get the referring page and this page

	// we need to construct matching strings

	// put the referring page straight in the array
	
	if(!isset($_SERVER['HTTP_REFERER'])){
		$_SERVER['HTTP_REFERER']='';
	}

	$h[] = $from = $_SERVER['HTTP_REFERER']; 

	$here = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

	

	// find out how many elements we have

	$count = count($h);

	

	//don't waste memory - trim off old entries

	while($count>20){

		array_shift($h);

		$count--;

	}

	

	// don't want to get stuck in a reference loop

	// this can be falsely triggered by pages that link to each other 

	// but hopefully rarely and the button will still behave rationally

	// also catches use of the browser 'Back' button/key

	// remove last two items to rewind history state

	while($count > 1 && $h[$count-2] == $here){

		array_pop($h);

		array_pop($h);

		$count -= 2; 

	}

	

	// don't want to get stuck on one page either

	// for pages that process themselves or are returned to after process script

	// remove last item to rewind history state

	while($count > 0 && $h[$count-1] == $here){

		array_pop($h);

		$count--;

	}

	// all done

	return;

}



function returnlink($defaulturl='index.php', $override=false){

	// initialise variables

	$c = 0;

	$url = '';

	

	// check that the history container exists

	// if so check it has something in it and set $url

	if(isset($_SESSION['history'])){

		$c = count($_SESSION['history']);

	    $url = ($c > 0) ? $_SESSION['history'][$c-1] : '';

    } 



	// check for use $defaulturl conditions

	// $c may still be > 0 if the page was accessed directly

	// but $url will be blank

	if($override || $c == 0 || $url == ''){

		return $defaulturl;

	}

	else{

		return $url;  

	} 

}
function get_ledger($sno){
	$sql = 'select * from customer where sno='.$sno;
	$row = mysqli_fetch_array(execute_query($sql));
	return $row['cus_name']; 
}

function get_cust_balance($from,$to,$id){
		$to=date("Y-m-d", strtotime($to)+86400);
		$sql_trans = 'select sum(amount) as trans from customer_transactions where timestamp<"'.$from.'" and type in ("payment") and cust_id="'.$id.'"';
	

		$dr_trans = mysqli_fetch_array(execute_query($sql_trans));
		$sql_trans = 'select sum(amount) as trans from customer_transactions where timestamp<"'.$from.'" and type in ("receipt") and  cust_id="'.$id.'"';
		//echo $sql_trans;
		$cr_trans = mysqli_fetch_array(execute_query($sql_trans));

		//$sql_trans = 'select sum(amount) as rentpay from customer_transactions where timestamp<"'.$from.'" and type="RENT" and (mop="cash" or mop="card") and cust_id='.$id;
		//$rent_pay = mysqli_fetch_array(execute_query($sql_trans));

		$sql_trans = 'select sum(amount) as rentcr from customer_transactions where timestamp<"'.$from.'" and type="RENT" and mop="credit" and cust_id="'.$id.'"';
		$rent_cr = mysqli_fetch_array(execute_query($sql_trans));
		//echo $sql_trans.'<br/>';

		$sql_trans = 'select sum(amount) as bancr from customer_transactions where timestamp<"'.$from.'" and type="BAN_AMT" and mop="credit" and cust_id="'.$id.'"';
		$ban_cr = mysqli_fetch_array(execute_query($sql_trans));
		//echo $sql_trans.'<br/>';

		$sql_trans = 'select sum(amount) as rescr from customer_transactions where timestamp<"'.$from.'" and type="sale_restaurant" and mop="credit" and cust_id="'.$id.'"';
		$res_cr1 = mysqli_fetch_array(execute_query($sql_trans));

		$sql_trans = 'select sum(amount) as adcr from customer_transactions where timestamp<"'.$from.'" and type="ADVANCE_AMT" and cust_id="'.$id.'"';
		$ad_cr = mysqli_fetch_array(execute_query($sql_trans));
	
		$sql_trans = 'select sum(amount) as addr from customer_transactions where timestamp<"'.$from.'" and type="ADVANCE_PAID" and cust_id="'.$id.'"';
		$ad_dr = mysqli_fetch_array(execute_query($sql_trans));

		$sql_advance_set = 'select sum(advance_set_amt) as advancedr from customer_transactions where timestamp<"'.$from.'" and (type="RENT" OR type="BAN_AMT") and mop="credit" and cust_id="'.$id.'"';
		$advance_cr = mysqli_fetch_array(execute_query($sql_advance_set));

		$sql = 'select * from customer where sno="'.$id.'"';
		$customer = mysqli_fetch_array(execute_query($sql));

		$cust_opening = $customer['opening_balance'] + $dr_trans['trans'] - $cr_trans['trans'] + $rent_cr['rentcr'] + $ban_cr['bancr'] + $res_cr1['rescr'] + $ad_dr['addr'] - $ad_cr['adcr'];

		$sql_trans = 'select sum(amount) as trans from customer_transactions where timestamp>="'.$from.'" and timestamp<="'.$to.'" and type="payment" and cust_id="'.$id.'"';

		$dr_trans = mysqli_fetch_array(execute_query($sql_trans));

		$sql_trans = 'select sum(amount) as trans from customer_transactions where timestamp>="'.$from.'" and timestamp<="'.$to.'" and type="receipt" and cust_id="'.$id.'"';
		
		$cr_trans = mysqli_fetch_array(execute_query($sql_trans));
		$sql_trans = 'select sum(amount) as rentpay from customer_transactions where timestamp>="'.$from.'" and timestamp<="'.$to.'" and type="RENT" and mop="credit" and cust_id="'.$id.'"';
		//echo $sql_trans;
		$rent_pay = mysqli_fetch_array(execute_query($sql_trans));

		$sql_trans = 'select sum(amount) as banpay from customer_transactions where timestamp>="'.$from.'" and timestamp<="'.$to.'" and type="BAN_AMT" and mop="credit" and cust_id="'.$id.'"';
		//echo $sql_trans;
		$ban_pay = mysqli_fetch_array(execute_query($sql_trans));

		$sql_trans = 'select sum(amount) as respay from customer_transactions where timestamp>="'.$from.'" and timestamp<="'.$to.'" and type="sale_restaurant" and mop="credit" and cust_id="'.$id.'"';
		//echo $sql_trans;
		$res_pay = mysqli_fetch_array(execute_query($sql_trans));

		$sql_trans = 'select sum(amount) as renp from customer_transactions where timestamp>="'.$from.'" and timestamp<="'.$to.'" and type="RENT" and payment_for="ROOM" and cust_id="'.$id.'"';
		//echo $sql_trans;
		$cr_room = mysqli_fetch_array(execute_query($sql_trans));

		$sql_trans = 'select sum(amount) as adcr from customer_transactions where timestamp>="'.$from.'" and timestamp<="'.$to.'" and type="ADVANCE_AMT" and cust_id="'.$id.'"';
		$ad_cr = mysqli_fetch_array(execute_query($sql_trans));

		$sql_trans = 'select sum(amount) as addr from customer_transactions where timestamp>="'.$from.'" and timestamp<="'.$to.'" and type="ADVANCE_PAID" and cust_id="'.$id.'"';
		$ad_dr = mysqli_fetch_array(execute_query($sql_trans));

		$sql_advance_set = 'select sum(advance_set_amt) as advancepay from customer_transactions where timestamp>="'.$from.'" and timestamp<="'.$to.'" and (type="RENT" OR type="BAN_AMT") and mop="credit" and cust_id='.$id;
		//echo $sql_trans;
		$advance_pay = mysqli_fetch_array(execute_query($sql_advance_set));
        
		$cust_balanace = $cust_opening+$dr_trans['trans'] - $cr_trans['trans']+$rent_pay['rentpay']+$ban_pay['banpay']-$cr_room['renp'] + $res_pay['respay'] + $ad_dr['addr'] - $ad_cr['adcr']-$advance_pay['advancepay'];
		return $cust_balanace;
}



function barcode_to_name($code){
	
	if($code!=''){
	
		$sql = 'select * from barcode_detector where barcode="'.$code.'"';
	
		$row = mysqli_fetch_array(execute_query($sql));
	
		$sql = 'select * from stock_available where sno='.$row['p_id'];
	
		$row = mysqli_fetch_array(execute_query($sql));
	
		return $row['description'];
		}
	else {
		 return $code;
	}

}



function check_stock_balance($stock, $date_from, $date_to, $party){

	$total=0;

	if($date_from!=0 && $date_to!=0){

		$date = ' and part_dateofpurchase>="'.$date_from.'" and part_dateofpurchase<="'.$date_to.'"';

		$recieve = ' and recieve_date>="'.$date_from.'" and recieve_date<="'.$date_to.'"';

		$issue = ' and issue_date>="'.$date_from.'" and issue_date<="'.$date_to.'"';

	}

	elseif($date_from!=0){

		$date = ' and part_dateofpurchase<"'.$date_from.'"';

		$recieve = ' and recieve_date<"'.$date_from.'"';

		$issue = ' and issue_date<"'.$date_from.'"';

	}

	else{

		$date = '';

	}

	$sql = 'select * from stock_available where sno='.$stock;

	$row = mysqli_fetch_array(execute_query($sql));

	$sql = 'select sum(qty) as sale from stock_sale where part_id='.$stock.$date;

	$sale = mysqli_fetch_array(execute_query($sql));

	$sql = 'select sum(qty) as issue from issue_stock where part_id='.$stock.$issue;

	$issue = mysqli_fetch_array(execute_query($sql));

	$sql = 'select sum(quantity) as to_oi from transactions_oi where type="TO" and part_id='.$stock;

	$to_oi = mysqli_fetch_array(execute_query($sql));

	$sql = 'select sum(qty) as purchase from stock_purchase where part_id='.$stock.$date;

	$purchase = mysqli_fetch_array(execute_query($sql));

	$sql = 'select sum(qty) as recieve from recieve_stock where part_id='.$stock.$recieve;

	$recieve = mysqli_fetch_array(execute_query($sql));

	$sql = 'select sum(quantity) as from_oi from transactions_oi where type="FROM" and part_id='.$stock;

	$from_oi = mysqli_fetch_array(execute_query($sql));

	$total = round(($purchase['purchase']+$recieve['recieve']+$from_oi['from_oi'])-($sale['sale']+$issue['issue']+$to_oi['to_oi']),2);

	if($date_to!=0){

		$opening = check_stock_balance($stock,$date_from,0,0);

	}

	else{

		$opening[1]=0;

	}

	$val[] = $total;

	$val[] = $row['opening'];

	$val[] = $total+$row['opening'];

	return $val;

}



function store_balance($store, $prod, $date_from){
	$sql = 'select * from stock_available where sno='.$prod;
	$row_stock = mysqli_fetch_array(execute_query($sql));
	$row_store['sno'] = $store;

	$sql = 'SELECT sum(stock_sale.qty) as qty FROM `stock_sale` join invoice_sale on stock_sale.invoice_no = invoice_sale.sno where part_id='.$row_stock['sno'].' and storeid='.$row_store['sno'].' and part_dateofpurchase<="'.$date_from.'"';
	$sale = mysqli_fetch_array(execute_query($sql));

	$sql = 'SELECT sum(stock_purchase.qty) as qty FROM `stock_purchase` join invoice_purchase on stock_purchase.invoice_no = invoice_purchase.sno where part_id='.$row_stock['sno'].' and storeid='.$row_store['sno'].' and part_dateofpurchase<="'.$date_from.'"';
	$purchase = mysqli_fetch_array(execute_query($sql));

	$sql = 'SELECT sum(stock_receive.quantity) as qty FROM `stock_receive` join invoice_receive on invoice_receive.sno = stock_receive.invoice_no where part_id='.$row_stock['sno'].' and store_id='.$row_store['sno'].' and invoice_receive.timestamp<="'.$date_from.'"';
	$recieve = mysqli_fetch_array(execute_query($sql));

	$sql = 'SELECT sum(stock_issue.quantity) as qty FROM `stock_issue` join invoice_issue on invoice_issue.sno = stock_issue.invoice_no where part_id='.$row_stock['sno'].' and store_id='.$row_store['sno'].' and invoice_issue.timestamp<="'.$date_from.'"';$issue = mysqli_fetch_array(execute_query($sql));

	$sql = 'SELECT sum(quantity_received) as qty FROM `transaction` where from_store='.$row_store['sno'].' and part_id='.$row_stock['sno'].' and timestamp<="'.$date_from.'"';
	$to = mysqli_fetch_array(execute_query($sql));

	$sql = 'SELECT sum(quantity_received) as qty FROM `transaction` where to_store='.$row_store['sno'].' and part_id='.$row_stock['sno'].' and timestamp<="'.$date_from.'"';
	$from = mysqli_fetch_array(execute_query($sql));

	$tot = ($from['qty']+$recieve['qty']+$purchase['qty'])-($to['qty']+$issue['qty']+$sale['qty']);
	$tot = $tot+$row_stock['opening'];
	return round($tot,2);
}





function get_store($id){

	$sql = 'select * from store where sno='.$id;

	$row = mysqli_fetch_array(execute_query($sql));

	return $row['name'];

}
function get_table($id){

	$sql = 'select * from res_table where sno='.$id;

	$row = mysqli_fetch_array(execute_query($sql));

	return $row['table_number'];

}


function get_pl_balance($from,$to,$id){

	$sql = 'select * from customer where parent="'.$id.'"';

	$res = execute_query($sql);

	$tot=0;

	while($row = mysqli_fetch_array($res)){

		$tot += get_cust_balace($from,$to,$row['sno']);

	}

	$tot += get_cust_balace($from,$to,$id);

	return $tot;

}



function get_pl_parent($id){

	if(is_numeric($id)){

		$sql = 'select * from customer where sno="'.$id.'"';

		$res = mysqli_fetch_array(execute_query($sql));

		$var = get_pl_parent($res['parent']);

		return $var;

	}

	else{

		return $id;

	}

}



function cash_in_hand($df, $dt, $id){

	if($id=='CASH IN HAND'){

		$sql = 'select * from pl_heads where description="CASH IN HAND"';

		$opening = mysqli_fetch_array(execute_query($sql));

		$i=0;

		$sale_date = $dt;

		$sql = 'SELECT sum(payment) as payment, sum(reciept) as reciept FROM `rojnamcha` where timestamp<="'.date("Y-m-d",strtotime($sale_date)-86400).'"';

		$prev = execute_query($sql);

		$prev=mysqli_fetch_array($prev);

		$bal_opening = $prev['reciept']-$prev['payment'];

		$tot_opening = $opening['opening']+$bal_opening;	

		$sql = "SELECT *, customer_transactions.amount as final_amount, customer_transactions.sno as serial_no FROM `customer_transactions` join rojnamcha on rojnamcha.timestamp = customer_transactions.timestamp where rojnamcha.timestamp = '".$sale_date."' and type in ('RECIEPT','PAYMENT')";

		//$sql = "SELECT * FROM `customer_transactions` where type in ('RECIEPT','PAYMENT')";

		$result = execute_query($sql);

		$tot_reciept = 0;

		$tot_payment = 0;

		$tot_reciept += $tot_opening;

		$i=1;

		while($row = mysqli_fetch_array($result)){

			$sql = 'select * from customer where sno='.$row['cust_id'];

			$cust = mysqli_fetch_array(execute_query($sql));

			if($row['type']=='RECIEPT'){

				$tot_reciept += $row['final_amount'];

			}

			if($row['type']=='PAYMENT'){

				$tot_payment += $row['final_amount'];

			}

		}

		$val = $tot_reciept-$tot_payment;

		return $val;

	}

	else{

		$val = get_cust_balace($df,$dt,$id);

		return $val;

	}

}



function get_product($id){

	$sql = 'select * from stock_available where sno='.$id;

	$result = execute_query($sql);

	$row = mysqli_fetch_array($result);

	return $row['description'];

}

function logvalidate($user1){

	$user1 = $_SESSION['username'];		

	if(isset($_SESSION['username'])){
		
		if($_SESSION['usertype']=='sadmin'){
			$sql='select * from users where userid = "'.$_SESSION['username'].'"';
			
		}
		else{
			$sql='select * from users where userid = "'.$_SESSION['username'].'" and type=1';
		}

		$user = mysqli_fetch_array(execute_query($sql));

		$sql = 'select * from user_access_detail where user_id= "'.$user['sno'].'" group by auth_id ';

		$res=execute_query($sql);

	    while($row = mysqli_fetch_array($res)){

			$sql='select * from auth_link where sno ="'.$row['auth_id'].'"';

			$new = execute_query($sql);

			$auth = mysqli_fetch_array($new);

			$sql='select * from user_authentication where auth_id="'.$auth['sno'].'"';

			$user_auth = mysqli_fetch_array(execute_query($sql));

			$sql='select * from user_authentication where sno = "'.$row['access_id'].'"';

			$link = mysqli_fetch_array(execute_query($sql));

			if($user_auth['type']=='main'){

			echo '

        <div id="icon" onClick="load_wind('.$auth['link'].')">

            <a href="'.$auth['link'].'?authenticationid='.$auth['sno'].'" style="width:80px;">'.$auth['auth_name'].'</a>

        </div>';

			}

			else{

			echo '

        <div id="icon" onClick="load_wind('.$user_auth['auth_link'].')">

            <a href="'.$link['auth_link'].'?authenticationid='.$row['sno'].'&access='.$row['access_id'].'" style="width:80px;">'.$auth['auth_name'].'</a>

        </div>';

			

				}

		}

		

	}

}

function page_logvalidate($user2){

	$user2 = $_SESSION['username'];

	

	if(isset($_SESSION['username'])){

		$sql='select * from users where userid = "'.$_SESSION['username'].'" and type=1';

		$user = mysqli_fetch_array(execute_query($sql));

		$sql='select * from user_access_detail where user_id= "'.$user['sno'].'" and auth_id='.$_GET['authenticationid'].'';

		$res=execute_query($sql);

		 echo '<div id="icon">

                   <a href="'.returnlink("index.php",false).'"><img style="width:50px;" src="images/back.png" /> </a>

                </div>';

	    while($row = mysqli_fetch_array($res)){

			$sql='select * from user_authentication where sno = "'.$row['access_id'].'"';

			$id_new = mysqli_fetch_array(execute_query($sql));

			echo '

        <div id="icon" onClick="load_wind('.$id_new['auth_link'].')">

            <a href="'.$id_new['auth_link'].'" style="width:80px;">'.$id_new['auth_name'].'</a>

        </div>';

			

		}

		

	}

}





function get_type($id){

	$sql = 'select * from new_type where sno='.$id;

	$row = mysqli_fetch_array(execute_query($sql));

	return $row['description'];

}
function get_item($id){
	$sql = 'SELECT * FROM `stock_available` where sno='.$id;
	$result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	return $row['description'];
}
function get_payroll_section($id){

	$sql = 'select * from payroll_section where sno='.$id;

	$row = mysqli_fetch_array(execute_query($sql));

	return $row['sec_name'];

}


function get_stock($id){
	$sql = 'select * from stock_available where sno='.$id;
	$row = mysqli_fetch_array(execute_query($sql));
	return $row['description'];
}

function get_user($id){
	$sql = 'select * from users where userid="'.$id.'"';
	$row = mysqli_fetch_array(execute_query($sql));
	return $row;
}

$nwords = array(    "zero", "one", "two", "three", "four", "five", "six", "seven",
				 "eight", "nine", "ten", "eleven", "twelve", "thirteen",
				 "fourteen", "fifteen", "sixteen", "seventeen", "eighteen",
			 "nineteen", "twenty", 30 => "thirty", 40 => "forty",
				 50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty",
				 90 => "ninety" );
function int_to_words($x)
{
 global $nwords;
 if(!is_numeric($x))
 {
	 $w = '#';
 }else if(fmod($x, 1) != 0)
 {
	 $w = '#';
 }else{
	 if($x < 0)
	 {
		 $w = 'minus ';
		 $x = -$x;
	 }else{
		 $w = '';
	 }
	 if($x < 21)
	 {
		 $w .= $nwords[$x];
	 }else if($x < 100)
	 {
		 $w .= $nwords[10 * floor($x/10)];

		 $r = fmod($x, 10);
		 if($r > 0)
		 {
			 $w .= '-'. $nwords[$r];
		 }
	 } else if($x < 1000)
	 {
		 $w .= $nwords[floor($x/100)] .' hundred';
		 $r = fmod($x, 100);
		 if($r > 0)
		 {
			 $w .= ' '. int_to_words($r);
		 }
	 } else if($x < 1000000)
	 {
		 $w .= int_to_words(floor($x/1000)) .' thousand';
		 $r = fmod($x, 1000);
		 if($r > 0)
		 {
			 $w .= ' ';
			 if($r < 100)
			 {
				 $w .= 'and ';
			 }
			 $w .= int_to_words($r);
		 }
	 } else {
		 $w .= int_to_words(floor($x/1000000)) .' million';
		 $r = fmod($x, 1000000);
		 if($r > 0)
		 {
			 $w .= ' ';
			 if($r < 100)
			 {
				 $word .= 'and ';
			 }
			 $w .= int_to_words($r);
		 }
	 }
 }
 return $w;
}
function get_floor($id){
	$sql = 'select * from floor_master where sno='.$id;
	$result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	return $row['floor_name'];
}
function get_room($id){
	$sql = 'select * from room_master where sno='.$id;
	$result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	return $row['room_name'];
}
function get_room_advance($id){
	$i = 1;
	$n = substr_count($id , ",");
	$n = $n + 1;
	$sql = 'select * from room_master where sno IN ('.$id.')';
	$result = execute_query($sql);
	while($row = mysqli_fetch_array($result)){
	foreach($result as $row){
		$room_name .= $row['room_name'];
		if($i < $n){
			$room_name .= ',';
			$i++;
		}
	}
}
	return $room_name;
}
function get_category($id){
	$sql = 'select * from category where sno='.$id;
	$result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	return $row['room_type'];
}
function get_cust_name($id){
	$sql = 'select * from customer where sno='.$id;
	$result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	return $row['cust_name'];
}
function get_company_name($id){
	$sql = 'select * from customer where sno='.$id;
	$result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	return $row['company_name'];
}

function get_advance($row){
	$sql = 'select *, sum(amount) as amount from customer_transactions where allotment_id='.$row['sno'];
	$result = execute_query($sql);
	$row1= mysqli_fetch_assoc($result);
	if($row['allotment_date']!=''){
		$break=explode('-',$row['allotment_date']);
		$total_days = cal_days_in_month(CAL_GREGORIAN, $break[1], $break[0]);
		$date = strtotime($row['allotment_date']);
		$day = date("d",$date);
		$no_of_days=$total_days-$day;
		$rent=($row['room_rent']*$no_of_days)/$total_days;
		$current_date=date('Y-m-d');
		$ts2 = strtotime($current_date);
		$year2 = date('Y', $ts2);
		$month2 = date('m',$ts2);
		$diff = (($year2 - $break[0]) * 12) + ($month2 - $break[1]);
		$total_rent=$row['room_rent']*$diff+round($rent);
		$pending=$row1['amount']-$total_rent;
		return round($pending);
	}
	else
		return $row1['amount'];
}
function get_availibility($id ,$from, $to){
	if($from=='' || $from==date("Y-m-d")){
		$from=date("2000-01-01");
		}
	if($to==''){
		$to=date("Y-m-d");
		}
	$sql='select * from allotment where room_id='.$id.' and allotment_date>="'.$from.'" and allotment_date<="'.$to.'"';
	if($to==date("Y-m-d")){
		$sql.="and exit_date IS NULL";
	}
	$result = execute_query($sql);
	$row= mysqli_num_rows($result);
	return $row;
	}
 function get_rent_details($rent,$allot_date,$id,$allot_id){
	 $allot_date=strtotime($allot_date);
	 $sql = 'select *, sum(amount) as amount from customer_transactions where cust_id='.$id;
	 $result = execute_query($sql);
	 $row= mysqli_fetch_assoc($result);
	 $today=time();
	 $hourdiff = round(abs($today - $allot_date)/3600);
	 $hourdiff=$hourdiff-2;
	 $days=ceil($hourdiff/24);
	 $total_rent=$rent*$days;
	 $remaining_rent=$row['amount']-$total_rent;
	 return $remaining_rent;
}
function get_room_rent($id){
	$sql='select *, sum(room_rent) as room_rent from allotment where cust_id='.$id;
    $result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	return $row['room_rent'];
}
function get_other_charges($id){
	$sql='select *, sum(other_charges) as other_charges from allotment where cust_id='.$id;
	$result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	return $row['other_charges'];
}

function check_pendency($rent, $allot_date, $id, $exit_date, $allot_id){
    if (empty($id) || empty($allot_id)) {
        return 0; // or handle as appropriate
    }

    $sql = 'SELECT SUM(amount) AS amount FROM customer_transactions WHERE cust_id=' . intval($id) . ' AND allotment_id=' . intval($allot_id) . ' AND type != "sale_restaurant"';
    $result = execute_query($sql);
    $row = mysqli_fetch_assoc($result);
    $days = get_days($allot_date, $exit_date);
    $rent = floatval($rent);
    $total_rent = $rent * $days;
    $remaining_rent = $total_rent - floatval($row['amount']);
    return $remaining_rent;
}

function get_balance($cust){
	
	//$sql = 'SELECT room_rent, DATEDIFF(IF(exit_date is null, CURRENT_TIMESTAMP, exit_date), allotment_date), (room_rent*DATEDIFF(IF(exit_date is null, CURRENT_TIMESTAMP, exit_date), allotment_date)) as total, sum((room_rent*DATEDIFF(IF(exit_date is null, CURRENT_TIMESTAMP, exit_date), allotment_date))) as total_rent, sum(other_charges) as other_charges FROM `allotment` where cust_id='.$cust;
	//echo $sql.'<br>';
	$sql = 'SELECT room_rent, allotment_date, exit_date, room_rent, other_charges as other_charges FROM `allotment` where cust_id='.$cust;
	$result = execute_query($sql);
	$rent=0;
	$other=0;
	while($row = $result->fetch_assoc()){
		if($row['exit_date']==''){
			$row['exit_date']=date("Y-m-d H:i:s");
		}
		$days = get_days($row['allotment_date'], $row['exit_date']);
		$rent += $row['room_rent']*$days;
		$other += $row['other_charges'];
	}
	
	$sql = 'select sum(amount) as amount from customer_transactions where cust_id='.$cust;
    $result = execute_query($sql);
	$row= mysqli_fetch_assoc($result);
	$balance = array();
	$balance[] = $rent;
	$balance[] = $other;
	$balance[] = $row['amount'];
	$balance[] = ($rent+$other)-$row['amount'];
	return $balance;
	
}

function get_days($from, $to) {
    // Validate input dates
    if (empty($from) || empty($to)) {
        return 1; // Return 1 day for empty inputs
    }

    $from_ts = strtotime($from);
    $to_ts = strtotime($to);
    
    // Validate timestamps
    if ($from_ts === false || $to_ts === false || $from_ts > $to_ts) {
        return 1; // Return 1 day for invalid dates or if check-out is before check-in
    }

    // Get dates at noon for comparison
    $from_date = date("Y-m-d", $from_ts);
    $to_date = date("Y-m-d", $to_ts);
    $from_noon = strtotime($from_date . " 12:00:00");
    $to_noon = strtotime($to_date . " 12:00:59");
    
    $days = 1; // Default to 1 day for same-day stays
    if ($from_date !== $to_date) {
        // Calculate days based on noon boundaries
        $days = ceil(($to_ts - $from_ts) / 86400);
        if ($from_ts >= $from_noon) {
            // Check-in after noon, add a day if check-out is after noon
            if ($to_ts >= $to_noon) {
                $days++;
            }
        } elseif ($to_ts >= $to_noon) {
            // Check-in before noon, check-out after noon
            $days++;
        }
    }
    
    return max(1, $days); // Ensure at least 1 day is billed
}

function get_unit($id){
	$sql = 'select * from unit where sno="'.$id.'"';
	//echo $sql.'<br>';
	$unit= mysqli_fetch_array(execute_query($sql));
	return $unit['unit'];
}
function username($id){
	$sql="select * from customer where sno='".$id."'";
	$run=execute_query($sql);
	$row=mysqli_fetch_array($run);
	return $row['cust_name'];

}
function address($id){
	$sql="select * from customer where sno='".$id."'";
	$run=execute_query($sql);
	$row=mysqli_fetch_array($run);
	return $row['address'];

}
function mobile($id){
	$sql="select * from customer where sno='".$id."'";
	$run=execute_query($sql);
	$row=mysqli_fetch_array($run);
	return $row['mobile'];

}
function  room_name($id){
	$sql="select * from room_master where sno='".$id."'";
	$run=execute_query($sql);
	$row=mysqli_fetch_array($run);
	return $row['room_name'];

}
function  floor_name($id){
	$sql="select * from floor_master where sno='".$id."'";
	$run=execute_query($sql);
	$row=mysqli_fetch_array($run);
	return $row['floor_name'];

}
function  category_name($id){
	$sql="select * from category where sno='".$id."'";
	$run=execute_query($sql);
	$row=mysqli_fetch_array($run);
	return $row['room_type'];

}

function get_state($id){
    $sql = 'SELECT * FROM `state_name` where sno="'.$id.'"';
    $state = mysqli_fetch_assoc(execute_query($sql));
    return $state['indian_states'];
}

function page_struct($title='WeKnow ERP') {

	
	$sql = 'select * from general_settings where `desc`="company"';
	$company = mysqli_fetch_array(execute_query($sql));
	$company = explode(" ", $company['rate']);
	$company_name = '';
	foreach($company as $k=>$v){
		$company_name .= '<span>'.substr($v, 0, 1).'</span>'.substr($v, 1).' ';
		//echo $v.'<br>';
	}
	$current_file_name = basename($_SERVER['PHP_SELF']);
	
	$sql = 'select * from general_settings where `desc`="company"';
	$company_name = mysqli_fetch_assoc(execute_query($sql));
	$company_name = $company_name['rate'];

	$sql = 'select * from general_settings where `desc`="gstin"';
	$company_gstin = mysqli_fetch_assoc(execute_query($sql));
	$company_gstin = $company_gstin['rate'];

	$sql = 'select * from general_settings where `desc`="pan"';
	$company_pan = mysqli_fetch_assoc(execute_query($sql));
	$company_pan = $company_pan['rate'];
	
	$sql = 'select * from general_settings where `desc`="address"';
	$company_address = mysqli_fetch_assoc(execute_query($sql));
	$company_address = $company_address['rate'];
	
	if(isset($_SESSION['session_id'])) {
		$sql = 'select * from navigation where hyper_link="'.$current_file_name.'"';
		$file = mysqli_fetch_array(execute_query($sql));
		logvalidate($file['sno']);
		$title = $file['link_description'];
	}
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>'.$title.'</title>
<link href="css/light-bootstrap-dashboard.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/component.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/jcarousel.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/pagination.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/jquery.multiselect.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/jquery.datetimepicker.css" rel="stylesheet" type="text/css" media="all" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Playwrite+HU:wght@100..400&family=Teko:wght@300..700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="js/jquery-1.11.2.min.js" language="javascript" type="text/javascript"></script>
<script src="js/calendar.js" language="javascript" type="text/javascript"></script>
<script src="js/jcarousel.js" language="javascript" type="text/javascript"></script>
<script src="js/bpopup.js" language="javascript" type="text/javascript"></script>
<script src="js/canvasjs.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="js/fontawesome.js" language="javascript" type="text/javascript"></script>
<script src="js/light-bootstrap-dashboard.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="jquery/jquery-ui.min.js" type="text/javascript"></script>
<script src="jquery/jquery.stickyheader.js" type="text/javascript"></script>
<script src="jquery/jquery.ba-throttle-debouce.min.js" type="text/javascript"></script>
<script src="jquery/jquery.multiselect.js" language="javascript"></script>
<script type="text/javascript" language="javascript">

$(document).ready( 
	function() {
		// Add the "focus" value to class attribute
		$("input").focusin( 
			function() {
				$(this).addClass("focus");
			}
		);
		$("select").focusin( 
			function() {
				$(this).addClass("focus");
			}
		);
		$(":checkbox").focusin( 
			function() {
				$(this).addClass("focus");
			}
		);
		// Remove the "focus" value to class attribute
		$("input").focusout( 
			function() {
				$(this).removeClass("focus");
			}
		);
		$("select").focusout( 
			function() {
				$(this).removeClass("focus");
			}
		);
		$(":checkbox").focusout( 
			function() {
				$(this).removeClass("focus");
			}
		);
	}
);

</script>
<script language="javascript" type="text/javascript">
function check_prev_date(form_date){
	var cur_date = "'.date("Y-m-d").'";
	var warn = 0;
	$(".noblank").each(function(index, element){
		if($(element).val()==""){
			$( element ).css( "backgroundColor", "yellow" );
			warn = 1;
		}
		else{
			$( element ).css( "backgroundColor", "white" );
		}
	});
	if(warn!=0){
		alert("Please enter all complusory blocks");
		return false;
	}

	if(cur_date>form_date){
		var response = confirm("Entry date is old than today. Do you want to proceed. ?");
	}
	else{
		var response = confirm("Are you sure?");
	}
	return response;
}
</script>
<link href="jquery/jquery-ui.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body  style="background-color:#ccc">
    
		<div id="content" class="print-only">
            <div style="border:0px solid;opacity:0.6;z-index:10;top: 0;position:absolute;background-color:#3a3f51;color:white;text-align:center;padding:10px; width:100%;display: flex; justify-content: space-around;align-items:center;">';
	
	echo '<img src="images/a2.png" alt="" width="140px" height="100px">';
		$sql = 'select * from general_settings where `desc`="restaurant_name"';
		$restaurant_name = mysqli_fetch_assoc(execute_query($sql));
		$restaurant_name = $restaurant_name['rate'];

		$sql = 'select * from general_settings where `desc`="restaurant_gstin"';
		$restaurant_gstin = mysqli_fetch_assoc(execute_query($sql));
		$restaurant_gstin = $restaurant_gstin['rate'];

		$sql = 'select * from general_settings where `desc`="restaurant_pan"';
		$restaurant_pan = mysqli_fetch_assoc(execute_query($sql));
		$restaurant_pan = $restaurant_pan['rate'];

		$sql = 'select * from general_settings where `desc`="company"';
		$hotel_name = mysqli_fetch_assoc(execute_query($sql));
		$hotel_name = $hotel_name['rate'];

		$sql = 'select * from general_settings where `desc`="address"';
		$hotel_address = mysqli_fetch_assoc(execute_query($sql));
		$hotel_address = $hotel_address['rate'];

		$sql = 'select * from general_settings where `desc`="gstin"';
		$hotel_gstin = mysqli_fetch_assoc(execute_query($sql));
		$hotel_gstin = $hotel_gstin['rate'];

		$sql = 'select * from general_settings where `desc`="pan"';
		$hotel_pan = mysqli_fetch_assoc(execute_query($sql));
		$hotel_pan = $hotel_pan['rate'];
	
		$file = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
		if(strrpos($file, "_restaurant")){
			echo '<div><h2 style="color:white;">'.$restaurant_name.'</h2>
				<h2 style="color:white;">'.$hotel_address.'</h2></div>'
				;
		}
		else{
			echo '<div><h3>'.$hotel_name.'</h3>
				<h4 >'.$hotel_address.'</h4></div>'
				;
		}
	echo '<div style="display: flex; align-items: center; font-family: Arial, sans-serif; font-size: 14px;">
    <img src="images/logo.gif" alt="" width="80px" height="80px" style="margin-right: 10px;">
    <div style="display: flex; flex-direction: column; line-height: 1.2;">
	<span style="font-size: 12px; color: #fff;">powered by</span>
        <a href="https://weknowtech.in" style="color: #127dc9; text-decoration: none; font-weight: bold; font-size: 16px;">Weknow Tech</a>
        
    </div>
</div>

            </div>
        </div>

        <div class="clear"></div>

';
}

?>

<?php
function calculate_invoice_amount($allotment_details, $customer_state, $hotel_state, $restaurant_data = []) {
    $days = get_days($allotment_details['allotment_date'], $allotment_details['exit_date']);
    $room_rent = floatval($allotment_details['original_room_rent'] ?? 0);
    $other_discount = is_numeric($allotment_details['other_discount'] ?? 0) ? floatval($allotment_details['other_discount']) : 0;
    $discount_value = is_numeric($allotment_details['discount_value'] ?? 0) ? floatval($allotment_details['discount_value']) : 0;
    $disc_val = $other_discount + $discount_value;
    $other_charges = floatval($allotment_details['other_charges'] ?? 0);
    $tax_rate = floatval($allotment_details['tax_rate'] ?? 0) / 2; // Assuming tax_rate is total (e.g., 12% split as 6% CGST + 6% SGST)
    $invoice_type = $allotment_details['invoice_type'] ?? 'tax';

    $daily_breakdown = [];
    $taxable_total = 0;
    $total_cgst = 0;
    $total_sgst = 0;
    $total_restaurant = 0;

    // Loop through each day
    $start_date = strtotime($allotment_details['allotment_date']);
    for ($i = 0; $i < $days; $i++) {
        $current_date = date('d-m-Y', $start_date + ($i * 86400));
        if ($invoice_type === 'tax') {
            $base_rent = $room_rent + $other_charges - ($disc_val / $days); // Distribute discount evenly
            $cgst = round($base_rent * ($tax_rate / 100), 2); // 6% CGST
            $sgst = round($base_rent * ($tax_rate / 100), 2); // 6% SGST
            $day_total = $base_rent + $cgst + $sgst;
        } else {
            $base_rent = $room_rent;
            $cgst = 0;
            $sgst = 0;
            $day_total = $base_rent;
        }
        $daily_breakdown[] = [
            'date' => $current_date,
            'room_rent' => $base_rent,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'total' => $day_total
        ];
        $taxable_total += $base_rent;
        $total_cgst += $cgst;
        $total_sgst += $sgst;
    }

    // Restaurant data
    foreach ($restaurant_data as $r) {
        $total_restaurant += floatval($r['amount']);
    }

    // Grand total
    $grand_total = $taxable_total + $total_cgst + $total_sgst + $total_restaurant;
    $amount_payable = round($grand_total);
    $round_off = round($amount_payable - $grand_total, 2);

    return [
        'daily_breakdown' => $daily_breakdown,
        'taxable_total' => round($taxable_total, 2),
        'total_cgst' => round($total_cgst, 2),
        'total_sgst' => round($total_sgst, 2),
        'total_discount' => round($disc_val, 2),
        'other_charges' => round($other_charges, 2),
        'restaurant_total' => round($total_restaurant, 2),
        'grand_total' => round($grand_total, 2),
        'amount_payable' => $amount_payable,
        'round_off' => $round_off
    ];
}
?>