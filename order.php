<?php 
class Order {
	public  $u_suc_orders;
	/*public  $sucs_orders = array();
	public  $dup_orders = array();*/
	private $action;
	private $recipient_name;
	private $recipient_email;
	private $recipient_type;
	private $recipient_mobile;
	private $recipient_thana;
	private $recipient_district;
	private $price;
	private $weight;
	private $payment_method;
	private $order_id;
	private $recipient_address;
	private $pick_address;
	private $emi_detail;
	private $freebee_detail;
	private $products_description;
	private $setComments	;
	private $tableName ='tbl_order_details';
	private $dbConn;

	function setAction($action) { $this->action = $action; }
	function getAction() { return $this->action; }
	function setR_Name($recipient_name) { $this->recipient_name = $recipient_name; }
	function getR_Name() { return $this->recipient_name; }
	function setR_Email($recipient_email) { $this->recipient_email = $recipient_email; }
	function getR_Email() { return $this->recipient_email; }
	function setR_type($recipient_type) { $this->recipient_type = $recipient_type; }
	function getR_type() { return $this->recipient_type; }
	function setR_Mobile($recipient_mobile) { $this->recipient_mobile = $recipient_mobile; }
	function getR_Mobile() { return $this->recipient_mobile; }
	function setR_Thana($recipient_thana) { $this->recipient_thana = $recipient_thana; }
	function getR_Thana() { return $this->recipient_thana; }
	function setR_District($recipient_district) { $this->recipient_district = $recipient_district; }
	function getR_District() { return $this->recipient_district; }
	function setPrice($price) { $this->price = $price; }
	function getPrice() { return $this->price; }
	function setWeight($weight) { $this->weight = $weight; }
	function getWeight() { return $this->weight; }

	function setPayment_method($payment_method) { $this->payment_method = $payment_method; }
	function getPayment_method() { return $this->payment_method; }
	function setOrderID($order_id) { $this->order_id = $order_id; }
	function getOrderID() { return $this->order_id; }
	function setR_Address($recipient_address) { $this->recipient_address = $recipient_address; }
	function getR_Address() { return $this->recipient_address; }
	function setP_Address($pick_address) { $this->pick_address = $pick_address; }
	function getP_Address() { return $this->pick_address; }
	function setEmi_Detail($emi_detail) { $this->emi_detail = $emi_detail; }
	function getEmi_Detail() { return $this->emi_detail; }
	function setFreebee_Detail($freebee_detail) { $this->freebee_detail = $freebee_detail; }
	function getFreebee_Detail() { return $this->freebee_detail; }
	function setP_description($products_description) { $this->products_description = $products_description; }
	function getP_description() { return $this->products_description; }
	function setComments($comments) { $this->comments = $comments; }
	function getComments() { return $this->comments; }


	public function __construct() {
		$db = new DbConnect();
		$this->dbConn = $db->connect();
		
	}

	

	public function insert() {

			 //Order date
		$timeSQL = $this->dbConn->query("SELECT NOW() + INTERVAL 6 HOUR as currenttime");
		$timeResult = $timeSQL->fetch();
		$orderDate = date("Y-m-d", strtotime($timeResult['currenttime']));

            //Order ID
            //selecting max auto id
		$maxemordid =$this->dbConn->query("SELECT max(ordSeq) as ordSeq from tbl_order_details where orderDate='$orderDate'");
                        //$maxresult = mysqli_query($conn, $maxemordid);
		foreach ($maxemordid as $maxrow){
			$orderid = $maxrow['ordSeq']+1;
			$ordSeq = $maxrow['ordSeq']+1; 
		}
		switch (strlen($orderid)){
			case 1: $orderid = "000".$orderid;
			break;
			case 2: $orderid = "00".$orderid;
			break;
			case 3: $orderid = "0".$orderid;
			break;
			
		}  
		$barcode = date("dmy", strtotime($orderDate)).$orderid."0";
		
		
		try{
          /*     //District
			$districtsql = $this->dbConn->query("SELECT districtId, districtName from tbl_district_info where districtName = '$this->recipient_district'");
			$district = $districtsql->fetch();
			$customerDistrict = $district['districtId'];
              //echo $customerDistrict;
			

              //Thana
			$thanasql = $this->dbConn->query("SELECT thanaId, thanaName from tbl_thana_info where thanaName = '$this->recipient_thana'");
			$thana = $thanasql->fetch();
			$customerThana = $thana['thanaId'];*/
            //echo $customerThana;


			
			if ($this->pick_address != ""){
             //Point
				$pickuppoint = $this->dbConn->query("Select pointCode from tbl_merchant_info where merchantCode = 'M-1-0484'");
		
				
				foreach ($pickuppoint as $pointrow){
					$merchantPointCode = $pointrow['pointCode'];
            //echo $merchantPointCode;
				}

				$pickupSystemSQL = $this->dbConn->query("SELECT pointCode, pickPointCode from tbl_regular_point where pointCode = '$merchantPointCode'");
				$pickupSystemRow = $pickupSystemSQL->fetch();
				$pickuppointcode = $pickupSystemRow['pickPointCode'];
             //echo $pickuppointcode;

				$droppoint = $this->dbConn->query("SELECT pointCode from tbl_point_mapping
				 where district ='$this->recipient_district' AND thana='$this->recipient_thana' AND merchantCode='M-1-0484'");
				if ($droppoint->rowCount() > 0) {
					foreach ($droppoint as $droprow){
						$droppointcode = $droprow['pointCode'];
                  //echo $droppointcode;
					}
					$orderid = date("dmy", strtotime($orderDate))."-".$orderid."-".$pickuppointcode."-".$droppointcode;

               //Rate Chart
					$merRateIdSQL = $this->dbConn->query("SELECT ratechartId, cod from tbl_merchant_info where merchantCode ='M-1-0484'");
					$merRateIdRow = $merRateIdSQL->fetch();
					$merRateChartId = $merRateIdRow['ratechartId'];
					$mercod = $merRateIdRow['cod']; 
					
             //I am taking charge and destination null;


					$sql = 'INSERT INTO ' . $this->tableName . '(action,
					custname,
					recipient_email,
					recipient_type,
					custphone,
					recipient_thana,
					recipient_district,
					packagePrice,
					productSizeWeight,
					payment_method,
					merOrderRef,
					custaddress,
					pickMerchantAddress,
					emi_detail,
					freebee_detail,
					productBrief,
					comments,ratechartId,destination,customerThana,customerDistrict,merchantCode,cod,creation_date, created_by,orderDate,orderid,ordSeq,pickPointCode,dropPointCode,barcode) VALUES(:action,
					:recipient_name,
					:recipient_email,
					:recipient_type,
					:recipient_mobile,
					:recipient_thana,
					:recipient_district,
					:price,
					:weight,
					:payment_method,
					:order_id,
					:recipient_address,
					:pick_address,
					:emi_detail,
					:freebee_detail,
					:products_description,
					:comments,:ratechartId,:destination,:customerThana,:customerDistrict,:merchantcode,:cod,:creation_date,:created_by,:orderDate,:orderid,:ordSeq,:pickPointCode,:dropPointCode,:barcode)';

					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindParam(':action', $this->action);
					$stmt->bindParam(':recipient_name', $this->recipient_name);
					$stmt->bindParam(':recipient_email', $this->recipient_email);
					$stmt->bindParam(':recipient_type', $this->recipient_type);
					$stmt->bindParam(':recipient_mobile', $this->recipient_mobile);
					$stmt->bindParam(':recipient_thana', $this->recipient_thana);

					$stmt->bindParam(':recipient_district', $this->recipient_district);
					$stmt->bindParam(':price', $this->price);
					$stmt->bindParam(':weight', $this->weight);
					$stmt->bindParam(':payment_method', $this->payment_method);
					$stmt->bindParam(':order_id', $this->order_id);
					$stmt->bindParam(':recipient_address', $this->recipient_address);

					$stmt->bindParam(':pick_address', $this->pick_address);
					$stmt->bindParam(':emi_detail', $this->emi_detail);
					$stmt->bindParam(':freebee_detail', $this->freebee_detail);
					$stmt->bindParam(':products_description', $this->products_description);
					$stmt->bindParam(':comments', $this->comments);

					/*$ratechartId = 4;*/
					$destination = 'demo';
			/*$customerThana = 5;
			$customerDistrict = 6;*/
			$merchantcode = 'M-1-0484';
			/*$orderDate = '2013-03-15';*/
			$mer = 'Robi';
			
			$stmt->bindParam(':ratechartId', $merRateChartId);
			$stmt->bindParam(':destination', $destination);
			$stmt->bindParam(':customerThana', $this->recipient_thana);
			$stmt->bindParam(':customerDistrict', $this->recipient_district);


			$stmt->bindParam(':merchantcode', $merchantcode);
			$stmt->bindParam(':orderDate', $orderDate);
			$stmt->bindParam(':orderid', $orderid);
			$stmt->bindParam(':ordSeq', $ordSeq);
			$stmt->bindParam(':pickPointCode', $pickuppointcode);
			$stmt->bindParam(':dropPointCode', $droppointcode);
			$stmt->bindParam(':cod', $mercod);
			$stmt->bindParam(':creation_date', $orderDate);
			$stmt->bindParam(':created_by', $mer);
			$stmt->bindParam(':barcode', $barcode);
			
			
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		/*$this->sucs_orders[] = $this->order_id;*/
		}else {
			http_response_code(400);
			header("content-type: application/json");
			$dup = "";
			$duplicates = (bool)$dup;
			$suc = "";
			$success = (bool)$suc;

            $arrytest = array_merge(array('error'=>array('code'=>400, 'message' =>"Customer thana not selected")),array('duplicates'=>$duplicates),array('success'=>$success));
            $response = json_encode($arrytest);
            echo $response; exit;
		}

	}else{

		$pickuppoint = $this->dbConn->query("Select pointCode from tbl_merchant_info where merchantCode = 'M-1-0484'");
		
		foreach ($pickuppoint as $pointrow){
			$merchantPointCode = $pointrow['pointCode'];
		}
		$pickupSystemSQL = $this->dbConn->query("SELECT pointCode, pickPointCode from tbl_regular_point where pointCode = '$merchantPointCode'");
		$pickupSystemRow = $pickupSystemSQL->fetch();
		
		$pickuppointcode = $pickupSystemRow['pickPointCode'];
		//echo $pickuppointcode;
                            // identify drop point
		$droppoint = $this->dbConn->query("SELECT pointCode from tbl_point_mapping
				 where district ='$this->recipient_district' AND thana='$this->recipient_thana' AND merchantCode='M-1-0484'");
		
		if ($droppoint->rowCount() > 0) {
			foreach ($droppoint as $droprow){
				$droppointcode = $droprow['pointCode'];

			}

			$orderid = date("dmy", strtotime($orderDate))."-".$orderid."-".$pickuppointcode."-".$droppointcode;

			$merRateIdSQL = $this->dbConn->query("select districtid, ratechartId, cod from tbl_merchant_info where merchantCode ='M-1-0484'");
			$merRateIdRow = $merRateIdSQL->fetch();
			$merRateChartId = $merRateIdRow['ratechartId'];
			$mercod = $merRateIdRow['cod']; 
			$merdistrictid = $merRateIdRow['districtid'];

                                  //I am taking charge and destination null;


			$sql = 'INSERT INTO ' . $this->tableName . '(action,
			custname,
			recipient_email,
			recipient_type,
			custphone,
			recipient_thana,
			recipient_district,
			packagePrice,
			productSizeWeight,
			payment_method,
			merOrderRef,
			custaddress,
			pickMerchantAddress,
			emi_detail,
			freebee_detail,
			productBrief,
			comments,ratechartId,destination,customerThana,customerDistrict,cod,creation_date, created_by,orderDate,orderid,ordSeq,pickPointCode,dropPointCode,merchantCode,barcode) VALUES(:action,
			:recipient_name,
			:recipient_email,
			:recipient_type,
			:recipient_mobile,
			:recipient_thana,
			:recipient_district,
			:price,
			:weight,
			:payment_method,
			:order_id,
			:recipient_address,
			:pick_address,
			:emi_detail,
			:freebee_detail,
			:products_description,
			:comments,:ratechartId,:destination,:customerThana,:customerDistrict,:cod,:creation_date,:created_by,:orderDate,:orderid,:ordSeq,:pickPointCode,:dropPointCode,:mc,:barcode)';

			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindParam(':action', $this->action);
			$stmt->bindParam(':recipient_name', $this->recipient_name);
			$stmt->bindParam(':recipient_email', $this->recipient_email);
			$stmt->bindParam(':recipient_type', $this->recipient_type);
			$stmt->bindParam(':recipient_mobile', $this->recipient_mobile);
			$stmt->bindParam(':recipient_thana', $this->recipient_thana);

			$stmt->bindParam(':recipient_district', $this->recipient_district);
			$stmt->bindParam(':price', $this->price);
			$stmt->bindParam(':weight', $this->weight);
			$stmt->bindParam(':payment_method', $this->payment_method);
			$stmt->bindParam(':order_id', $this->order_id);
			$stmt->bindParam(':recipient_address', $this->recipient_address);

			$stmt->bindParam(':pick_address', $this->pick_address);
			$stmt->bindParam(':emi_detail', $this->emi_detail);
			$stmt->bindParam(':freebee_detail', $this->freebee_detail);
			$stmt->bindParam(':products_description', $this->products_description);
			$stmt->bindParam(':comments', $this->comments);

			/*$ratechartId = 4;*/
			$destination = 'demo';
			/*$customerThana = 5;
			$customerDistrict = 6;*/
			$merchatcode = 'demo';
			/*$orderDate = '2013-03-15';*/
			$merchantcode = 'M-1-0484';
			$mer = 'Robi';
			
			$stmt->bindParam(':ratechartId', $merRateChartId);
			$stmt->bindParam(':destination', $destination);
			$stmt->bindParam(':customerThana', $this->recipient_thana);
			$stmt->bindParam(':customerDistrict', $this->recipient_district);
			$stmt->bindParam(':mc', $merchantcode);
			

			$stmt->bindParam(':orderDate', $orderDate);
			$stmt->bindParam(':orderid', $orderid);
			$stmt->bindParam(':ordSeq', $ordSeq);
			$stmt->bindParam(':pickPointCode', $pickuppointcode);
			$stmt->bindParam(':dropPointCode', $droppointcode);
			$stmt->bindParam(':cod', $mercod);
			$stmt->bindParam(':creation_date', $orderDate);
			$stmt->bindParam(':created_by', $mer);
			$stmt->bindParam(':barcode', $barcode);
			
			if($stmt->execute()) {
				
				return true;
			} else {

				return false;
			}
			
			
		}
		else
		{
			http_response_code(400);
			header("content-type: application/json");
			$dup = "";
			$duplicates = (bool)$dup;
			$suc = "";
			$success = (bool)$suc;

            $arrytest = array_merge(array('error'=>array('code'=>400, 'message' =>"Customer thana not selected")),array('duplicates'=>$duplicates),array('success'=>$success));
            $response = json_encode($arrytest);
            echo $response; exit;
		}
      	
    
	} 
    

}catch(Exception $e)
{           //print_r($this->dbConn->errorInfo());
	        $dorder = $this->dbConn->query("SELECT * from tbl_order_details where merOrderRef = '$this->order_id'");
	       
	        if($dorder->rowCount()>0)
	        {
	        	$dorders = $dorder->fetch();
	        	http_response_code(409);
	        	header("content-type: application/json");
	        	$err = "";
	        	$error = (bool)$err;
	        	$suc="";
	        	$success = (bool) $suc;
                $arrytest = array_merge(array('error'=>$error),array('duplicate'=>array('code'=>409, 'message' =>"duplicate order",'tracking_id'=>$dorders['orderid'],'order_id'=>$dorders['merOrderRef'])),array('success'=>$success));
                $response = json_encode($arrytest);
                echo $response; exit;
	        }
	        else{
	        	http_response_code(400);
	        header("content-type: application/json");
	        $dup = "";
			$duplicates = (bool)$dup;
			$suc = "";
			$success = (bool)$suc;
            $arrytest = array_merge(array('error'=>array('code'=>400, 'message' =>"thana or district did not match")),array('duplicates'=>$duplicates),array('success'=>$success));
            $response = json_encode($arrytest);
            echo $response; exit;
            }
	    
}


} 


public function getOrderDetails() {

	$sql = "SELECT * FROM tbl_order_details WHERE 
	merOrderRef = :order_id";

	$stmt = $this->dbConn->prepare($sql);
	$stmt->bindParam(':order_id', $this->order_id);
	$stmt->execute();
	$orderr = $stmt->fetch(PDO::FETCH_ASSOC);
	return $orderr;
}





}


?>  
        
