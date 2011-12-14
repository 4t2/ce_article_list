
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<?php foreach ($this->pages as $page): ?>
<?php if ($this->hlPage): ?>
<<?php echo $this->hlPage; ?>><?php echo $page['title']; ?></<?php echo $this->hlPage; ?>>
<?php endif; ?>
<?php if (strlen($page['articles'][0]['teaser'])): ?>
<div><?php echo $page['articles'][0]['teaser']; ?></div>
<?php endif; ?>
<ul>
<?php foreach ($page['articles'] as $article): ?>
	<li><a href="<?php echo $article['link']; ?>"><?php echo $article['title']; ?></a></li>
<?php endforeach; ?>
</ul>
<?php endforeach; ?>

</div>
