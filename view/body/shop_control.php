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
	    <a href='shop'><div class='tablinks active'>Shop</div></a>
		<a href='my_order'><div class='tablinks'>My Order</div></a>
		<a href='shop_order'><div class='tablinks'>Shop Order</div></a>
	    <a href='logout'><div class="tablinks">Logout</div></a>
	</div>

	<!-- Tab content -->
	<div id="shop" class="tabcontent">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
					<h2>My Shop</h2>
					<hr>
					<label>Shop</label>
					<input type="text" class="form-control input-lg" placeholder="shop name" value="<?php echo $store_info['name'] ?>" disabled>
					<hr>
					<label>City</label>
					<input type="text" class="form-control input-lg" placeholder="shop city" value="<?php echo $store_info['city']?>" disabled>
					<hr>
					<form role="form" method="post" action="" autocomplete="off">
						<div class="form-group">
							<label>Mask Price</label>
							<input type="text" class="form-control input-lg" name="new_price" placeholder="mask price" value="<?php echo $store_info['price']?>">
							<label>Mask Amount</label>
							<input type="text" class="form-control input-lg" name="new_amount" placeholder="mask price" value="<?php echo $store_info['amount']?>">
							<input type="submit" class="btn btn-primary btn-block btn-lg" name="price_amount_change" value="Update">
						</div>
					</form>
					<hr>
					<form role="form" method="post" action="" autocomplete="off">
						<label>Employee</label><br>
						<div class="form-group">
							<input type="text" class="form-control input-lg" name="employee_account" id="employee_account" placeholder="Type Account">
							<input type="submit" class="btn btn-primary btn-block btn-lg" name="new_employee" value="add">
						</div>
						<?php
							if(isset($error['new_employee'])) {
								echo '<p class="bg-danger">'.$error['new_employee'].'</p>';
							}
						?>
					</form>
					
					<div>
						<?php
							$staff = Database::get()->execute("SELECT Account, Phonenumber as Phone FROM members WHERE UID in (SELECT UID FROM staff WHERE SID = ${store_info['SID']})", []);
							echo "<br>";
							echo "<table class='table'> 
								<tr>
									<th scope='col'>Account</th>
									<th scope='col'>Phone</th>
									<th scope='col'>ACTION</th>
								</tr>";
							foreach($staff as $row){
								echo "<form role='form' method='post' action='' autocomplete='off'>";								
								echo "<tr><td>" . $row['Account'] . "</td><td>" . $row['Phone'] . "</td><td>" . "<input type='hidden' name = 'account' value='" . $row['Account'] . "'>" . "<input type='submit' class='btn btn-primary btn-block btn-lg'  name = 'delete_employee' value='DELETE' style='background-color:red;'> " . "</td></tr>";
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
