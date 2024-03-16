<div class="dropdown">
<label for="dropdown_user">
	<i class="fas fa-user"></i>
</label>
<button onMouseOver="ShowDropdown('dropdown2')" id="dropdown_user" name="dropdown_user" onMouseOut="HideDropdown('dropdown2')" class="dropbtn">
	<?php print($_SESSION['login'][$application]['user']); ?>
</button>
	<table id="dropdown2" class="dropdown-content" onMouseOver="ShowDropdown('dropdown2')"  onMouseOut="HideDropdown('dropdown2')">
		<tr><td><a class="editDetails">Edit Details</a></td></tr>
		<tr><td><a href="./password.php">Change Password</a></td></tr>
		<?php
			if ($_SESSION['login'][$application]['admin'] > 0) {
				print("<tr><td><a href=\"admin/dashboard.php\">Admin Dashboard</a></td></tr>\n");
			}
		?>
		<tr><td><a href="./logout.php">Log Out</a></td></tr>
	</table>
	<script>
		$('.editDetails').click(function(){
			window.location.href = "./edit_details.php?return=" +  window.location.pathname;
			//window.location = "./edit_details.php";
		});

		$('.editDetails').css('cursor', 'pointer');
	</script>
</div>
