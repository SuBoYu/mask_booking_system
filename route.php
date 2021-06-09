<?php
$route = new Router(Request::uri()); //搭配 .htaccess 排除資料夾名稱後解析 URL
$route->getParameter(1); // 從 http://127.0.0.1/game/aaa/bbb 取得 aaa 字串之意
date_default_timezone_set("Asia/Taipei");
// 用參數決定載入某頁並讀取需要的資料
switch($route->getParameter(1)){
  case "logout";
      unset($_SESSION['UID']);
      unset($_SESSION['Account']);
      header('Location: login');
    break;

  case "home":
    if(UserVeridator::isLogin(isset($_SESSION['Account'])?$_SESSION['Account']:'')){
		$error = [];
		if(isset($_POST['submit'])) {
			//接收表單傳來的資料
			$gump = new GUMP();
			$_POST = $gump->sanitize($_POST); 


			//驗證資料合法性
			$validation_rules_array = array(
				'Price_min'    => 'numeric',
				'Price_max'    => 'numeric'
			);
			$gump->validation_rules($validation_rules_array);

			$filter_rules_array = array(
				'Price_min' => 'trim',
				'Price_max' => 'trim',
				'Shop' => 'trim',
			);
			$gump->filter_rules($filter_rules_array);

			$validated_data = $gump->run($_POST);

			if($validated_data === false) {
				$error = $gump->get_readable_errors(false);
			}
			if(count($error) == 0) { 
				try{
					//依照條件查詢店家
					//
					// foreach($validation_rules_array as $key => $val) {
						// ${$key} = $_POST[$key];
						// echo $_POST[$key];
					// }
					// $condition = "name in :name and city = :city and price >= :Price_min and price <= :Price_max and amount = :Amount";
					$order_by = "1";
					$fields = "SID, name, city, price, amount";
					$limit = ""; //"LIMIT 5";
					$data_array = [];
					
					//echo "name: ${_POST['Shop']}<br>city: ${_POST['City']}<br>min: ${_POST['Price_min']}<br>max: ${_POST['Price_max']}<br>Amount: ${_POST['Amount']}<br>";
					$condition[] = 'name LIKE :name';
					$data_array[":name"] = "%".$_POST['Shop']."%";
					if ($_POST['City'] != "All") {
						$condition[] = 'city = :City';
						$data_array[":City" ] = $_POST['City'];
					}
					if (!empty($_POST['Price_min'])) {
						$condition[] = 'price >= :Price_min';
						$data_array[":Price_min"] = $_POST['Price_min'];
					}
					if (!empty($_POST['Price_max'])) {
						$condition[] = 'price <= :Price_max';
						$data_array[":Price_max"] = $_POST['Price_max'];
					}
					switch ($_POST['Amount']) {
					case "empty":
						$condition[] = 'amount = 0';
						break;
					case "few":
						$condition[] = 'amount > 0 AND amount < 100';
						break;
					case "lot":
						$condition[] = 'amount >= 100';
						break;
					default:
						break;
					}

					$condition = "".implode(" AND ", $condition);
					if (isset($_POST['work_at']) && $_POST['work_at'] == True)
						$condition .= " AND (UID = ${_SESSION['UID']} OR SID in (SELECT SID FROM staff WHERE UID = ${_SESSION['UID']}))";
				}
				catch(PDOException $e) {
					$error[] = $e->getMessage();
				}
				
			}
		}
		if (isset($_POST['buy'])){
			if ((int)$_POST['order_amount'] > (int)$_POST['amount']){
				echo "in";
				$error["buy"] = "Failed order_amount shoud not exceed the inventory_amount!!";
			}
			if ((int)$_POST['order_amount'] <= 0){
				$error["buy"] = "Failed order_amount shoud not be smaller than or equal to 0!!";
			}
			if(count($error) == 0)
			{ 
				try {
					// 新增到資料庫
					$time=date("Y-m-d H:i:s");
					$data_array = array(
						"creator_UID" =>  $_SESSION['UID'],
						"SID" => $_POST["SID"],
						"create_time" => $time,
						"amount" => $_POST["order_amount"],
						"price" => $_POST["price"],
						"status" => 'unfinished'
					);
					Database::get()->insert("mask_order", $data_array);
					$update_array = array(
						":amount" => (int)$_POST['amount']-(int)$_POST['order_amount']
					);
					Database::get()->execute("UPDATE store SET amount = :amount WHERE SID = ". $_POST['SID'], $update_array);
					// 彈出成功視窗，redirect to register page
					echo '<script> alert("order Success!")</script>';				
					// else catch the exception and show the error.
				} 
				catch(PDOException $e) {
					$error[] = $e->getMessage();
				}
			}
		}
		//這邊要怎麼樣才能讓下面search出來的結果停在畫面上
		include('view/body/home.php');  // 載入首頁的頁面
	}
    else{
      header('Location: logout');
    }
  break;
  
  case "shop":
    if(UserVeridator::isLogin(isset($_SESSION['Account'])?$_SESSION['Account']:'')){
        $result = Database::get()->execute("SELECT * FROM store WHERE UID=${_SESSION['UID']}", []);
		$error = [];
		if(isset($result[0]['SID']) and !empty($result[0]['SID'])){
			// echo "do have store<br>";
			$store_info = $result[0];
			if(isset($_POST['new_employee'])) {
				$result = Database::get()->execute('SELECT Account, UID FROM members WHERE Account = :Account', array(':Account' => $_POST['employee_account']));
				if(isset($result[0]['Account']) and !empty($result[0]['Account'])){
					$empID = $result[0]['UID'];
					$result = Database::get()->execute("SELECT UID FROM staff WHERE UID =" . $result[0]['UID'] . " AND SID = " . $store_info['SID'], []);
					if(isset($result[0]['UID']) and !empty($result[0]['UID'])){
						$error['new_employee'] = "*Already a staff!!!";
					} else if($empID == $_SESSION['UID']) {
						$error['new_employee'] = "*Can NOT add YOURSELF";
					} else {
						Database::get()->execute("INSERT INTO staff (UID, SID) VALUES(:EMPID, " . $store_info['SID'] . ")", array(":EMPID" => $empID));
						echo "<script>alert('add staff successfully')</script>";
					}
				} else {
					$error['new_employee'] = "*Account NOT EXISTS";
				}
			}
			if(isset($_POST['price_amount_change'])) {
				$gump = new GUMP();
				$_POST = $gump->sanitize($_POST); 

				// 驗證資料合法性
				$validation_rules_array = array(
					'new_price' => 'required|integer|min_numeric, 0',
					'new_amount' => 'required|integer|min_numeric, 0'
				);
				$gump->validation_rules($validation_rules_array);

				$filter_rules_array = array(
					'new_price' => 'trim',
					'new_amount' => 'trim'
				);
				$gump->filter_rules($filter_rules_array);
		
				$validated_data = $gump->run($_POST);
				if($validated_data === false) {
					$error = $gump->get_readable_errors(false);
				}
				// if no errors have been created carry on

				if(count($error) == 0) { 
					try {
						foreach($validation_rules_array as $key => $val) {
							${$key} = $_POST[$key];
						}
						// 新增到資料庫
						$data_array = array(
							":price" => $new_price,
							":amount" => $new_amount
						);
						Database::get()->execute("UPDATE store SET amount = :amount, price = :price WHERE SID = ". $store_info['SID'], $data_array);
						$store_info = Database::get()->execute("SELECT * FROM store WHERE UID=${_SESSION['UID']}", [])[0];
						echo "<script>alert('update mask price and amount successfully')</script>";
					} // else catch the exception and show the error.
					catch(PDOException $e) {
						$error[] = $e->getMessage();
					}
				}
			}
			if(isset($_POST['delete_employee'])) {
				$result = Database::get()->execute('SELECT UID FROM members WHERE Account = :Account', array(':Account' => $_POST['account']));
				if(isset($result[0]['UID']) and !empty($result[0]['UID'])){
					$empID = $result[0]['UID'];
					if($empID != $_SESSION['UID']) {
						$result = Database::get()->execute("DELETE FROM staff WHERE UID = :empID AND SID = :SID", array(":empID" => $empID, ":SID" => $store_info['SID']));
					} else {
						$error['delete_employee'] = "*CAN NOT DELETE YOURSELF";
					}
				} else {
					$error['delete_employee'] = "*Account NOT EXISTS";
				}
			}
			include('view/body/shop_control.php'); // 已有自己的店(店長)
        } else {
			// echo "do not have store<br>";
			if(isset($_POST['submit'])) {
				// 接收表單傳來的資料
				$gump = new GUMP();
				$_POST = $gump->sanitize($_POST); 

				// 驗證資料合法性
				$validation_rules_array = array(
					'Shop'    => 'required',
					'City'    => 'required',
					'MaskPrice' => 'required|integer|min_numeric, 0',
					'MaskAmount' => 'required|integer|min_numeric, 0'
				);
				$gump->validation_rules($validation_rules_array);

				$filter_rules_array = array(
					'Shop' => 'trim',
					'City' => 'trim',
					'MaskPrice' => 'trim',
					'MaskAmount' => 'trim'
				);
				$gump->filter_rules($filter_rules_array);
		
				$validated_data = $gump->run($_POST);

				if($validated_data === false) {
					$error = $gump->get_readable_errors(false);
				}
				else {
					// 檢查店名是否存在資料庫了
					// validation successful
					foreach($validation_rules_array as $key => $val) {
						${$key} = $_POST[$key];
					}
					$userVeridator = new UserVeridator();
					$userVeridator->isShopDuplicate($Shop);
					$error = $userVeridator->getErrorArray();
				}
				// if no errors have been created carry on

				if(count($error) == 0)
				{ 
					try {
						// 新增到資料庫
						$data_array = array(
							"UID" =>  $_SESSION['UID'],
							"name" => $Shop,
							"city" => $City,
							"amount" => $MaskAmount,
							"price" => $MaskPrice
						);
						Database::get()->insert("store", $data_array);
						// 彈出成功視窗，redirect to register page
						echo '<script> alert("Store Register Success!")</script>';
						// 改成管理店家頁面
						header('Location: shop');
					
						// else catch the exception and show the error.
					} 
					catch(PDOException $e) {
						$error[] = $e->getMessage();
					}
				}
			}
			// 顯示畫面
			// include('view/header/default.php'); // 載入共用的頁首
			include('view/body/shop_register.php'); // 沒有自己的店(非店長)
			// include('view/footer/default.php'); // 載入共用的頁尾
		}
    }
    else{
      header('Location: logout');
    }
  break;

  case "my_order":
    if(UserVeridator::isLogin(isset($_SESSION['Account'])?$_SESSION['Account']:'')){
		$error = [];
		if(isset($_POST['search'])) {
			$condition = $_POST['Status'];
		} else {
			$condition = "all";
		}
		if(isset($_POST['cancel_order'])) {
			if(isset($_POST['COID'])) {
				$order = Database::get()->execute("SELECT * FROM mask_order WHERE creator_UID = :UID AND OID = :OID", array(":UID" => $_SESSION['UID'], ":OID" => $_POST['COID']));
				if(!empty($order)) {
					$order = $order[0];
					$time=date("Y-m-d H:i:s");
					Database::get()->execute("UPDATE mask_order SET status=\"canceled\", finish_UID=:fUID, end_time=:time WHERE OID = :OID", array(":fUID"=>$_SESSION['UID'], ":time"=>$time, ":OID"=>$order['OID']));
					Database::get()->execute("UPDATE store SET amount = amount + :amount WHERE SID = :SID", array(":amount"=>$order['amount'], ":SID"=>$order['SID']));
				} else {
					$error['delete_order_error'] = "Deletion FAILED due to incorrect order ID";
				}
			}
		}
		include('view/body/my_order.php');
    }
    else{
      header('Location: logout');
    }
  break;
  
  case "shop_order":
    if(UserVeridator::isLogin(isset($_SESSION['Account'])?$_SESSION['Account']:'')){
		$error = [];
		if(isset($_POST['search'])) {
			$condition = $_POST['Status'];
			$shop_condition = $_POST['shop'];
		} else {
			$condition = "all";
			$shop_condition = "all";
		}
		if(isset($_POST['cancel_order'])) {
			if(isset($_POST['COID'])) {
				$order = Database::get()->execute("SELECT * FROM mask_order WHERE OID = :OID and SID in (SELECT distinct store.SID FROM staff join store WHERE staff.UID=:UID and staff.SID=store.SID or store.UID=:sUID)", array(":OID" => $_POST['COID'], ":UID"=>$_SESSION['UID'], ":sUID"=>$_SESSION['UID']));
				if(!empty($order)) {
					$order = $order[0];
					$time=date("Y-m-d H:i:s");
					Database::get()->execute("UPDATE mask_order SET status=\"canceled\", finish_UID=:fUID, end_time=:time WHERE OID=:OID", array(":fUID"=>$_SESSION['UID'], ":time"=>$time, ":OID"=>$order['OID']));
					Database::get()->execute("UPDATE store SET amount = amount + :amount WHERE SID = :SID", array(":amount"=>$order['amount'], ":SID"=>$order['SID']));
				} else {
					$error['delete_order_error'] = "Deletion FAILED due to incorrect order ID";
				}
			}
		}
		if(isset($_POST['finish_order'])) {
			if(isset($_POST['FOID'])) {
				$order = Database::get()->execute("SELECT * FROM mask_order WHERE OID = :OID and SID in (SELECT distinct store.SID FROM staff join store WHERE staff.UID=:UID and staff.SID=store.SID or store.UID=:sUID)", array(":OID" => $_POST['FOID'], ":UID"=>$_SESSION['UID'], ":sUID"=>$_SESSION['UID']));
				if(!empty($order)) {
					$order = $order[0];
					$time=date("Y-m-d H:i:s");
					Database::get()->execute("UPDATE mask_order SET status=\"finished\", finish_UID=:fUID, end_time=:time WHERE OID=:OID", array(":fUID"=>$_SESSION['UID'], ":time"=>$time, ":OID"=>$order['OID']));
				} else {
					$error['finish_order_error'] = "Deletion FAILED due to incorrect order ID";
				}
			}
		}
		include('view/body/shop_order.php');
    }
    else{
      header('Location: logout');
    }
  break;
	
  case "register":

    if(isset($_POST['submit']))
    {
      //接收表單傳來的資料
      $gump = new GUMP();
      $_POST = $gump->sanitize($_POST); 


      //驗證資料合法性
      $validation_rules_array = array(
        'Account'    => 'required|alpha_numeric',
        'password'    => 'required|alpha_numeric',
        'passwordConfirm' => 'required',
        'Phonenumber' => 'required|numeric'
      );
      $gump->validation_rules($validation_rules_array);

      $filter_rules_array = array(
        'Account' => 'trim',
        'password' => 'trim',
        'passwordConfirm' => 'trim',
        'Phonenumber' => 'trim'
      );
      $gump->filter_rules($filter_rules_array);

      $validated_data = $gump->run($_POST);

      if($validated_data === false) {
        $error = $gump->get_readable_errors(false);
      }
      else {
        //檢查帳號是否存在資料庫了
        //檢查輸入兩次的密碼是否相符
        // validation successful
        foreach($validation_rules_array as $key => $val) {
          ${$key} = $_POST[$key];
        }
        $userVeridator = new UserVeridator();
        $userVeridator->isPasswordMatch($password, $passwordConfirm);
        $userVeridator->isAccountDuplicate($Account);
        $error = $userVeridator->getErrorArray();
      }
      // if no errors have been created carry on

      if(count($error) == 0)
      {
        $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
        

        try {
          //新增到資料庫
          $data_array = array(
            "Account" => $Account,
            "password" => $hashedpassword,
            "Phonenumber" => $Phonenumber,
          );
          echo $data_array;
          Database::get()->insert("members", $data_array);

          //redirect to register page
          header('Location: '.Config::BASE_URL."login");
        
        //else catch the exception and show the error.
        } 
        catch(PDOException $e) {
          $error[] = $e->getMessage();
        }
        echo $hashedpassword;
      }
    }
    //顯示畫面
    include('view/header/default.php'); // 載入共用的頁首
    include('view/body/register.php');  // 載入註冊用的頁面
    include('view/footer/default.php'); // 載入共用的頁尾
  break;

  case "login":
    if(isset($_POST["submit"]))
    {
      $gump = new GUMP();

      $_POST = $gump->sanitize($_POST);
    
      $validation_rules_array = array(
        'Account'    => 'required|alpha_numeric',
        'password'    => 'required|alpha_numeric'
      );
      $gump->validation_rules($validation_rules_array);

      $filter_rules_array = array(
        'Account' => 'trim',
        'password' => 'trim'
      );

      $gump->filter_rules($filter_rules_array);

      $validated_data = $gump->run($_POST);

      if($validated_data === false){
        $error = $gump->get_readable_errors(false);
      }
      else{
        // validation successful
        foreach($validation_rules_array as $key => $val){
          ${$key} = $_POST[$key];
        }

        $userVeridator = new UserVeridator();
        $userVeridator->loginVerification($Account, $password);
        $error = $userVeridator->getErrorArray();

        if(count($error) == 0){
          $condition = "Account = :Account";
          $order_by = "1";
          $fields = "*";
          $limit = "LIMIT 1";
          $data_array = array(":Account" => $Account);
          $result = Database::get()->query("members", $condition, $order_by, $fields, $limit, $data_array);
          $_SESSION['UID'] = $result[0]['UID'];
          $_SESSION['Account'] = $Account;
		  $_SESSION['Phonenumber'] = $result[0]['Phonenumber'];

          header('Location: home');
        }
      }
    }
    include('view/header/default.php'); // 載入共用的頁首
    include('view/body/login.php'); // 載入登入用的頁面
    include('view/footer/default.php'); // 載入共用的頁尾
  break;

  default:
	header('Location: login');
    // include('view/header/default.php'); // 載入共用的頁首
    // include('view/body/login.php'); // 載入登入用的頁面
    // include('view/footer/default.php'); // 載入共用的頁尾
  break;
}