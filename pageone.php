<?php
// define variables and set to empty values
$sellernameErr = $selleraddressErr = $sellernoErr = $buyernameErr = $buyeraddressErr = $buyernoErr = $ordernoErr = $buyerhousenoErr  =  "";
$sellername = $selleraddress = $sellerno = $buyername = $buyeraddress = $buyerno = $orderno = $buyerhouseno = "";

$GLOBALS["data"] = array();


//define
$no_of_storages = 30;

$GLOBALS["storage"] = array();
for($i = 0; $i < $no_of_storages; $i++){
	$GLOBALS["storage"][] = array("empty","no_date", "no_info");
}

load_data();


	$isErr = false;
	
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
  if (empty($_POST["sellername"])) {
    $sellernameErr = "Name is required";
	  $isErr = true;
  } else {
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z-' ]*$/",$sellername)) {
      $sellernameErr = "Only letters and white space allowed";
	    $isErr = true;
	 
    }else{
      $sellername = test_input($_POST["sellername"]);
      $GLOBALS["data"]["sellername"] = $sellername;
    }
	
   
  }

  if (empty($_POST["selleraddress"])) {
    $selleraddressErr = "Address is required";
	$isErr = true;
  } else {
    $selleraddress = test_input($_POST["selleraddress"]);
	$GLOBALS["data"]["selleraddress"] = $selleraddress;
  }

    if (empty($_POST["sellerno"])) {
      $sellernoErr = "Phone no. is required";
	$isErr = true;
    } else {
      $sellerno = test_input($_POST["sellerno"]);
		$GLOBALS["data"]["sellerno"] = $sellerno;
    }

  if (empty($_POST["buyername"])) {
    $buyernameErr = "Name is required";
	$isErr = true;
  } else {
    $buyername = test_input($_POST["buyername"]);
	$GLOBALS["data"]["buyername"] = $buyername;
  }

  if (empty($_POST["buyeraddress"])) {
    $buyeraddressErr = "Address is required";
	$isErr = true;
  } else {
    $buyeraddress = test_input($_POST["buyeraddress"]);
	$GLOBALS["data"]["buyeraddress"] = $buyeraddress;
  }
  
  if (empty($_POST["buyerhouseno"])) {
    $buyerhousenoErr = "House no. is required";
	$isErr = true;
  } else {
    $buyerhouseno = test_input($_POST["buyerhouseno"]);
	if( is_numeric($buyerhouseno) && $buyerhouseno < 30 && $buyerhouseno >= 0){
		$GLOBALS["data"]["buyerhouseno"] = $buyerhouseno;
	}else{
		$isErr = true;
		$buyerhousenoErr = "House no. is not availble ( select from 0 to 29 )";
	}
	
  }

  if (empty($_POST["buyerno"])) {
    $buyernoErr = "Phone no. is required";
	$isErr = true;
  } else {
    $buyerno = test_input($_POST["buyerno"]);
	$GLOBALS["data"]["buyerno"] = $buyerno;
  }

  if (empty($_POST["orderno"])) {
    $ordernoErr = "Order no. is required";
	$isErr = true;
  } else {
    $orderno = test_input($_POST["orderno"]);
	$GLOBALS["data"]["orderno"] = $orderno;
  }
  
  
}else{
	$isErr = true;
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}



//data functions

function store($house_no){

	$is_empty = $GLOBALS["storage"][$house_no][0] !== "uncollected";
	if(get_filled_slots() < 5 && $is_empty){
    	$GLOBALS["storage"][$house_no][0] = "uncollected";
    	$GLOBALS["storage"][$house_no][1] = date("Y-m-d h:i:sa");
    	$GLOBALS["storage"][$house_no][2] = json_encode($GLOBALS["data"]);
		
        save_data();
		return "stored successfully";
    }else{
    	return "the slot is not empty";
    }
	
}





function get_filled_slots(){
$count = 0;
for($i = 0; $i < sizeof($GLOBALS["storage"]); $i++){
  	
    if ($GLOBALS["storage"][$i][0] === "uncollected"){
    	$count++;
    };
  }
  return $count;
}

function refresh(){
	for($i = 0; $i < sizeof($GLOBALS["storage"]); $i++){
		check_return($i);
  	}
}

function check_return($house_no){
	if($GLOBALS["storage"][$house_no][1] !== "no_date"){
    	$now = date("Y-m-d h:i:sa"); //
		$now_date = strtotime(date("Y-m-d h:i:sa")); 
		// echo $now;
		// echo $now_date;
        $slot_date = strtotime($GLOBALS["storage"][$house_no][1]);
        $datediff = $now_date - $slot_date;

		$days = round($datediff / (60 * 60 * 24));
        
        if($days > 2){
          $GLOBALS["storage"][$house_no][0] = "returned";
          $GLOBALS["storage"][$house_no][1] = date("Y-m-d h:i:sa");
			return "it was returned";
        }else{
          return "not returned";
        }
    }else{
    	return "no date";
    }
}

function save_data(){
	$file = fopen("data", "w") or die("Unable to open file!");
	$data = json_encode($GLOBALS["storage"]);
	fwrite($file, $data);
	fclose($file);
}

function load_data(){

	if(file_exists('data')){
			$data = file_get_contents('data');
			if($data != "" ){
		    $GLOBALS["storage"] = json_decode($data);
	    }else{
        save_data();
      }
		}else{
			save_data();
		}
	
	
	
}


?>

<!DOCTYPE HTML>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>



<center><h2>Welcome to Management System for Parcel</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="pageone.php">
  Seller's Name: <input type="text" name="sellername" value="<?php echo $sellername;?>">
  <span class="error">* <?php echo $sellernameErr;?></span>
  <br><br>
  Seller's Address: <input type="text" name="selleraddress" value="<?php echo $selleraddress;?>">
  <span class="error">* <?php echo $selleraddressErr;?></span>
  <br><br>
  Seller's Phone No.: <input type="text" name="sellerno" value="<?php echo $sellerno;?>">
  <span class="error">* <?php echo $sellernoErr;?></span>
  <br><br>
  Buyer's Name: <input type="text" name="buyername" value="<?php echo $buyername;?>">
  <span class="error">* <?php echo $buyernameErr;?></span>
  <br><br>
  Buyer's Address: <input type="text" name="buyeraddress" value="<?php echo $buyeraddress;?>">
  <span class="error">* <?php echo $buyeraddressErr;?></span>
  <br><br>
  Buyer's House No.: <input type="text" name="buyerhouseno" value="<?php echo $buyerhouseno;?>">
  <span class="error">* <?php echo $buyerhousenoErr;?></span>
  <br><br>
  Buyer's Phone No.: <input type="text" name="buyerno" value="<?php echo $buyerno;?>">
  <span class="error">* <?php echo $buyernoErr;?></span>
  <br><br>
  Order No. <input type="text" name="orderno" value="<?php echo $orderno;?>">
  <span class="error">* <?php echo $ordernoErr;?></span>
  <br><br>

  <input type="submit" name="submit" value="Submit">
  <a href='pagetwo.php'>View Tables</a>
</form>

<?php
echo "<h2>Shipping Informations:</h2>";
echo $sellername;
echo "<br>";
echo $selleraddress;
echo "<br>";
echo $sellerno;
echo "<br>";
echo $buyername;
echo "<br>";
echo $buyeraddress;
echo "<br>";
echo $buyerhouseno;
echo "<br>";
echo $buyerno;
echo "<br>";
echo $orderno;


if(!$isErr){
echo "<br>";
echo "<br>";
	echo "<p style='color: blue;'>".store($buyerhouseno)."</p>";
}


?>




</body>
</html>
