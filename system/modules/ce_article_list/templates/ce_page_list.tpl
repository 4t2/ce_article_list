<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<ul>
<?php foreach ($this->pages as $page): ?>
<?php if ($page['protected']): ?>
	<li class="protected level<?php echo $page['level']; ?>"><?php echo $page['title']; ?></li>
<?php else: ?>
	<li class="level<?php echo $page['level']; ?>"><a href="<?php echo $page['link']; ?>"><?php echo $page['title']; ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
</ul>
</div>