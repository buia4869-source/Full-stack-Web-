<?php

function render_gallery($gallery) {

	$html = '<div class="shared-image-container">';

	if (isset($gallery)) {
		foreach ($gallery as $img) {
			$html .= "
			<div class='post-container'>
				<img src='{$img["url"]}' alt='{$img["desc"]}'>
				<div class='post-info'>
					<p><strong>{$img["email"]}</strong>: {$img["desc"]}</p>
				</div>
			</div>
			";
		}
	}

	$html .= '</div>';

	return $html;
}

?>
