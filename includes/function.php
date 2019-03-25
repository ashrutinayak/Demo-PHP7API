<?php

	// Redirecting URL to given location

		

		function getRemoteFileSize($url)

		{ 

		   $parsed = parse_url($url); 

		   $host = $parsed["host"]; 

		   $fp = @fsockopen($host, 80, $errno, $errstr, 20); 

		   if(!$fp)return false; 

		   else { 

		       @fputs($fp, "HEAD $url HTTP/1.1\r\n"); 

		       @fputs($fp, "HOST: $host\r\n"); 

		       @fputs($fp, "Connection: close\r\n\r\n"); 

		       $headers = ""; 

		       while(!@feof($fp))$headers .= @fgets ($fp, 128); 

		   } 

		   @fclose ($fp); 

		   $return = false; 

		   $arr_headers = explode("\n", $headers); 

		   foreach($arr_headers as $header) { 

		       $s = "Content-Length: "; 

		       if(substr(strtolower ($header), 0, strlen($s)) == strtolower($s)) { 

		           $return = trim(substr($header, strlen($s))); 

		           break; 

		       } 

		   } 

		   if($return){ 

		              $size = round($return / 1024, 2); 

		              $sz = "KB"; // Size In KB 

		        if ($size > 1024) { 

		            $size = round($size / 1024, 2); 

		            $sz = "MB"; // Size in MB 

		        } 

		        $return = "$size $sz"; 

		   } 

		   return $return; 

		} 



		function format_number($str) 

		{

			return number_format($str, 2, '.', '');

		}

		function RTESafe($strText) 

		{

			//returns safe code for preloading in the RTE

			$tmpString = trim($strText);

			

			//convert all types of single quotes

			$tmpString = str_replace(chr(145), chr(39), $tmpString);

			$tmpString = str_replace(chr(146), chr(39), $tmpString);

			$tmpString = str_replace("'", "&#39;", $tmpString);

			

			//convert all types of double quotes

			$tmpString = str_replace(chr(147), chr(34), $tmpString);

			$tmpString = str_replace(chr(148), chr(34), $tmpString);

		//	$tmpString = str_replace("\"", "\"", $tmpString);

			

			//replace carriage returns & line feeds

			$tmpString = str_replace(chr(10), " ", $tmpString);

			$tmpString = str_replace(chr(13), " ", $tmpString);

			

			return $tmpString;

		}

		function RTEChange($strText) 

		{

			$tmpString=ereg_replace('(.")',"'",$strText);

			return $tmpString;

		}

		function redirect($to)

		{

	  		$schema = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';

		  	$host = strlen($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];

	 		if (headers_sent()) 

			{

				

				

				return false;

		

		

			}

	  		else

	  		{

				header("HTTP/1.1 301 Moved Permanently");

				

				//$temp_local = "core/iflameapi/restlife/";

				$temp_local = "core/iflameapi/restlife";

				//$temp_local = "development/newadmin";

				header("Location: $schema://$host/$temp_local/$to");

				//header("Location: $schema://$host/$to");

				exit();

		 	}

	}

	

	

	// Encrpting the Data

	function encrypt($id)

	{

		//base64_decode($str);

		/*encode

		$eno = $id ;

		$eno = ($eno*3900)/13;

		$enew_no = dechex($eno);

		return $enew_no;*/

		return base64_encode($id);

	}



	// Dycrypting the Data

	function decrypt($id)

	{

		/*$dno =  hexdec($id) ;

		$dno = ($dno*(13/3900));

		return $dno;*/

		return base64_decode($id);

	}

	

	function imageUpload($images,$user_id=null,$flag=null)

	{

		$dirswfl = $_SERVER['DOCUMENT_ROOT']."core/ripesale/admin/uploadedImages/".$user_id;



		if(!is_dir($dirswfl))

		{

		$chmod = (is_dir($dirswfl) === true) ? 644 : 777;

		if (in_array(get_current_user(), array('apache', 'httpd', 'nobody', 'system', 'webdaemon', 'www', 'www-data')) === true)

		{

		$chmod += 22;

		}

		

		mkdir($dirswfl);

		chmod($dirswfl, octdec(intval($chmod)));

		

		}



		$target = $_SERVER['DOCUMENT_ROOT']."core/ripesale/admin/uploadedImages/".$user_id."/";

		$imageCount = count($_FILES[$images]['name']);

		for($i =0;$i<$imageCount;$i++)

		{

			$pieces = explode(".",$_FILES[$images]['name'][$i]);

			$countPieces = count($pieces);

			if($countPieces == 2)

			{

				$extension = strtolower($pieces[1]);

			}

			else

			{

				$extension = strtolower($pieces[$countPieces-1]);

			}

			$cleanstring = strtolower(trim(preg_replace('#\W+#', '_', $pieces[0]), '_'));

			$photo_file = $user_id.'_'.$cleanstring.'.'.$extension;

			$originalImage=$target.$photo_file;

			

			if(in_array($extension , array('jpg','jpeg', 'gif', 'png', 'bmp','pdf')))

			{	

				//Thumbnail file name File

				$image_filePath = $_FILES[$images]['tmp_name'][$i];

				$img_fileName = $user_id.'_'.$cleanstring.'_Thumb.'.$extension;

				$img_thumb = $target.$img_fileName;

				$extension = strtolower($pieces[1]);

				//Check the file format before upload

				

				//Find the height and width of the image

				list($gotwidth, $gotheight, $gottype, $gotattr)= getimagesize($image_filePath);

				//---------- To create thumbnail of image---------------

				if($extension=="jpg" || $extension=="jpeg"  )

				{

					$src = imagecreatefromjpeg($_FILES[$images]['tmp_name'][$i]);

				}

				else if($extension=="png")

				{

					$src = imagecreatefrompng($_FILES[$images]['tmp_name'][$i]);

				}

				

				else

				{

					$src = imagecreatefromgif($_FILES[$images]['tmp_name'][$i]);

				}

				list($width,$height)=getimagesize($_FILES[$images]['tmp_name'][$i]);

				//This application developed by www.webinfopedia.com

				//Check the image is small that 124px

				if($gotwidth>=124)

				{

					//if bigger set it to 124

					$newwidth=124;

				}

				else

				{

					//if small let it be original

					$newwidth=$gotwidth;

				}

				

				//Find the new height

				$newheight=round(($gotheight*$newwidth)/$gotwidth);

				//Creating thumbnail

				$tmp=imagecreatetruecolor($newwidth,$newheight);

				imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight, $width,$height);

				//Create thumbnail image

				$createImageSave=imagejpeg($tmp,$img_thumb,100);

				if($createImageSave)

				{

					//upload the original file

					$uploadOrginal=move_uploaded_file($_FILES[$images]['tmp_name'][$i],$originalImage);

					if($flag=='edit')

					{

						$imageName[] = $photo_file;

					}

					else

					{

						if($i==0)

						{

							$imageName = $photo_file;

						}

						else

						{

							$imageName .= ','.$photo_file;

						}	

					}

				}				

			}

		}

		return $imageName;

	}

	

	function to_month($id)

	{

		/*January

		February

		March

		April

		May

		June

		July

		August

		September

		October

		November

		December*/

		

		if($id==1)

			return "January";

		else if($id==2)

			return "February";

		else if($id==3)

			return "March";

		else if($id==4)

			return "April";

		else if($id==5)

			return "May";			

		else if($id==6)

			return "June";

		else if($id==7)

			return "July";

		else if($id==8)

			return "August";

		else if($id==9)

			return "September";

		else if($id==10)

			return "October";

		else if($id==11)

			return "November";

		else if($id==12)

			return "December";			

		

	}

	

	

	

	// Fuction to Replace " ' with space

	function replace($givenstr)

	{

		$str = $givenstr;

		$chr = array("'");

		$finalstr = str_replace($chr, "\'", $str);

		return $finalstr;

	}

	function replace_date($date_to)

	{

		$str = $date_to;

		$chr = array("-","/");

		$date_to = str_replace($chr, "*", $str);

		$exp_date = explode("*",$date_to);

		$date_to = $exp_date[2]."/".$exp_date[1]."/".$exp_date[0];

		return($date_to);

	}

	function replace_date_dash($date_to)

	{

		$str = $date_to;

		$chr = array("-","/");

		$date_to = str_replace($chr, "*", $str);

		$exp_date = explode("*",$date_to);

		$date_to = $exp_date[2]."-".$exp_date[1]."-".$exp_date[0];

		return($date_to);

	}

	function replace_date1($date_to)

	{

		$str = $date_to;

		$chr = array("-","/");

		$date_to = str_replace($chr, "/", $str);

		$exp_date = explode("/",$date_to);

		return($exp_date);

	}

	

	function replace_comm($givenstr)

	{

		$str = $givenstr;

		$chr = array("'",",");

		$finalstr = str_replace($chr, "", $str);

		return $finalstr;

	}

	function replace_slash($givenstr)

	{

		$str = $givenstr;

		$chr = array("/");

		$finalstr = str_replace($chr, "_", $str);

		return strtolower($finalstr);

	}

	function add_slash($givenstr)

	{

		$str = $givenstr;

		$chr = array("_");

		$finalstr = str_replace($chr, "/", $str);

		return strtolower($finalstr);

	}

	function replace_safaricategory($givenstr)

	{

		$str = $givenstr;

		$chr = array("'"," ");

		$finalstr = str_replace($chr, "*", $str);

		return strtolower($finalstr);

	}

	function add_safaricategory($givenstr)

	{

		$str = $givenstr;

		$chr = array("*");

		$finalstr = str_replace($chr, " ", $str);

		return $finalstr;

	}

	

	

	function count_booking_id($id)

	{

		$s = substr($id,3,strlen($id)) + 1;

		$s = substr($id,0,3).$s;

		return $s;

	}

	/**

	 * @name calc_due_date()

	 * @created February 21, 2003

	 * @author J de Silva

	 * @modified July 30, 2004

	 * ------------------------------------------------------------------

	 */

	

	function upload_file($field,$path)

	{

		$uploadDir = $path."/";

		$uploadFile = $uploadDir.$_FILES[$field]['name'];

		

		$image_name = $_FILES[$field]['name'];

		

		

		if (move_uploaded_file($_FILES[$field]['tmp_name'], $uploadFile))

		{

			chmod("$uploadFile",0777);

			

		}

		else

			echo "<br>some problem";

		return $image_name;

	}

	function send_email($to,$message,$subject,$from,$from_email,$reply="",$reply_email="")

	{

		

		$headers = "From: \"".$from."\"<".$from_email.">\n";

		$headers .= "Reply-To: \"".$reply."\"<".$reply_email.">\n";

		$headers .= 'MIME-Version: 1.0' . "\n"; 

		$headers .= 'Content-Type: text/html; charset=iso-8859-1"'."\n\n"; 

		/*if( $_SERVER['DOCUMENT_ROOT']!="C:/Inetpub/wwwroot")

		{*/	

			mail($to, $subject, $message, $headers); 

		/*}*/

	}	

	function fill_combo($table_name,$control_name,$first_value,$display_value,$field_value,$condition,$print_query="",$selected_value="",$css_class="",$multiple=false,$event="",$default_add=false,$size="",$second_displayvalue="")

	{		

		if($second_displayvalue!="")

			$query= "select ".$display_value.",".$field_value.",".$second_displayvalue." from ".$table_name. "".$condition;

		else

			$query= "select ".$display_value.",".$field_value." from ".$table_name. "".$condition;

		if($print_query!="")

		{

			echo $query;

		}		

		$rs = mysql_query($query) or die("There some error in function :".mysql_error());		

		if($multiple!="" && $multiple!="false")

		{			

			echo "<select class='".$css_class."' name='".$control_name."[]' multiple='".$multiple."' size='".$size."'". $event.">";

		}

		else

		{						

			echo "<select class='".$css_class."' name='".$control_name."' ". $event.">";

		}

		if($first_value!="")

			echo "<option value=''>".$first_value."</option>";

		if($second_displayvalue!="")

		{

			while($row= mysql_fetch_object($rs))

			{

				echo $row->$field_value;

				if($row->$field_value==$selected_value)

					echo "<option selected value='".$row->$field_value."'>(".$row->$second_displayvalue.")".$row->$display_value."</option>";

				else

					echo "<option  value='".$row->$field_value."'>(".$row->$second_displayvalue.")".$row->$display_value."</option>";

			}

		}

		else

		{

			while($row= mysql_fetch_object($rs))

			{

				echo $row->$field_value;

				if($row->$field_value==$selected_value)

					echo "<option selected value='".$row->$field_value."'>".$row->$display_value."</option>";

				else

					echo "<option  value='".$row->$field_value."'>".$row->$display_value."</option>";

			}

		}

	}

	

	function randon_password()

	{

		return 	rand(1000,9999);

	}

	function send_sms($mobile_number,$message)

	{

		$request = ""; //initialize the request variable

		$param["user"] = "ebizpromo"; //this is the username of our TM4B account

		$param["password"] = "ebizpromo123"; //this is the password of our TM4B account

		$param["text"] = $message; //this is the message that we want to send

		$param["PhoneNumber"] = "91".$mobile_number; //these are the recipients of the message

		$param["sender"] = "ebizpromo";//this is our sender 

		foreach($param as $key=>$val) //traverse through each member of the param array

		{ 

		  $request.= $key."=".urlencode($val); //we have to urlencode the values

		  $request.= "&"; //append the ampersand (&) sign after each paramter/value pair

		}

		$request = substr($request, 0, strlen($request)-1); //remove the final ampersand sign from the request

		//First prepare the info that relates to the connection

		$host = "sms.globalbulksms.com";

		$script = "/sendsms.asp";

		$request_length = strlen($request);

		$method = "POST"; // must be POST if sending multiple messages

		if ($method == "GET") 

		{

		  $script .= "?$request";

		}

		

		//Now comes the header which we are going to post. 

		$header = "$method $script HTTP/1.1\r\n";

		$header .= "Host: $host\r\n";

		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";

		$header .= "Content-Length: $request_length\r\n";

		$header .= "Connection: close\r\n\r\n";

		$header .= "$request\r\n";

		

		//Now we open up the connection

		$socket = @fsockopen($host, 80, $errno, $errstr); 

		if ($socket) //if its open, then...

		{ 

		  fputs($socket, $header); // send the details over

		  while(!feof($socket))

		  {

			$output[] = fgets($socket); //get the results 

		  }

		  fclose($socket); 

		} 

		//print "<pre>";

		//print_r($output);

		//print "</pre>";

	}

	function create_fck_editor($object_name,$editor_name,$editor_path,$width,$height,$value)

	{

		$object_name = new FCKeditor($editor_name) ;

		$object_name->BasePath = $editor_path;				

		$object_name->Width  = $width ;

		$object_name->Height = $height ;

		$object_name->Value = $value;

		$object_name->Create() ;

	}

	

	 function latlong($address='',$key='')

	{

	 $address = $address; // Google HQ

        $prepAddr = str_replace(' ','+',$address);

        

		$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');

        $output= json_decode($geocode);

       $latitude = $output->results[0]->geometry->location->lat;

		 

        $longitude = $output->results[0]->geometry->location->lng;

		$latlongArr = array();

		$latlongArr['tm_lat'] = $latitude;

	    $latlongArr['tm_long'] = $longitude;

		return $latlongArr;

		 

	/*if (!is_string($address))die("All Addresses must be passed as a string");

	$_url = sprintf('http://maps.google.com/maps?output=js&q=%s',rawurlencode($address));

	$_result = false;

	if($_result = file_get_contents($_url)) {

	//if(strpos($_result,'errortips') > 1 || strpos($_result,'Did you mean:') !== false) return false;

	preg_match('!center:\s*{lat:\s*(-?\d+\.\d+),lng:\s*(-?\d+\.\d+)}!U', $_result, $_match);

	

	$latlongArr = array();

	$latlongArr['tm_lat'] = $_match[1];

	$latlongArr['tm_long'] = $_match[2];

	}

	 */

	}

	

	function binarytoimageSmall($binaryString, $username, $user_id=null,$flag=null)

	{

		$dirswfl = "uploadedImages/".$user_id;



		if(!is_dir($dirswfl))

		{

			$chmod = (is_dir($dirswfl) === true) ? 644 : 777;

			if (in_array(get_current_user(), array('apache', 'httpd', 'nobody', 'system', 'webdaemon', 'www', 'www-data')) === true)

			{

			$chmod += 22;

			}

			mkdir($dirswfl);

			chmod($dirswfl, octdec(intval($chmod)));

		}

		

		$target = "uploadedImages/".$user_id."/";

		$binaryString = base64_decode($binaryString);

		

		$binaryImage = imagecreatefromstring($binaryString);

		

		$uri = 'data://application/octet-stream;base64,' . base64_encode($binaryString);

		$info = getimagesize($uri);

		

		$width = $info[0];

		$height = $info[1];

	

		$maxwidth = 64;

		$maxheight = 64;

		if ($maxwidth < $width && $width >= $height) {

		  $desired_width = $maxwidth;

		  $desired_height = ($desired_width / $width) * $height;

		}

		elseif ($maxheight < $height && $height >= $width) {

		  $desired_height = $maxheight;

		  $desired_width = ($desired_height /$height) * $width;

		}

		else {

		  $desired_height = $height;

		  $desired_width = $width;

		}

		

		$binaryImage = imagecreatefromstring($binaryString);

		$new = imagecreatetruecolor($desired_width, $desired_height); 

		$x = imagesx($binaryImage);

		$y = imagesy($binaryImage);

		imagecopyresampled($new, $binaryImage, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);

		

		$cleanstring = strtolower(trim(preg_replace('#\W+#', '_', $username), '_'));

		$newNameImage = sha1($user_id."_".$username."_small").".png";



		$binaryImageg_thumb = $target.$newNameImage;



		$black = imagecolorallocatealpha($new, 0, 0, 0, 127);

		imagealphablending($new, false);

		imagecolortransparent($new, $black);

		$createImageSave=imagepng($new,$binaryImageg_thumb);



		return $newNameImage;

	}

	

	function binarytoimageMedium($binaryString, $username, $user_id=null,$flag=null)

	{

		/*$dirswfl = "uploadimage/".$user_id;

	

		if(!is_dir($dirswfl))

		{

			$chmod = (is_dir($dirswfl) === true) ? 644 : 777;

			if (in_array(get_current_user(), array('apache', 'httpd', 'nobody', 'system', 'webdaemon', 'www', 'www-data')) === true)

			{

			$chmod += 22;

			}

			mkdir($dirswfl);

			chmod($dirswfl, octdec(intval($chmod)));

		}*/

		

		$target = "uploadimage/";

		$binaryString = base64_decode($binaryString);



		$binaryImage = imagecreatefromstring($binaryString);

		

		$uri = 'data://application/octet-stream;base64,' . base64_encode($binaryString);

		$info = getimagesize($uri);

		

		$width = $info[0];

		$height = $info[1];

	

		$maxwidth = 100;

		$maxheight = 100;

		if ($maxwidth < $width && $width >= $height) {

		  $desired_width = $maxwidth;

		  $desired_height = ($desired_width / $width) * $height;

		}

		elseif ($maxheight < $height && $height >= $width) {

		  $desired_height = $maxheight;

		  $desired_width = ($desired_height /$height) * $width;

		}

		else {

		  $desired_height = $height;

		  $desired_width = $width;

		}



		$binaryImage = imagecreatefromstring($binaryString);

		$new = imagecreatetruecolor($desired_width, $desired_height); 

		$x = imagesx($binaryImage);

		$y = imagesy($binaryImage);

		imagecopyresampled($new, $binaryImage, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);

		

		//$cleanstring = strtolower(trim(preg_replace('#\W+#', '_', $username), '_'));

		$newNameImage =  $username.".png";

		

		 $binaryImageg_thumb = $target.$newNameImage;

		 

		$black = imagecolorallocatealpha($new, 0, 0, 0, 127);

		imagealphablending($new, false);

		imagecolortransparent($new, $black);

		$createImageSave=imagepng($new,$binaryImageg_thumb);

		return $newNameImage;

	}

	

	function binarytoimage($binaryString, $username, $user_id=null,$flag=null)

	{ 

		$dirswfl = "certyimage/";

	

		if(!is_dir($dirswfl))

		{

			$chmod = (is_dir($dirswfl) === true) ? 644 : 777;

			if (in_array(get_current_user(), array('apache', 'httpd', 'nobody', 'system', 'webdaemon', 'www', 'www-data')) === true)

			{

			$chmod += 22;

			}

			mkdir($dirswfl);

			chmod($dirswfl, octdec(intval($chmod)));

		}

		

		$target = "certyimage/";

		$binaryString = base64_decode($binaryString);



		$binaryImage = imagecreatefromstring($binaryString);

		

		$uri = 'data://application/octet-stream;base64,' . base64_encode($binaryString);

		$info = getimagesize($uri);

		

		$desired_height = $info[1];

		$desired_width = $info[0];



		$binaryImage = imagecreatefromstring($binaryString);

		$new = imagecreatetruecolor($desired_width, $desired_height); 

		$x = imagesx($binaryImage);

		$y = imagesy($binaryImage);

		imagecopyresampled($new, $binaryImage, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);

		

		$cleanstring = strtolower(trim(preg_replace('#\W+#', '_', $username), '_'));

		$newNameImage = sha1($user_id."_".$username).".png";



		$binaryImageg_thumb = $target.$newNameImage;

		$black = imagecolorallocatealpha($new, 0, 0, 0, 127);

		imagealphablending($new, false);

		imagecolortransparent($new, $black);

		$createImageSave=imagepng($new,$binaryImageg_thumb);

		return $newNameImage;

	}

	function imageUploadwithName($firstname, $images,$news_id=null,$flag=null)

	{

		$dirswfl = $_SERVER['DOCUMENT_ROOT']."core/dmtc/admin/newsUpload/".$news_id;



		if(!is_dir($dirswfl))

		{

		$chmod = (is_dir($dirswfl) === true) ? 644 : 777;

		if (in_array(get_current_user(), array('apache', 'httpd', 'nobody', 'system', 'webdaemon', 'www', 'www-data')) === true)

		{

		$chmod += 22;

		}

		

		mkdir($dirswfl);

		chmod($dirswfl, octdec(intval($chmod)));

		

		}



		$target = $_SERVER['DOCUMENT_ROOT']."core/dmtc/admin/newsUpload/".$news_id."/";

		$imageCount = count($_FILES[$images]['name']);

		

		$pieces = explode(".",$_FILES[$images]['name']);

		$countPieces = count($pieces);

		if($countPieces == 2)

		{

			$extension = strtolower($pieces[1]);

		}

		else

		{

			$extension = strtolower($pieces[$countPieces-1]);

		}

		$extension = "png";

		$cleanstring = strtolower(trim(preg_replace('#\W+#', '_', $firstname), '_'));

		$photo_file = $news_id.'_'.$cleanstring."_".sha1($news_id."_".$firstname).'.'.$extension;

		$originalImage=$target.$photo_file;



		if(in_array($extension , array('jpg','jpeg', 'gif', 'png', 'bmp','pdf')))

		{	

			//Thumbnail file name File

			$image_filePath = $_FILES[$images]['tmp_name'];

			$img_fileName = $news_id."_".$firstname.'_small'.".".$extension;

			$img_thumb = $target.$img_fileName;

			

			$img_fileName2 = $news_id."_".$firstname.".".$extension;

			$img_thumb2 = $target.$img_fileName2;

			$extension = strtolower($pieces[1]);

			

			$img_fileName3 = $news_id."_".$firstname.'_thumb'.".png";

			$img_thumb3 = $target.$img_fileName3;

			$extension = strtolower($pieces[1]);

			//Check the file format before upload

			

			//Find the height and width of the image

			list($gotwidth, $gotheight, $gottype, $gotattr)= getimagesize($image_filePath);

			//---------- To create thumbnail of image---------------

			if($extension=="jpg" || $extension=="jpeg" )

			{

				$src = imagecreatefromjpeg($_FILES[$images]['tmp_name']);

			}

			else if($extension=="png")

			{

				$src = imagecreatefrompng($_FILES[$images]['tmp_name']);

			}

			

			else

			{

				$src = imagecreatefromgif($_FILES[$images]['tmp_name']);

			}

			list($width,$height)=getimagesize($_FILES[$images]['tmp_name']);



			$height=$gotheight;

			$width=$gotwidth;

				

			$maxwidth = 64;

			$maxheight = 64;

			if ($maxwidth < $width && $width >= $height) {

			  $desired_width = $maxwidth;

			  $desired_height = ($desired_width / $width) * $height;

			}

			elseif ($maxheight < $height && $height >= $width) {

			  $desired_height = $maxheight;

			  $desired_width = ($desired_height /$height) * $width;

			}

			else {

			  $desired_height = $height;

			  $desired_width = $width;

			}

			$newwidth=$desired_width;

			$newheight=$desired_height;





			$maxwidth = $width;

			$maxheight = $height;

			if ($maxwidth < $width && $width >= $height) {

			  $desired_width = $maxwidth;

			  $desired_height = ($desired_width / $width) * $height;

			}

			elseif ($maxheight < $height && $height >= $width) {

			  $desired_height = $maxheight;

			  $desired_width = ($desired_height /$height) * $width;

			}

			else {

			  $desired_height = $height;

			  $desired_width = $width;

			}

			$newwidth2=$desired_width;

			$newheight2=$desired_height;





			$maxwidth = 300;

			$maxheight = 300;

			if ($maxwidth < $width && $width >= $height) {

			  $desired_width = $maxwidth;

			  $desired_height = ($desired_width / $width) * $height;

			}

			elseif ($maxheight < $height && $height >= $width) {

			  $desired_height = $maxheight;

			  $desired_width = ($desired_height /$height) * $width;

			}

			else {

			  $desired_height = $height;

			  $desired_width = $width;

			}

			$newwidth3=$desired_width;

			$newheight3=$desired_height;

		

			//Creating thumbnail

			$tmp=imagecreatetruecolor($newwidth,$newheight);

			$tmp2=imagecreatetruecolor($newwidth2,$newheight2);

			$tmp3=imagecreatetruecolor($newwidth3,$newheight3);

			

			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight, $width,$height);

			imagecopyresampled($tmp2,$src,0,0,0,0,$newwidth2,$newheight2, $width,$height);

			imagecopyresampled($tmp3,$src,0,0,0,0,$newwidth3,$newheight3, $width,$height);

			

			//Create thumbnail image

			$createImageSave=imagepng($tmp,$img_thumb);

			$createImageSave2=imagepng($tmp2,$img_thumb2);

			$createImageSave3=imagepng($tmp3,$img_thumb3);

			

			if($createImageSave)

			{

				//upload the original file

				//$uploadOrginal=move_uploaded_file($_FILES[$images]['tmp_name'],$originalImage);

				//move_uploaded_file($_FILES[$images]['tmp_name'],$originalImage)

				if(1==1)

				{

					if($flag=='edit')

					{

						$imageName[] = $photo_file;

					}

					else

					{

						if($i==0)

						{

							$imageName = $photo_file;

						}

						else

						{

							$imageName .= ','.$photo_file;

						}	

					}

					return array($img_fileName, $img_fileName2 ,$img_fileName3);

				}

				else

				{

					return "error";	

				}

			}				

		}	

	}

	function mp4VideoUpload($firstname, $images,$news_id=null,$flag=null)

	{

		$dirswfl = $_SERVER['DOCUMENT_ROOT']."core/dmtc/admin/newsUpload/".$news_id;



		if(!is_dir($dirswfl))

		{

		$chmod = (is_dir($dirswfl) === true) ? 644 : 777;

		if (in_array(get_current_user(), array('apache', 'httpd', 'nobody', 'system', 'webdaemon', 'www', 'www-data')) === true)

		{

		$chmod += 22;

		}

		

		mkdir($dirswfl);

		chmod($dirswfl, octdec(intval($chmod)));

		

		}



		$target = $_SERVER['DOCUMENT_ROOT']."core/dmtc/admin/newsUpload/".$news_id."/";

		$imageCount = count($_FILES[$images]['name']);

		

		$pieces = explode(".",$_FILES[$images]['name']);

		$countPieces = count($pieces);

		if($countPieces == 2)

		{

			$extension = strtolower($pieces[1]);

		}

		else

		{

			$extension = strtolower($pieces[$countPieces-1]);

		}

		$extension = "mp4";

		$cleanstring = strtolower(trim(preg_replace('#\W+#', '_', $firstname), '_'));

		$photo_file = $news_id.".".$extension;

		$originalImage=$target.$photo_file;

		

		if(in_array($extension , array('mp4')))

		{

			move_uploaded_file($_FILES[$images]['tmp_name'], $originalImage);

		}

		return $photo_file;

	}

	

	function base64_to_video( $base64_string, $output_file ) {

		$ifp = fopen( $output_file, "wb" ); 

		stream_filter_append($ifp, "convert.base64-decode");

		fwrite( $ifp, $base64_string );

		fclose( $ifp ); 

		return( $output_file ); 

	}

	function newsBinarytoimageSmall($binaryString, $username, $user_id=null,$flag=null)

	{

		$dirswfl = "newsUpload/".$user_id;



		if(!is_dir($dirswfl))

		{

			$chmod = (is_dir($dirswfl) === true) ? 644 : 777;

			if (in_array(get_current_user(), array('apache', 'httpd', 'nobody', 'system', 'webdaemon', 'www', 'www-data')) === true)

			{

			$chmod += 22;

			}

			mkdir($dirswfl);

			chmod($dirswfl, octdec(intval($chmod)));

		}

		

		$target = "newsUpload/".$user_id."/";

		$binaryString = base64_decode($binaryString);

		

		$binaryImage = imagecreatefromstring($binaryString);

		

		$uri = 'data://application/octet-stream;base64,' . base64_encode($binaryString);

		$info = getimagesize($uri);

		

		$width = $info[0];

		$height = $info[1];

	

		$maxwidth = 64;

		$maxheight = 64;

		if ($maxwidth < $width && $width >= $height) {

		  $desired_width = $maxwidth;

		  $desired_height = ($desired_width / $width) * $height;

		}

		elseif ($maxheight < $height && $height >= $width) {

		  $desired_height = $maxheight;

		  $desired_width = ($desired_height /$height) * $width;

		}

		else {

		  $desired_height = $height;

		  $desired_width = $width;

		}

		

		$binaryImage = imagecreatefromstring($binaryString);

		$new = imagecreatetruecolor($desired_width, $desired_height); 

		$x = imagesx($binaryImage);

		$y = imagesy($binaryImage);

		imagecopyresampled($new, $binaryImage, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);

		

		$cleanstring = strtolower(trim(preg_replace('#\W+#', '_', $username), '_'));

		$newNameImage = $user_id."_small".".png";



		$binaryImageg_thumb = $target.$newNameImage;



		$black = imagecolorallocatealpha($new, 0, 0, 0, 127);

		imagealphablending($new, false);

		imagecolortransparent($new, $black);

		$createImageSave=imagepng($new,$binaryImageg_thumb);



		return $newNameImage;

	}

	

	function newsBinarytoimageMedium($binaryString, $username, $user_id=null,$flag=null)

	{

		$dirswfl = "newsUpload/".$user_id;

	

		if(!is_dir($dirswfl))

		{

			$chmod = (is_dir($dirswfl) === true) ? 644 : 777;

			if (in_array(get_current_user(), array('apache', 'httpd', 'nobody', 'system', 'webdaemon', 'www', 'www-data')) === true)

			{

			$chmod += 22;

			}

			mkdir($dirswfl);

			chmod($dirswfl, octdec(intval($chmod)));

		}

		

		$target = "newsUpload/".$user_id."/";

		$binaryString = base64_decode($binaryString);



		$binaryImage = imagecreatefromstring($binaryString);

		

		$uri = 'data://application/octet-stream;base64,' . base64_encode($binaryString);

		$info = getimagesize($uri);

		

		$width = $info[0];

		$height = $info[1];

	

		$maxwidth = 300;

		$maxheight = 300;

		if ($maxwidth < $width && $width >= $height) {

		  $desired_width = $maxwidth;

		  $desired_height = ($desired_width / $width) * $height;

		}

		elseif ($maxheight < $height && $height >= $width) {

		  $desired_height = $maxheight;

		  $desired_width = ($desired_height /$height) * $width;

		}

		else {

		  $desired_height = $height;

		  $desired_width = $width;

		}



		$binaryImage = imagecreatefromstring($binaryString);

		$new = imagecreatetruecolor($desired_width, $desired_height); 

		$x = imagesx($binaryImage);

		$y = imagesy($binaryImage);

		imagecopyresampled($new, $binaryImage, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);

		

		$cleanstring = strtolower(trim(preg_replace('#\W+#', '_', $username), '_'));

		$newNameImage = $user_id."_thumb".".png";

		

		$binaryImageg_thumb = $target.$newNameImage;

		

		$black = imagecolorallocatealpha($new, 0, 0, 0, 127);

		imagealphablending($new, false);

		imagecolortransparent($new, $black);

		$createImageSave=imagepng($new,$binaryImageg_thumb);

		return $newNameImage;

	}

	

	function newsBinarytoimage($binaryString, $username, $user_id=null,$flag=null)

	{

		$dirswfl = "newsUpload/".$user_id;

	

		if(!is_dir($dirswfl))

		{

			$chmod = (is_dir($dirswfl) === true) ? 644 : 777;

			if (in_array(get_current_user(), array('apache', 'httpd', 'nobody', 'system', 'webdaemon', 'www', 'www-data')) === true)

			{

			$chmod += 22;

			}

			mkdir($dirswfl);

			chmod($dirswfl, octdec(intval($chmod)));

		}

		

		$target = "newsUpload/".$user_id."/";

		$binaryString = base64_decode($binaryString);



		$binaryImage = imagecreatefromstring($binaryString);

		

		$uri = 'data://application/octet-stream;base64,' . base64_encode($binaryString);

		$info = getimagesize($uri);

		

		$desired_height = $info[1];

		$desired_width = $info[0];



		$binaryImage = imagecreatefromstring($binaryString);

		$new = imagecreatetruecolor($desired_width, $desired_height); 

		$x = imagesx($binaryImage);

		$y = imagesy($binaryImage);

		imagecopyresampled($new, $binaryImage, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);

		

		$cleanstring = strtolower(trim(preg_replace('#\W+#', '_', $username), '_'));

		$newNameImage = $user_id.".png";



		$binaryImageg_thumb = $target.$newNameImage;

		$black = imagecolorallocatealpha($new, 0, 0, 0, 127);

		imagealphablending($new, false);

		imagecolortransparent($new, $black);

		$createImageSave=imagepng($new,$binaryImageg_thumb);

		return $newNameImage;

	}



?>