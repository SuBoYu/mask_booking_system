<html>
<head>
	<meta charset='utf-8'>
	<link rel='stylesheet' type='text/css' href='view/body/home.css'>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
	<title>home page</title>
</head>
<body>
	<div class="tab">
	    <a href='home'><div class='tablinks '>Home</div></a>
	    <a href='shop'><div class='tablinks'>Shop</div></a>
		<a href='my_order'><div class='tablinks active'>My Order</div></a>
		<a href='shop_order'><div class='tablinks'>Shop Order</div></a>
	    <a href='logout'><div class="tablinks">Logout</div></a>
	</div>

	<!-- Tab content -->
	<div id="my_order" class="tabcontent">
		<div class="container">
			<div class="row">
				<div>
					<h2>My Order</h2>
					<hr>
					<form role='form' method='post' action='' autocomplete='off'>
						<div class="form-group">
							<div class="col-xs-6 col-md-6" >
								<label>Status</label>
								<select class="form-control" name="Status" id="Status">
									<option value="all" <?php if(isset($_POST['Status']) and !strcmp($condition, "all")) {echo "selected";} ?>>All</option>
									<option value="finished" <?php if(isset($_POST['Status']) and !strcmp($condition, "finished")) {echo "selected";} ?>>已完成</option>
									<option value="unfinished" <?php if(isset($_POST['Status']) and !strcmp($condition, "unfinished")) {echo "selected";} ?>>未完成</option>
									<option value="canceled" <?php if(isset($_POST['Status']) and !strcmp($condition, "canceled")) {echo "selected";} ?>>取消</option>
								</select>
								<div class="col-xs-6 col-md-6">
									<input type="submit" name="search" value="Search" class="btn btn-primary btn-block btn-lg">
								</div>
							</div>
						</div>
					</form>
					<hr>
					<div>
						<?php
							if(!strcmp($condition, "all")) {
								$myorder = Database::get()->execute("SELECT OID, status, create_time, end_time, name, o.price, o.amount FROM mask_order as o join store as s WHERE creator_UID = ${_SESSION['UID']} and o.SID = s.SID", []);
							} else {
								$myorder = Database::get()->execute("SELECT OID, status, create_time, end_time, name, o.price, o.amount FROM mask_order as o join store as s WHERE creator_UID = ${_SESSION['UID']} and o.SID = s.SID and status = :condition", array(":condition"=> $condition));
								// Database::get()->execute("INSERT INTO staff (UID, SID) VALUES(:EMPID, " . $store_info['SID'] . ")", array(":EMPID" => $empID));
							}
							if(isset($error['delete_order_error'])) {
								echo '<p class="bg-danger">'.$error['delete_order_error'].'</p>';
							}
							echo "<br>";
							echo "<table class='table'> 
								<tr>
									<th scope='col'>OID</th>
									<th scope='col'>Status</th>
									<th scope='col'>Start</th>
									<th scope='col'>End</th>
									<th scope='col'>Shop</th>
									<th scope='col'>Total Price</th>
									<th scope='col'>action</th>
								</tr>";
							foreach($myorder as $order){
								echo "<form role='form' method='post' action='' autocomplete='off'>";								
								echo "<tr><td>" . $order['OID'] . "</td><td>" . $order['status'] . "</td><td>" . $order['create_time'] . "</td><td>" . $order['end_time'] . "</td><td>" . $order['name'] . "</td><td>\$" . $order['price']*$order['amount'] . "(" . $order['amount'] . "*\$" . $order['price'] . ")</td><td>";
								if($order['status'] == "unfinished") {
									echo "<input type='hidden' name='OID' value='" . $order['OID'] . "'>" . "<input type='submit' class='btn btn-primary btn-block btn-lg'  name='cancel_order' value='cancel' style='background-color:red;'>";
								}
								echo "</td></tr>";
								echo "</form>";
							}
							echo "</table>";
						?>
					</div>
				</div>
			</div>
	    </div>
	</div>
</body>
</html>
