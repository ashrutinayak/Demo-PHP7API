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
	


    	$query_teacher=$con->select_query("state","*","","");
            if($con->total_records($query_teacher) != 0)
            {
                $x=0;
                $TeacherList=array();
                while($row_teacher = mysqli_fetch_array($query_teacher))
                {
                        $TeacherList[$x]["state_id"]=intval($row_teacher['state_id']);
                        $TeacherList[$x]["state_name"]=$row_teacher['state_name'];
                         $x++;
                        
                    }
                    header('Content-type: application/json');
                    echo json_encode(array("status"=>1,"statedetail"=>$TeacherList,"message"=>"State Listed Successfully."));
                }



?>