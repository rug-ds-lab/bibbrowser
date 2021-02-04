<?php

$_GET['library']=1;
@define('BIBTEXBROWSER_BIBTEX_LINKS',false);
require('bibtexbrowser.php');

$db = new BibDataBase();

Markup('bibtexbrowseyear',    'fulltext','/\\(:bibyear (\\d{4}):\\)/',                "BibBrowseByYear");
Markup('bibtexbrowserel',     'fulltext','/\\(:bibyear ([-+]?[0-9]{1,2}):\\)/',       "BibBrowseByYearRel");
Markup('bibtexbrowselast',    'fulltext','/\\(:biblast (\\S+) ([0-9]{1,2}):\\)/',     "BibBrowseByLast");
Markup('bibtexbrowseauth',    'fulltext','/\\(:bibauth (\\S+):\\)/',                  "BibBrowseByAuth");
Markup('bibtexbrowseauthtype','fulltext','/\\(:bibauth (\\S+) (\\S+):\\)/',           "BibBrowseByAuth");
Markup('bibtexbrowsetype',    'fulltext','/\\(:bibtype (\\S+):\\)/',                  "BibBrowseByType");


function BibLoad($bib) {
	global $db;
	$db->load($bib);
}

function BibBrowseByYear($m) {
	global $db;
	
	$query = array('year'=>$m[1]);
	$entries = $db->multisearch($query);
	uasort($entries, 'academicOrder');
	
	return printEntries($entries);
}

function BibBrowseByYearRel($m) {
	global $db;
	
	$query = array('year'=>date("Y") + $m[1]);
	$entries = $db->multisearch($query);
	uasort($entries, 'academicOrder');
	
	return printEntries($entries);
}

function BibBrowseByLast($m) {
	global $db;
	
	$query = array('author'=>$m[1]);
	$entries = $db->multisearch($query);
	uasort($entries, 'academicOrder');
	
	$entries = array_slice($entries, 0, $m[2]);
	
	return printEntries($entries);
}

function BibBrowseByAuth($m) {
	global $db;
	
	$query = array('author'=>$m[1],
				   'type'=>(isset($m[2]) ? $m[2] : '.*'));
	$entries = $db->multisearch($query);
	uasort($entries, 'academicOrder');
	
	return printEntries($entries);
}

function BibBrowseByType($m) {
	global $db;
	
	$query = array('type'=>$m[1]);
	$entries = $db->multisearch($query);
	uasort($entries, 'academicOrder');
	
	return printEntries($entries);
}

function printEntries($entries) {
	$output = '<ol>';
	
	foreach ($entries as $bibentry) {
	  $output .= '<li class="text-justify">' . $bibentry->toHTML() . '</li>';
	}
	$output .= '</ol>';
	
	return Keep($output);
}

function academicOrder($a, $b) {
	$r = (($a->getYear() < $b->getYear()) ? 1 : (($a->getYear() > $b->getYear()) ? -1 : 0));
	if($r == 0) $r = ((getTypeWeight($a) < getTypeWeight($b)) ? -1 : ((getTypeWeight($a) > getTypeWeight($b)) ? 1 :0));
	if($r == 0) $r = strcmp($a->getField("month"), $b->getField("month"));
	if($r == 0) $r = strcmp($a->getFormattedAuthorsString(), $b->getFormattedAuthorsString());
	
	return $r;
}

function getTypeWeight($e) {
	$r = 6;
	
	switch ($e->getType()) {
		case "book":
			$r = 1;
			break;
		case "article":
			$r = 3;
			break;
		case "booklet":
			$r = 1;
			break;
		case "inbook":
			$r = 1;
			break;
		case "incollection":
			$r = 4;
			break;
		case "inproceedings":
			$r = 4;
			break;
		case "manual":
			$r = 5;
			break;
		case "mastersthesis":
			$r = 5;
			break;
		case "phdthesis":
			$r = 1;
			break;
		case "proceedings":
			$r = 2;
			break;
		case "techreport":
			$r = 5;
			break;
		case "unpublished":
			$r = 6;
			break;
	    default:
			$r = 6;
	}
	
	return $r;
}

?>
