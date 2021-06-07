<html>
<head>
	<meta charset='utf-8'>
	<link rel='stylesheet' type='text/css' href='view/body/home.css'>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
	<script src="view/body/home.js"></script>
	<title>home page</title>
</head>
<body>
	<div class="tab">
	    <a href='home'><div class='tablinks'>Home</div></a>
	    <a href='shop'><div class='tablinks active'>Shop</div></a>
		<a href='my_order'><div class='tablinks'>My Order</div></a>
		<a href='shop_order'><div class='tablinks'>Shop Order</div></a>
	    <a href='logout'><div class="tablinks">Logout</div></a>
	</div>

	<!-- Tab content -->
	<div id="home" class="tabcontent"></div>

	<div id="shop" class="tabcontent">
	    <div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
					<form role="form" method="post" action="" autocomplete="off">
						<h2>Register shop</h2>
						<p>* Since you do not have any shop, you can register one to sell masks~ </p>
						<hr>
						<?php
						//check for any errors
						if(isset($error)){
							foreach($error as $error){
								echo '<p class="bg-danger">'.$error.'</p>';
							}
						}

						//if action is joined show sucess
						if(isset($_GET['action']) && $_GET['action'] == 'joined'){
							echo "<h2 class='bg-success'>Registration successful, please check your email to activate your account.</h2>";
						}
						?>

						<div class="form-group">
							<label>Shop</label>
							<input type="text" name="Shop" id="Shop" class="form-control input-lg" placeholder="Shop" value="<?php if(isset($error) and !empty($error)){ echo htmlspecialchars($_POST['Shop'], ENT_QUOTES); } ?>" tabindex="1">
						</div>
						<div class="form-group">
							<label for="City">City</label>
							<select class="form-control" name="City" id="City">
								<option value="Taipei">Taipei</option>
								<option value = "Taichung">Taichung</option>
								<option value = "Kaohsiung">Kaohsiung</option>
								<option value = "Tainan">Tainan</option>
								<option value = "NewTaipei">NewTaipei</option>
								<option value = "Taoyuan">Taoyuan</option>
							</select>
						</div>
						<div class="form-group">
							<label>Mask Price</label>
							<input type="text" name="MaskPrice" id="MaskPrice" class="form-control input-lg" placeholder="Mask Price" value="<?php if(isset($error)  and !empty($error)){ echo htmlspecialchars($_POST['MaskPrice'], ENT_QUOTES); } ?>" tabindex="1">
						</div>
						
						<div class="form-group">
							<label>Mask Amount</label>
							<input type="text" name="MaskAmount" id="MaskAmount" class="form-control input-lg" placeholder="Mask Amount" value="<?php if(isset($error) and !empty($error)){ echo htmlspecialchars($_POST['MaskAmount'], ENT_QUOTES); } ?>" tabindex="4">
						</div>
						<div class="row">
							<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Register" class="btn btn-primary btn-block btn-lg" tabindex="5"></div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
