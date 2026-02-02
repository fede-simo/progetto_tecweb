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

function allyModTesto($testo): string {

    $map  = [
		'ETF' => '<abbr title="Exchange Traded Fund" lang="en">ETF</abbr>',
		'PIR' => '<abbr title="Piani Individuali di Risparmio">PIR</abbr>',
		'TFR' => '<abbr title="Trattamento di Fine Rapporto">TFR</abbr>',
		'PIP' => '<abbr title="Piani Individuali Pensionistici">PIP</abbr>',
		'long-term' => '<span lang="en">long-term</span>',
		'budget' => '<span lang="en">budget</span>',
		'Budget' => '<span lang="en">Budget</span>',
		'minimal' => '<span lang="en">minimal</span>',
		'envelope' => '<span lang="en">envelope</span>'
	];

	foreach ($map as $key => $replacement) {
		$testo = str_replace($key, $replacement, $testo);
	}

	return $testo;
}

?>