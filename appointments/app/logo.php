<?php if ($content['logo']): ?>
    <img class="brand-logo" src="<?= e(asset($content['logo'])) ?>" alt="Havisha Hospital" width="654" height="189">
<?php else: ?>
    <span class="brand-name">HAVISHA<span>HOSPITAL</span></span>
<?php endif ?>