<?php
namespace modes;

?><form class="login-form" method="post">
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
				<input type="checkbox"
					<?php if (!empty($_COOKIE['uid'])) :?>checked="checked"<?php endif;?>
					name="autologin" id="autologin">
				<label class="cell" for="autologin">Remember me</label>
			</td>
		</tr>
		<tr class="login-submit">
			<td colspan="2"><input class="button" type="submit" value="<?=$args['title']?>"></td>
		</tr>
	</table>
	<?=get_post_nonce_field();?>
	<input type="hidden" name="login">
	<p class="login-reset"><a href="<?=WWW_ROOT?>/reset">Can't log in?</a></p>
</form>
