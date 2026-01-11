<?php

function replaceContent($bookmark, $newContent, &$paginaHTML): void{
	$start = "<!--{start-".$bookmark."}-->";
	$end = "<!--{end-".$bookmark."}-->";

	$startPos = strpos($paginaHTML, $start);
	$endPos = strpos($paginaHTML, $end);

	if ($startPos === false || $endPos === false) {
		return;
	}

	$contentStart = $startPos + strlen($start);
	$contentLength = $endPos - $contentStart;

	$paginaHTML = substr_replace(
		$paginaHTML,
		$newContent,
		$contentStart,
		$contentLength
	);
}

function allyModCorso($modalita): string {
	if (str_contains($modalita, 'Online live')) {
		return '<span lang="en">Online live</span>';
	} elseif (str_contains($modalita, 'Online registrata')) {
		return '<span lang="en">Online</span> registrata';
	} elseif (str_contains($modalita, 'In aula')) {
		return 'In aula';
	} else {
		return htmlspecialchars($modalita, ENT_QUOTES);
	}
}

?>