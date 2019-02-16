<?php

class API extends Rest{

	public function __construct() {
			parent::__construct();
			
		}

		public function generateToken() {
            $email = $this->validateParameter('userName', $this->param['userName'], STRING);
			$pass = $this->validateParameter('userPassword', $this->param['userPassword'], STRING);
            try {
			$stmt = $this->dbConn->prepare("SELECT * FROM tbl_user_info WHERE userName = :userName AND userPassword = :userPassword");
				$stmt->bindParam(":userName", $email);
				$stmt->bindParam(":userPassword", md5($pass));
				$stmt->execute();
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
				
				if(!is_array($user)) {
					$this->returnResponse(INVALID_USER_PASS, "Email or Password is incorrect.");
				}

				$paylod = [
					'iat' => time(),
					'iss' => 'localhost',
					'exp' => time() + (60*60),
					'userId' => $user['user_id']
				];

				$token = JWT::encode($paylod, SECRETE_KEY);
				$data = ['token' => $token];
				$this->returnResponse(SUCCESS_RESPONSE, $data);
				} catch (Exception $e) {
				$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
			}
		}

		public function pushOrder() {
				
			$action = $this->validateParameter('action', $this->param['action'], STRING, false);
			$recipient_name = $this->validateParameter('recipient_name', $this->param['recipient_name'], STRING, false);
			$recipient_email = $this->validateParameter('recipient_email', $this->param['recipient_email'], STRING, false);
			$recipient_type = $this->validateParameter('recipient_type',$this->param['recipient_type'], STRING, false);

			$recipient_mobile = $this->validateParameter('recipient_mobile', $this->param['recipient_mobile'], STRING, false);
			$recipient_thana = $this->validateParameter('recipient_thana', $this->param['recipient_thana'], STRING, true);
			$recipient_district = $this->validateParameter('recipient_district', $this->param['recipient_district'], STRING, true);
			$price = $this->validateParameter('price', $this->param['price'], STRING, false);


			$weight = $this->validateParameter('weight', $this->param['weight'], STRING, false);
			$payment_method = $this->validateParameter('payment_method', $this->param['payment_method'], STRING, false);
			$order_id = $this->validateParameter('order_id', $this->param['order_id'], STRING, true);
			$recipient_address = $this->validateParameter('recipient_address', $this->param['recipient_address'], STRING, false);

			$pick_address = $this->validateParameter('pick_address', $this->param['pick_address'], STRING, false);
			$emi_detail = $this->validateParameter('emi_detail', $this->param['emi_detail'], STRING, false);
			$freebee_detail = $this->validateParameter('freebee_detail', $this->param['freebee_detail'], STRING, false);
			$products_description = $this->validateParameter('products_description', $this->param['products_description'], STRING, false);


			$comments = $this->validateParameter('comments', $this->param['comments'], STRING, false);
			

	
			$order = new Order;


			$order->setAction($action);
			$order->setR_Name($recipient_name);
			$order->setR_Email($recipient_email);
			$order->setR_type($recipient_type);
			$order->setR_Mobile($recipient_mobile);
			$order->setR_Thana($recipient_thana);
            
            $order->setR_District($recipient_district);
			$order->setPrice($price);
			$order->setWeight($weight);
			$order->setPayment_method($payment_method);
			$order->setOrderID($order_id);
			$order->setR_Address($recipient_address);

			$order->setP_Address($pick_address);
			$order->setEmi_Detail($emi_detail);
			$order->setFreebee_Detail($freebee_detail);
			$order->setP_description($products_description);
			$order->setComments($comments);

			if(!$order->insert()) {

/*				$message = 'Failed to insert.';*/

			} else {

				 $order->setOrderID($order_id);
	             $orderr = $order->getOrderDetails();
	             $response['tracking_number'] = $orderr['orderid'];
		

				header("content-type: application/json");
				$err = "";
			    $error = (bool)$err;
			    $dup = "";
			    $duplicates = (bool)$dup;
                $arrytest = array_merge(array('error'=>$error),array('duplicates'=>$duplicates),array('success'=>array('message'=>"successfully inserted", 'tracking_id' =>$orderr['orderid'],'order_id' => $orderr['merOrderRef'])));
                $response = json_encode($arrytest);
                echo $response; exit;

				
	             /*if(!is_array($orderr)) {
				 $this->returnResponse(SUCCESS_RESPONSE, ['message' => 'Customer details not found.']);
			}*/

			
			}

			//$this->returnResponse(SUCCESS_RESPONSE, $message);
		}
       }

	

		

?>