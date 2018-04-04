<?php
class dbConnect{
	private $host = 'localhost';
	private $username = 'root';
	private $password = '';
	private $database = 'webservice_demo';

	private $con = NULL;
	
	public function __construct(){
		if($this->con){
			return $con;
		}else{
			$this->con = mysqli_connect($this->host,$this->username,$this->password,$this->database);
			return $this->con;
		}
	}

	function buildInsertQuery($tableName,$paramArray,$expected_params){
		$fields	=	array();
		$values	=	array();	
		// $fields	=	implode(",",array_map('enclosefield',array_keys($paramArray)));		
		
		foreach($paramArray as $k => $v){
			$fields[]	=	"`$k`";
			if($expected_params[$k] == 'string'){
				$values[]	=	"'$v'";
			}else{
				$values[]	=	"$v";
			}
		}
		$fieldsStr	=	implode(",",$fields);
		$valuesStr	=	implode(",",$values);
		
		$query	=	"INSERT INTO `$tableName` (`id`,$fieldsStr) VALUES (NULL,$valuesStr)";
		return $query;	
	}
	
	function buildSelectQuery($tableName,	$columnListStr="*",	$paramArray=NULL,	$expected_params=NULL){
		
		$columns	=	'*';
		$whereStr	=	'';
		
		if($columnListStr != "*"){
			$columnListArray	=	explode(",",$columnListStr);
			$columns	=	implode(",",array_map('enclosefield',$columnListArray));
		}
		
		if($paramArray && $expected_params){
			
			$whereArray	=	array();
			
			foreach($paramArray as $k => $v){			
				if($expected_params[$k] == 'string'){
					$whereArray[]	=	"`$k` = '$v'";
				}else{
					$whereArray[]	=	"`$k` = $v";
				}
			}
			
			$whereStr	=	" WHERE ".implode(" AND ",$whereArray);
		}
		
		$query	=	"SELECT $columns FROM `$tableName` $whereStr";
		return $query;	
	}
	
	function buildUpdateQuery($tableName,$paramArray,$expected_params,$id){
		$values	=	array();	
		// $fields	=	implode(",",array_map('enclosefield',array_keys($paramArray)));		
		
		foreach($paramArray as $k => $v){
			if($expected_params[$k] == 'string'){
				$values[]	=	"`$k`='$v'";
			}else{
				$values[]	=	"'$k'=$v";
			}
		}		
		$valuesStr	=	implode(", ",$values);
		
		$query	=	"UPDATE `$tableName` SET $valuesStr WHERE `id` = $id";
		return $query;	
	}

	function enclosefield($val){
		return "`$val`";
	}
	function enclosevalue($val){
		global $expected_params;
		if($expected_params[$val]=='string'){
			return "'$val'";
		}else{
			return "$val";
		}
	}

	function insert($query){
		$status	=	mysqli_query($this->con,$query);
		return $status;
	}
	
	function select($query){
		$resultSet	=	mysqli_query($this->con,$query);
		$resultArray	=	array();
				
		$rows		=	mysqli_num_rows($resultSet);		
		
		if($rows){
			while($row = mysqli_fetch_assoc($resultSet)){
				$resultArray[]	=	$row;				
			}
		}
		return $resultArray;
	}
	
	function update($query,$id){
		$status	=	mysqli_query($this->con,$query);
		return $status;
	}
	
	function delete($tableName,$id){
		$query	=	"DELETE FROM `$tableName` WHERE `id` = $id";
		$status	=	mysqli_query($this->con,$query);
		return $status;
	}
}
?>