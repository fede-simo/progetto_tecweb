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

?>