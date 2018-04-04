<?php
//set response header.
include_once "db.php";
$obj	=	new dbConnect();
$expected_params	=	array('id'=>'int','name'=>'string','contact'=>'string','city'=>'string');
header("Content-Type : application/json");

if($_SERVER['REQUEST_METHOD']=="GET"){
	if(!$_GET){
		$selectQuery	=	$obj->buildSelectQuery('students',"*");		
		$resultArray	=	$obj->select($selectQuery);
		$result			=	array();
		$result['total rows']		=	count($resultArray);
		$result['data']	=	$resultArray;
		header('HTTP/1.1 200 OK');
		echo json_encode($result);
		exit;
	}else{
		$whereArray	=	array();
		foreach($_GET as $k => $v){
			$whereArray[$k]	=	$v;
		}
		
		$selectQuery	=	$obj->buildSelectQuery('students',"*",$whereArray,$expected_params);		
		$resultArray	=	$obj->select($selectQuery);
		$result			=	array();
		$result['total rows']		=	count($resultArray);
		$result['data']	=	$resultArray;
		header('HTTP/1.1 200 OK');
		echo json_encode($result);
		exit;
	}
}elseif($_SERVER['REQUEST_METHOD']=="POST"){
	unset($expected_params['id']);
	if(!$_POST){
		header('HTTP/1.1 400 Bad Request');
		echo json_encode(array("message" => "No Values Passed"));
		exit;
	}
	
	$errorParams		=	array();
	$paramArray			=	array();
	
	foreach($_POST as $k => $v){
		
		if(!array_key_exists($k,$expected_params)){
			$errorParams[] = $k;			
		}else{
			$paramArray[$k]	=	$v;
		}
	}
	
	if($errorParams || count($paramArray) != count($expected_params)){
		header('HTTP/1.1 400 Bad Request');
		echo json_encode(array("status" => array("message" => "Post variables are not as expected. Expected values are as follows","params" => array_keys($expected_params))));
		exit;
	}else{
		$insertQuery	=	$obj->buildInsertQuery('students',$paramArray,$expected_params);
		$selectQuery	=	$obj->buildSelectQuery('students',"*",$paramArray,$expected_params);
		
		$resultArray	=	$obj->select($selectQuery);
		
		if(!$resultArray){

			$status			=	$obj->insert($insertQuery);		
		
			if($status){
				header('HTTP/1.1 201 Created');
				echo json_encode(array("message" => "Record inserted Successfully"));
				exit;
			}else{
				header('HTTP/1.1 500 Internal Server Error');
				echo json_encode(array("message" => "Server is not able to fulfill the request at this moment"));
				exit;
			}	
		}else{
			header('HTTP/1.1 202 Accepted');
			echo json_encode(array("message" => "Record already Present"));
			exit;
		}	
	}
	// echo json_encode($ax);
}elseif($_SERVER['REQUEST_METHOD']=="PUT"){
	
	if(!$_REQUEST){
		header('HTTP/1.1 400 Bad Request');
		echo json_encode(array("status" => array("message" => "No Values Passed. Expected values are as follows","params" => array_keys($expected_params))));
		exit;
	}
	$errorParams		=	array();
	$paramArray			=	array();
	$id					=	'';
	
	foreach($_REQUEST as $k => $v){
		
		if(!array_key_exists($k,$expected_params)){
			$errorParams[] = $k;
		}else{
			if($k == 'id'){
				$id	=	$v;
			}else{
				$paramArray[$k]	=	$v;
			}
		}
	}
	
	if(!$id){
		header('HTTP/1.1 400 Bad Request');
		echo json_encode(array("status" => array("message" => "Student ID is required. Expected values are as follows","params" => array_keys($expected_params))));
		exit;
	}
	
	if($errorParams || count($paramArray) < 1){
		header('HTTP/1.1 400 Bad Request');
		echo json_encode(array("status" => array("message" => "Please provide the value to update based on id. Expected values are as follows","params" => array_keys($expected_params))));
		exit;
	}else{
		$updateQuery	=	$obj->buildUpdateQuery('students',$paramArray,$expected_params,$id);
		
		$selectQuery	=	$obj->buildSelectQuery('students',"*",array('id' => $id),$expected_params);
		
		$resultArray	=	$obj->select($selectQuery);
		
		if($resultArray){
			
			$status		=	$obj->update($updateQuery,$id);		
		
			if($status){
				header('HTTP/1.1 201 Created');
				echo json_encode(array("message" => "Record updated Successfully"));
				exit;
			}else{
				header('HTTP/1.1 500 Internal Server Error');
				echo json_encode(array("message" => "Server is not able to fulfill the request at this moment"));
				exit;
			}	
		}else{
			header('HTTP/1.1 202 Accepted');
			echo json_encode(array("message" => "No record present with $id to update"));
			exit;
		}	
	}
}elseif($_SERVER['REQUEST_METHOD']=="DELETE"){
	if(!isset($_REQUEST['id'])){
		header('HTTP/1.1 400 Bad Request');
		echo json_encode(array("message" => "No id Passed"));
		exit;
	}
	
	$id	=	$_REQUEST['id'];
	
	if(!$id){
		header('HTTP/1.1 400 Bad Request');
		echo json_encode(array("message" => "No id dPassed"));
		exit;
	}
	$selectQuery	=	$obj->buildSelectQuery('students',"*",array('id' => $id),$expected_params);
		
	$resultArray	=	$obj->select($selectQuery);
	
	if($resultArray){	
		$status		=	$obj->delete('students',$id);		
			
		if($status){
			header('HTTP/1.1 200 OK');
			echo json_encode(array("message" => "Record deleted Successfully"));
			exit;
		}else{
			header('HTTP/1.1 500 Internal Server Error');
			echo json_encode(array("message" => "Server is not able to fulfill the request at this moment"));
			exit;
		}	
	}else{
		header('HTTP/1.1 202 Accepted');
		echo json_encode(array("message" => "No record present with $id to delete"));
		exit;
	}
}else{
	
}
?>