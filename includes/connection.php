<?php

	/**
	* Connection Class
	* @var $host protected string host name.
	* @var $user protected string user name of database.
	* @var $pass protected string password name of database.
	* @var $db protected string database name
	* @var $table protected string database name
	* @var $con protected  object representing the connection to the MySQL server 
	*/
	class connect
	{
		protected $host 	= "localhost";
		protected $user 	= "root";
		protected $pass 	= "";
		protected $db 		= "audio_book";
		protected $table 	= "login";
		protected $con;

		function __construct()
		{
			$this->con = mysqli_connect(
				$this->host,
				$this->user,
				$this->pass,
				$this->db
				)
				Or 
				die("connection failure".mysqli_error($this->con));
			
			if($this->con)
			{
				echo "<h1>connection successfull</h1>";
			}
		}

		function checkAdmin($user,$pass,$table)
		{

			$flag 	= 0;

			$query 	= "select * from ".$table;

			$res 	= mysqli_query($this->con,$query)
						or
						die("<strong>Query Fail to Execute:</strong>".mysqli_error($this->con));

			$row 	= mysqli_fetch_object($res);
			

			if($user == $row->username && $pass == $row->password)
			{

				$flag = 1;

				$_SESSION["$user"];
						
				return $flag;
			}
			
			return $flag;
		}

		function selectQuery($table,$fields,$condition="",$limit="",$display_query="")
		{
			$this->table = $table;

			$query = "select ".$fields." from ".$table." ".$condition." ".$limit;
			
			if($display_query !="")
			{
				echo "<br><strong> query is :</strong>".$query."<br>";
				die("Query displayed above");
			}

			$result = mysqli_query($this->con,$query)
						or
						die("<strong>Query Fail to Execute:</strong>".mysqli_error($this->con));
			
			return $result;
		}

		function total_records($res)
     	{
			return mysqli_num_rows($res);
			
     	}

		function maxid($table,$field,$alias="",$condition="",$display_query="")
		{
			$query = "select max(".$field.")from ".$table."";

			$res = mysqli_query($this->con,$query);

			$record = mysqli_fetch_array($res);
			

			$id = $record[0];

			return $id;
			/*if($condition!="")
			{
				$res="select ifnull(max(".$field."),0)+1 as ".$alias." from ".$table." ".$condition;
			}
			else
			{
				$res="select ifnull(max(".$field."),0)+1 as ".$alias." from ".$table;
			}

			if($display_query !="")
			{
				echo "<br> query is ".$res."<br>";
			}

			$result = mysqli_query($res, $this->con);			

			$row_maxid = mysqli_fetch_object($result);

			$max_id = $row_maxid->$alias;			

			return $max_id;*/
		}		

		function minid($table,$field,$alias,$condition="",$display_query="")
		{
			if($condition!="")
			{
				$res="select ifnull(min(".$field."),0)+1 as ".$alias." from ".$table." ".$condition;
			}
			else
			{
				$res="select ifnull(min(".$field."),0)+1 as ".$alias." from ".$table;
			}

			if($display_query !="")
			{
				echo "<br> query is ".$res."<br>";
			}

			$result=mysqli_query($this->con,$res);			

			$row_minid = mysqli_fetch_object($result);

			$min_id=$row_minid->$alias;			

			return $min_id;
		}		

	  	function insert_record($table,$field)
	   	{
		  	$q 	= "";

		  	$q1 = "";

		 	foreach($field as $i => $value)
		 	{
				$value = addslashes($value);

				$q 	= $q.$i.",";

			 	$q1 = $q1."'".$value."',";

				//echo $q1;

				//die();
	     	}	  

			$q 	= substr($q,0,strlen($q)-1);

			$q1 = substr($q1,0,strlen($q1)-1);


		 	$sql="insert into ".$table."(".$q .") values (".$q1.")";

		 	//echo "Insert SQL==".$sql; die;

 	     	$rs = mysqli_query($this->con,$sql) or die("There is some error in insert query in table ".$table.mysqli_error($this->con));

		 	return $rs;

		}

  	  function update($table,$field,$condition)
	   {
	      $q = "";

		  $q1 = "";

		  $str = "";

		  foreach($field as $i => $value)
		  {

			$q = $q.$i.",";
			$q1 = $q1.$value.",";
			$str = $str.$i." = '".$value."',";
			//$q=$q.$i.",";

		 	//$q1=$q1."'".$value."',";
	      }

		  $q = substr($q,0,strlen($q)-1);

		  $q1 = substr($q1,0,strlen($q1)-1);

		  $str=substr($str,0,strlen($str)-1);

		  $sql="update ".$table." set ".$str."".$condition;
		 

		  //echo "<br>".$sql;

		  $result_update = mysqli_query($this->con,$sql)or die("update query==".mysqli_error($this->con));

		  return $result_update;

	   }//end of update	   

	    function search_record($table,$field,$criteria,$condition="")
		{

	    	echo "test";

		   	$q="";

		   	$q1="";

		   	$str="";

           	if($criteria =="OR")

			{

		  		foreach($field as $i => $value)

			   	{

			     	$q=$q.$i.",";

					if(is_numeric($value))

				  	{ 

				   		if($value !=NULL)

						{

					  		$q1=$q1.$value.",";

					  		$str=$str.$i."= ".$value." OR ";

						}

				   		else

						{

					   		$q1=$q1." NULL ".",";

					   		$str=$str.$i."= NULL "." OR ";

						}

			 	  	} 

				  	else

			      	{

					 	if($value != "")

					 	{

							$q1=$q1."'".$value."',";

					   		$str=$str.$i."= '".$value."'"." OR ";

			  	     	}

					 	else

					  	{

							 $q1=$q1."''";

					     	$str=$str.$i."= '' "." OR ";

					  	}	

				   	}

				}	  

		   		$str=substr($str,0,strlen($str)-4);

	            $sql="select * from ".$table ." where  ".$str;

				$rs=mysqli_query($this->con,$sql);	

				return $rs;

			} //end of if criteria

			else

			{

				foreach($field as $i => $value)

			   	{

			     	$q=$q.$i.",";

				 	if(is_numeric($value))

				  	{ 

				    	if($value !=NULL)

					 	{

					   		$q1=$q1.$value.",";

					   		$str=$str.$i."= ".$value." AND ";

					 	}
					 	else
					 	{

					   		$q1=$q1." NULL ".",";

					 	}
				 	}
				 	else
				  	{
					 	if($value != "")
					 	{
				 	   		$q1=$q1."'".$value."',";

					   		$str=$str.$i."= '".$value."'"." AND ";
					 	}
					 	else
					  	{

						 	$q1=$q1."''";
					   	}	
				   	}
				}	  

		   		$str = substr($str,0,strlen($str)-4);

	            $sql="select * from ".$table ." where  ".$str;

				$rs=mysqli_query($this->con,$sql);	

				return $rs;

			}

	  	}//end of search

	 	

		function delete($table,$condition)

	    {

			$sql="delete from ".$table." ".$condition ;

		   	//echo $sql;

		   	$rs=mysqli_query($this->con,$sql) or die("There is some error in delete query :".mysqli_error($this->con));

		    return $rs;

		}  

		function check_duplicate($table_name,$field_name,$condition,$print_query="")
		{

			$query = "select ".$field_name."  from ".$table_name." ". $condition;

			if($print_query!="")

				echo $query;			
			$rs = mysqli_query($this->con,$query) or  die("Some error in query:". mysqli_error($this->con));

			$num=mysqli_num_rows($rs);

			if($num==0)
				return false;
			else
				return true;		
		}
	}//end of class DB_connect

?>