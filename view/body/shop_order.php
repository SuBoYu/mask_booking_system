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
		<a href='my_order'><div class='tablinks'>My Order</div></a>
		<a href='shop_order'><div class='tablinks active'>Shop Order</div></a>
	    <a href='logout'><div class="tablinks">Logout</div></a>
	</div>

	<!-- Tab content -->
	<div id="shop_order" class="tabcontent">
		<div class="container">
			<div class="row">
				<div>
					<h2>Shop Order</h2>
					<hr>
					<form role='form' method='post' action='' autocomplete='off'>
						<div class="form-group">
							<div class="col-xs-6 col-md-6" >
								<label>Shop</label>
								<select class="form-control" name="shop" id="shop">
									<option value="all" <?php if(isset($_POST['Status']) and !strcmp($shop_condition, "all")) {echo "selected";} ?>>All</option>
									<?php
										$workAt = Database::get()->execute('SELECT distinct name FROM staff join store WHERE staff.UID=:UID and staff.SID=store.SID or store.UID=:sUID', array(":UID"=>$_SESSION['UID'], ":sUID"=>$_SESSION['UID']));
										foreach($workAt as $row) {
											if(isset($_POST['shop']) and !strcmp($shopCondition, $row[0]))
												echo "<option value=$row[0] id=$row[0] selected>$row[0]</option>";
											else
												echo "<option value=$row[0] id=$row[0]>$row[0]</option>";
										}
									?>
								</select>
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
							if(isset($error['delete_order_error'])) {
								echo "<script>${error['delete_order_error']}</script>";	
							} else if(isset($error['finish_order_error'])) {
								echo "<script>${error['finish_order_error']}</script>";	
							}
							$sql = "SELECT OID, status, create_time, end_time, name, o.price, o.amount FROM mask_order as o join store as s WHERE o.SID=s.SID and o.SID in (SELECT distinct store.SID FROM staff join store WHERE staff.UID=:UID and staff.SID=store.SID or store.UID=:sUID) and o.SID=s.SID";
							$in_params[':UID'] = $_SESSION['UID'];
							$in_params[':sUID'] = $_SESSION['UID'];
							if(strcmp($shop_condition, "all")) {
								$sql .= " and SID=:shop_condition";
								$in_params[':shop_condition'] = Database::get()->execute("SELECT SID FROM store WHERE name=:name", array(":name"=>shop_condition))[0];
							}
							if(strcmp($condition, "all")) {
								$sql .= " and status=:condition";
								$in_params[':condition'] = $condition;
							}
							$shopOrder = Database::get()->execute($sql, $in_params);
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
							foreach($shopOrder as $order){
								echo "<form role='form' method='post' action='' autocomplete='off'>";								
								echo "<tr><td>" . $order['OID'] . "</td><td>" . $order['status'] . "</td><td>" . $order['create_time'] . "</td><td>" . $order['end_time'] . "</td><td>" . $order['name'] . "</td><td>\$" . $order['price']*$order['amount'] . "(" . $order['price'] . "*" . $order['amount'] . ")</td><td>";
								if($order['status'] == "unfinished") {
									echo "<input type='hidden' name='OID' value='" . $order['OID'] . "'>" . "<input type='submit' class='btn btn-primary btn-lg'  name='finish_order' value='finish' style='background-color:green;'>";
									echo "<input type='hidden' name='OID' value='" . $order['OID'] . "'>" . "<input type='submit' class='btn btn-primary btn-lg' name='cancel_order' value='cancel' style='background-color:red;'>";
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
