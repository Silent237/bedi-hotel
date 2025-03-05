<?php
    $queries = [];
	$db = mysqli_connect("localhost", "root", "mysql", "cloudsdj_bedi");
	if(!$db){
		die("Error 1 : Contact Administrator.");
	}
	execute_query("SET TIME_ZONE='+05:30'", $db);	

function execute_query($query){
	global $db, $queries;

    $start_time = microtime(true);
    $result = mysqli_query($db, $query);
    $end_time = microtime(true);
    $execution_time = $end_time - $start_time;

    $queries[] = [
        'query' => $query,
        'time' => $execution_time
    ];
	
	return $result;
}

function insert_id($db=''){
	global $db;
	return mysqli_insert_id($db);
}

function select_data($table, $fields, $where='', $join='', $join_on='', $union='', $union_cols=''){
	
}

function update_data($table, $fields, $values, $where){
	
}

function delete_data($table, $fields, $values, $where){
	
}

?>