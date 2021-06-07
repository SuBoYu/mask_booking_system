<html>
<head>
	<meta charset='utf-8'>
	<link rel='stylesheet' type='text/css' href='view/body/home.css'>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
	<title>home page</title>
</head>
<body>
	<div class="tab">
	    <a href='home'><div class='tablinks active'>Home</div></a>
	    <a href='shop'><div class='tablinks'>Shop</div></a>
		<a href='my_order'><div class='tablinks'>My Order</div></a>
		<a href='shop_order'><div class='tablinks'>Shop Order</div></a>
	    <a href='logout'><div class="tablinks">Logout</div></a>
	</div>

	<!-- Tab content -->
	<div id="home" class="tabcontent">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
					<form role="form" method="post" action="" autocomplete="off">
						<?php
							//check for any errors
							if(isset($error)){
								foreach($error as $error){
									echo '<p class="bg-danger">'.$error.'</p>';
								}
							}
						?>
						<h2>Profile</h2>
						<hr>
						<label>Account: </label><?php echo $_SESSION['Account']?>
						<hr>
						<label>Phonenumber: </label><?php echo $_SESSION['Phonenumber']?>
						<hr>
						<h2>Shop List</h2>
						<hr>
						<div class="form-group">
							<label>Shop</label>
							<input type="text" name="Shop" id="Shop" class="form-control input-lg" placeholder="Shop" value="<?php if(isset($error) and isset($_POST['Shop'])){ echo htmlspecialchars($_POST['Shop'], ENT_QUOTES); } ?>">
						</div>
						<hr>
						<div class="form-group">
							<label for="City">City</label>
							<select class="form-control" name="City" id="City">
								<option value="All">All</option>
								<option value="Taipei">Taipei</option>
								<option value = "Taichung">Taichung</option>
								<option value = "Kaohsiung">Kaohsiung</option>
								<option value = "Tainan">Tainan</option>
								<option value = "NewTaipei">NewTaipei</option>
								<option value = "Taoyuan">Taoyuan</option>
								<?php
									// $result = Database::get()->execute('SELECT distinct city FROM store', []);	
									// foreach($result as $row) {
										// echo "<option value=$row[0] id=$row[0]>$row[0]</option>";
									// }
								?>
							</select>
						</div>
						<hr>
						<div class="form-group">
							<label>Price</label>
							<input type="text" name="Price_min" id="Price_min"  class="form-control input-lg" placeholder="min" value="<?php if(isset($error) and isset($_POST['Price_min'])){ echo htmlspecialchars($_POST['Price_min'], ENT_QUOTES); } ?>">
							~
							<input type="text" name="Price_max" id="Price_max"  class="form-control input-lg" placeholder="max" value="<?php if(isset($error) and isset($_POST['Price_max'])){ echo htmlspecialchars($_POST['Price_max'], ENT_QUOTES); } ?>">
						</div>
						<hr>
						<div class="form-group">
							<label>Amount</label>
							<select class="form-control" name="Amount" id="Amount">
								<option value="All">All</option>
								<option value="empty">(售完)0</option>
								<option value="few">(稀少)1~99</option>
								<option value="lot">(充足)100+</option>
							</select></p>
						</div>
						<hr>
						<div class="row">
							<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Search" class="btn btn-primary btn-block btn-lg"></div>
							<input type="checkbox" id="work_at" name="work_at" value="work_at">
							<label for="work_at">Only show the shop I work at</label><br>
						</div>
					</form>
					<div>
						<?php
							//echo Database::get()->getLastSql();
							echo "<br>";
							echo "<table class='table'> 
									<tr>
										<th scope='col'>Shop</th>
										<th scope='col'>City</th>
										<th scope='col'>Mask Price</th>
										<th scope='col'>Mask Amount</th>
									</tr>";
								if(isset($condition)) {
									$result = Database::get()->query("store", $condition, $order_by, $fields, $limit, $data_array);
								} else {
									$result = Database::get()->execute("SELECT SID, name, city, price, amount FROM store", []);
								}
								foreach($result as $row){ 
									echo "<form role='form' method='post' action='' autocomplete='off'>";	
									echo "<tr><td>" . $row['name'] . "</td><td>" . $row['city'] . "</td><td>" . $row['price'] . "</td><td>" . $row['amount'] . "</td><td>" . "<input type='hidden' name = 'SID' value='" . $row['SID'] . "'>" . "<input type='hidden' name = 'price' value='" . $row['price'] . "'>" . "<input type='hidden' name = 'amount' value='" . $row['amount'] . "'>" ."<div class='row'> <div class='col-xs-6 col-sm-6 col-md-6'> <div class=form-group'> <input type='text' name='order_amount' id='order_amount' class='form-control input-lg' placeholder='0' tabindex='2'> </div> </div> <div class='col-xs-6 col-md-6'> <input type='submit' name='buy' value='Buy!' class='btn btn-primary btn-block btn-lg' tabindex='5'> </div> </div>" . "</td></tr>";
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
