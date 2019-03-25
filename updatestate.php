<?php

$file = fopen("php://input","r");

$jsonInput ="";

while(!feof($file))
{
	$jsonInput .= fgets($file);	
}

fclose($file);

	$input_params = json_decode($jsonInput,true);
    


    include("includes/common_class.php");
	
	include("includes/config.php");
	


    	$state_name = $input_params['state_name'];
    	
    	$state_id = $input_params['state_id'];

    	if (empty($state_name) || empty($state_id)) 
    	{
    		
    		header('Content-type: application/json');

			echo json_encode(array("status"=>0,"message"=>"Please fill all required Fields"));	
    	}
    	else
    	{

			$query_class_duplicate=$con->select_query("state","*","Where state_id='".$state_id."'");
			$rowfetchduplicate = mysqli_fetch_assoc($query_class_duplicate);
			if(mysqli_num_rows($query_class_duplicate) < 0)
			{
				header('Content-type: application/json');
				echo json_encode(array('status'=>0,'message'=>'State is not availble.'));
				
			}
			else
			{    		
    		
    		$state=array(

    			"state_name"=>$state_name

    		);

    		$insertUser = $con->update("state",$state,"where state_id='".$state_id."'");

					// $user_id = mysqli_insert_id();

					//print_r($userid);die();
					header('Content-type: application/json');

					echo json_encode(array("status"=>1,"message"=>"state update Successfully."));
    	}
    }
?>