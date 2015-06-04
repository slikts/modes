<form class="login-form" method="post">
	<table>
		<tr class="login-user row">
			<th><label for="user">User</label></th>
			<td><input type="text" name="user" id="user"></td>
		</tr>
		<tr class="login-password row">
			<th><label for="password">Password</label></th>
			<td><input type="password" name="password" id="password"></td>
		</tr>
		<tr class="login-remember row">
			<td colspan="2">
				<input type="checkbox" name="remember" id="remember">
				<label class="cell" for="remember">Remember me</label>
			</td>
		</tr>
		<tr class="login-submit">
			<td colspan="2"><input class="button" type="submit" value="<?=$args['title']?>"></td>
		</tr>
	</table>
	<input type="hidden" name="<?=session_name()?>" value="<?=session_id()?>">
	<p class="login-reset"><a href="<?=WWW_ROOT?>/reset">Can't log in?</a></p>
</form>
