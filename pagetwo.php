<?php

$no_of_storages = 30;

$GLOBALS["storage"] = array();
for($i = 0; $i < $no_of_storages; $i++){
	$GLOBALS["storage"][] = array("empty","no_date", "no_info");
}

load_data();

refresh();

if(isset($_GET["collect"])){
	collect($_GET["collect"]);
}


function collect($house_no){
	
     check_return($house_no)."<br>";
    
	$is_empty = $GLOBALS["storage"][$house_no][0] !== "uncollected";
	if(!$is_empty){
    	$GLOBALS["storage"][$house_no][0] = "collected";
    	$GLOBALS["storage"][$house_no][1] = date("Y-m-d h:i:sa");
        save_data();
		return "collected successfully";
    }else{
    	return "the slot is empty";
    }
}


function display(){
	$string = "<table style='border: 1px solid black'>";
	$string =  $string .
	"<tr>
		<th>House No.</th>
		<th>Order No.</th>
		<th>Seller's Name</th>
		<th>Seller's Address</th>
		<th>Seller's Phone No.</th>
		<th>Buyer's Name</th>
		<th>Buyer's Address</th>
		<th>Buyer's Phone No.</th>
		<th>Status</th>
		<th>Date</th>
	</tr>";
	
  for($i = 0; $i < sizeof($GLOBALS["storage"]); $i++){
	  
	  if($GLOBALS["storage"][$i][2] !== "no_info"){
		$base_date = json_decode($GLOBALS["storage"][$i][2]);
	
		$string = $string . " <tr>";
		  
		$string = $string. "<td>".($i)."</td>";
		$string = $string. "<td>".$base_date->orderno."</td>";
		$string = $string. "<td>".$base_date->sellername."</td>";
		$string = $string. "<td>".$base_date->selleraddress."</td>";
		$string = $string. "<td>".$base_date->sellerno."</td>";
		$string = $string. "<td>".$base_date->buyername."</td>";
		$string = $string. "<td>".$base_date->buyeraddress."</td>";
		$string = $string. "<td>".$base_date->buyerno."</td>";
		$string = $string. "<td>".$GLOBALS["storage"][$i][0].($GLOBALS["storage"][$i][0] == "uncollected"? "<Br><a href='pagetwo.php?collect=".$i."'>Collect</a>":"")."</td>";
		$string = $string. "<td>".$GLOBALS["storage"][$i][1]."</td>";
		
		$string = $string . " </tr>";
	  }
	 
  }
  return $string;
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
	}
		}else{
			save_data();
		}
	
	
	
}

echo display();

?>

<style>
table, th, td {
  border: 1px solid black;
  text-align: center;
}

</style>