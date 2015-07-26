<?php
namespace modes;

?><form action="<?=WWW_ROOT?>/upload" method="post" id="upload-form">
	<noscript>
		<p><input type="file" name="file"></p>
		<p><input type="submit" value="Upload"></p>
		<?=get_post_nonce_field();?>
	</noscript>
</form>
<script>
document.getElementById('upload-form').classList.add('dropzone');
</script>

<h2>Fetch remote</h2>
<form action="<?=WWW_ROOT?>/fetch" class="home-fetch">
	<p><label for="home-fetch-url">URL</label> <input type="text" value="" name="url" id="home-fetch-url"> <input type="submit" value="Fetch"></p>
	<script>document.getElementById('home-fetch-url').focus();</script>
</form>

<h2>About</h2>
<ul>
	<li><strong><?=number_format(get_file_count())?></strong> uploaded files</li>
	<li><strong><?=format_bytes(get_total_file_size())?></strong> total file size</li>
	<li><strong><?=format_bytes(disk_total_space('/'))?></strong> free space</li>
</ul>

<h2>Bookmarklets</h2>
<ul class="bookmarklets-list">
	<li>
		<a href="javascript:void((function(){if(typeof%20__modes__!='undefined'){__modes__.toggle();return;}var%20d=document,q=d.createElement('script');q.setAttribute('src','<?=WWW_FULL?>/bookmarklet.js');d.body.appendChild(q)})());"
			onclick="alert('Bookmark to use'); return false;">
			Save to <?=TOOL_NAME?>
    	</a>
	</li>
	<li>
		<a href="javascript:void((function()%20{var%20x=document.images[0];prompt('',%20'<img%20src=%22'%20+%20x.src%20+%20'%22%20width=%22'%20+%20x.width%20+%20'%22%20height=%22'%20+%20x.height%20+%20'%22%20alt=%22%22%20/>')})())"
		onclick="alert('Bookmark to use'); return false;">
			&lt;img>
		</a>
	</li>
</ul>
