<?php

$_GET['library']=1;
@define('BIBTEXBROWSER_BIBTEX_LINKS',false);
@define('ORDER_FUNCTION','compare_bib_entry_by_bibtex_order');
require('bibtexbrowser.php');

$db = new BibDataBase();
$bib = $BibtexBib;

Markup('bibtexbrowseyear',    'fulltext','/\\(:bibyear (\\d{4}):\\)/',                "BibBrowseByYear");
Markup('bibtexbrowserel',     'fulltext','/\\(:bibyear ([-+]?[0-9]{1,2}):\\)/',       "BibBrowseByYearRel");
Markup('bibtexbrowselast',    'fulltext','/\\(:biblast (\\S+) ([0-9]{1,2}):\\)/',     "BibBrowseByLast");
Markup('bibtexbrowseauth',    'fulltext','/\\(:bibauth (\\S+):\\)/',                  "BibBrowseByAuth");
Markup('bibtexbrowseauthtype','fulltext','/\\(:bibauth (\\S+) (\\S+):\\)/',           "BibBrowseByAuth");
Markup('bibtexbrowsetype',    'fulltext','/\\(:bibtype (\\S+):\\)/',                  "BibBrowseByType");


function load($bib) {
	global $db;
	$db->load($bib);
}

function BibBrowseByYear($m) {
	global $db, $bib;
	load($bib);
	
	$query = array('year'=>$m[1]);
	$entries = $db->multisearch($query);
	uasort($entries, 'compare_bib_entries');
	
	return printEntries($entries);
}

function BibBrowseByYearRel($m) {
	global $db, $bib;
	load($bib);
	
	$query = array('year'=>date("Y") + $m[1]);
	$entries = $db->multisearch($query);
	uasort($entries, 'compare_bib_entries');
	
	return printEntries($entries);
}

function BibBrowseByLast($m) {
	global $db, $bib;
	load($bib);
	
	$query = array('author'=>$m[1]);
	$entries = $db->multisearch($query);
	uasort($entries, 'compare_bib_entries');
	
	$entries = array_slice($entries, 0, $m[2]);
	
	return printEntries($entries);
}

function BibBrowseByAuth($m) {
	global $db, $bib;
	load($bib);
	
	$query = array('author'=>$m[1],
				   'type'=>(isset($m[2]) ? $m[2] : '.*'));
	$entries = $db->multisearch($query);
	uasort($entries, 'compare_bib_entries');
	
	return printEntries($entries);
}

function BibBrowseByType($m) {
	global $db, $bib;
	load($bib);
	
	$query = array('type'=>$m[1]);
	$entries = $db->multisearch($query);
	uasort($entries, 'compare_bib_entries');
	
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

?>