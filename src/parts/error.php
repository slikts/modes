<p><?=$args['descr']?></p>
<?php if (DEV && !empty($args['message'])) : ?>
<pre>
<?=$args['message']?>
</pre>
<?php endif; ?>
<p class="error-image"><img src="<?=STATIC_ROOT?>/images/error.jpg" width="800" height="522"></p>