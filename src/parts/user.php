<?php
namespace modes;

?>

<h2>autologin sessions</h2>
<?php
$cols = array(
	'created' => 'Created',
	'used' => 'Last used',
	'ip' => 'IP',
	'user_agent' => 'User Agent'
);
$sessions = get_autologin_sessions();
fb($sessions[0]);
if (!empty($sessions)) :
?>

<table class="user-autologins">
	<tr>
	<?php foreach ($cols as $id => $name) :?>
		<th><?=$name?></th>
	<?php endforeach; ?>
	</tr>
<?php foreach ($sessions as $session) : ?>
	<tr>
	<?php foreach ($cols as $id => $name) :?>
		<td><?=$id === 'created' || $id === 'used' ? format_datetime(strtotime($session[$id])) : $session[$id]?></td>
	<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
</table>
<form method="post">
	<p><input type="submit" value="Remove all sessions"></p>
	<?=get_post_nonce_field()?>
	<input type="hidden" name="reset_autologin">
</form>

<?php else : ?>
<p>No active sessions.</p>
<?php endif; ?>