<?php
$videos = $attributes['videoURLs'] ?? [];
$videos_reversed = array_reverse($videos);

$posterURL = $attributes["posterURL"] ?? '';
$playAuto = $attributes["playAuto"] ?? false;
$playLoop = $attributes["playLoop"] ?? false;
$playNoSound = $attributes["playNoSound"] ?? false;
$playControls = $attributes["playControls"] ?? true;
$playButton = $attributes["playButton"] ?? false;

$uid = wp_unique_id('vr-');
$playButton = $playButton ? 'video-play-button' : '';

$block_props = get_block_wrapper_attributes([
	'class' => $playButton,
	'id' => $uid,
]);

$css = '';
foreach ($videos as $i => $v) {
	$w = $v['width'] ?? 0;
	$h = $v['height'] ?? 0;
	$size = (int)($v['size'] ?? 0);
	if (!$w || !$h) continue;

	if ($i === 0) {
		$css .= "#$uid{aspect-ratio:{$w}/{$h}}";
	} elseif ($size) {
		$css .= "@media(max-width:{$size}px){#{$uid}{aspect-ratio:{$w}/{$h}}}";
	}
}

$video_attributes = "playsinline";
$video_attributes .= $playAuto ? ' autoplay' : '';
$video_attributes .= $playLoop ? ' loop' : '';
$video_attributes .= $playNoSound ? ' muted' : '';
$video_attributes .= $playControls ? ' controls' : '';
$preload = $playAuto ? 'none' : 'metadata';

// loading="lazy" is valid however not many browsers support it as of now (2026)
// we have it so the browsers that do support it gets better performance and browsers that do not ignore it
?>
<figure <?= $block_props; ?>>
	<video loading="lazy" <?= $video_attributes ?> preload="<?= $preload ?>"<?= $posterURL ? ' poster="' . htmlspecialchars($posterURL) . '"' : '' ?>>
		<?php foreach ($videos_reversed as $index => $video) : ?>
			<?php
				$is_default = $index >= count($videos_reversed) - 1;
				$media_attr = $is_default ? '' : "media='(max-width:" . (int) $video["size"] . "px)'";
				$type_attr = !empty($video['mime']) ? 'type="' . $video['mime'] . '"' : '';
			?>
			<source src="<?= htmlspecialchars($video['url']); ?>" <?= $media_attr ?> <?= $type_attr ?>/>
		<?php endforeach; ?>

		<p>Din browser supporter ikke video</p>
	</video>
</figure>
<?php if ($css): ?><style><?= $css ?></style><?php endif; ?>