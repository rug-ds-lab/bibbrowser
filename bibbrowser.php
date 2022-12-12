<?php

defined("PmWiki") or die;

$RecipeInfo['BibBrowse']['Version'] = '2022-12-07';

@define("BIBBROWSE_SORT", [
    "academic" => "bibBrowseAcademicOrder"
]);
@define("BIBBROWSE_DEFAULT_SORT", "bibBrowseAcademicOrder");

@define("BIBBROWSE_TYPE_WEIGHTS", [
    "book" => 10,
    "phdthesis" => 20,
    "proceedings" => 30,
    "booklet" => 40,
    "inbook" => 50,
    "article" => 60,
    "incollection" => 70,
    "inproceedings" => 80,
    "manual" => 90,
    "mastersthesis" => 90,
    "techreport" => 90,
    "unpublished" => 100
]);
@define("BIBBROWSE_MONTH_WEIGHTS", [
    "jan" => 1,  "january" => 1,    "1" => 1,
    "feb" => 2,  "february" => 2,   "2" => 2,
    "mar" => 3,  "march" => 3,      "3" => 3,
    "apr" => 4,  "april" => 4,      "4" => 4,
    "may" => 5,  "may" => 5,        "5" => 5,
    "jun" => 6,  "june" => 6,       "6" => 6,
    "jul" => 7,  "july" => 7,       "7" => 7,
    "aug" => 8,  "august" => 8,     "8" => 8,
    "sep" => 9,  "september" => 9,  "9" => 9,
    "oct" => 10, "october" => 10,  "10" => 10,
    "nov" => 11, "november" => 11, "11" => 11,
    "dec" => 12, "december" => 12, "12" => 12
]);
@define("BIBBROWSE_DEFAULT_WEIGHT", 100);

@define("BIBBROWSE_HTML_PREFIX_LIST", '<ol class="bibbrowse-list">');
@define("BIBBROWSE_HTML_POSTFIX_LIST", "</ol>");
@define("BIBBROWSE_HTML_PREFIX_ENTRY", '<li class="text-justify">');
@define("BIBBROWSE_HTML_POSTFIX_ENTRY", "</li>");
@define("BIBBROWSE_HTML_SEPARATOR", "");


global $db;

if (!$db) {
    // Initialize
    $db = new BibDataBase();

    Markup('bibtexbrowseyear',    '<split','/\\(:bibyear (\\d{4}):\\)/',                'bibYear');
    Markup('bibtexbrowserel',     '<split','/\\(:bibyear ([-+]?[0-9]{1,2}):\\)/',       'bibYearRel');
    Markup('bibtexbrowselast',    '<split','/\\(:biblast (\\S+) ([0-9]{1,2}):\\)/',     'bibLast');
    Markup('bibtexbrowseauthor',  '<split','/\\(:bibauth (\\S+):\\)/',                  'bibAuthor');
    Markup('bibtexbrowseeditor',  '<split','/\\(:bibedit (\\S+):\\)/',                  'bibEditor');
    Markup('bibtexbrowseauthtype','<split','/\\(:bibauth (\\S+) (\\S+):\\)/',           'bibAuthor');
    Markup('bibtexbrowsetype',    '<split','/\\(:bibtype (\\S+):\\)/',                  'bibType');
    Markup('bibtexbrowser',       '<split','/\\(:bib (.*):\\)/',                        'bibQuery');
}

if (!function_exists('bibBrowsePrint')) {
    function bibBrowsePrint($entries)
    {
        return BIBBROWSE_HTML_PREFIX_LIST
            . implode(
                BIBBROWSE_HTML_SEPARATOR,
                array_map(function ($entry) {
                    return BIBBROWSE_HTML_PREFIX_ENTRY . $entry->toHTML() . BIBBROWSE_HTML_POSTFIX_ENTRY;
                }, $entries)
            )
            . BIBBROWSE_HTML_POSTFIX_LIST;
    }
}

function bibBrowseAcademicOrder($a, $b)
{
    $r = (($a->getYear() < $b->getYear()) ? 1 : (($a->getYear() > $b->getYear()) ? -1 : 0));
    if ($r == 0) $r = ((getTypeWeight($a) < getTypeWeight($b)) ? -1 : ((getTypeWeight($a) > getTypeWeight($b)) ? 1 : 0));
    if ($r == 0) $r = ((getMonthWeight($a) < getMonthWeight($b)) ? 1 : ((getMonthWeight($a) > getMonthWeight($b)) ? -1 : 0));
    if ($r == 0) $r = ((getDayWeight($a) < getDayWeight($b)) ? 1 : ((getDayWeight($a) > getDayWeight($b)) ? -1 :0));
    if ($r == 0) $r = strcmp($a->getFormattedAuthorsString(), $b->getFormattedAuthorsString());

    return $r;
}

function getTypeWeight($e) {
    return BIBBROWSE_TYPE_WEIGHTS[$e->getType()] ?? BIBBROWSE_DEFAULT_WEIGHT;
}

function getMonthWeight($e) {
    return BIBBROWSE_MONTH_WEIGHTS[strtolower($e->getField('Month'))] ?? 0;
}

function getDayWeight($e) {
    return ($e->hasField('Day') ? intval($e->getField('Day')) : 0);
}

function loadBib(string $bib) {
    global $db;

    $db->load($bib);
}

function bibYear($m) {
    $entries = search(array('year'=>$m[1]));
    return Keep(bibBrowsePrint($entries));
}

function bibYearRel($m) {
    $entries = search(array('year'=>date("Y") + $m[1]));
    return Keep(bibBrowsePrint($entries));
}

function bibLast($m) {
    $entries = search(array('author'=>$m[1]));
    $entries = array_slice($entries, 0, $m[2]);
    return Keep(bibBrowsePrint($entries));
}

function bibAuthor($m) {
    $entries = search(array('author'=>$m[1], 'type'=>(isset($m[2]) ? $m[2] : '.*')));
    return Keep(bibBrowsePrint($entries));
}

function bibEditor($m) {
    $entries = search(array('editor'=>$m[1], 'type'=>(isset($m[2]) ? $m[2] : '.*')));
    return Keep(bibBrowsePrint($entries));
}

function bibType($m) {
    $entries = search(array('type'=>$m[1]));
    return Keep(bibBrowsePrint($entries));
}

function bibQuery($m) {
    $entries = search(query($m));
    return Keep(bibBrowsePrint($entries));
}

function query($m) {
    preg_match_all("/\s*([a-z]+)\s*=\s*\"([^\"]+)\"/i", $m[1], $query);
    $query = array_combine($query[1], $query[2]);

    $yearOffset = [];
    if (isset($query['year']) && preg_match('/(\s*[\+|\-]\d+)/', $query['year'], $yearOffset)) {
        $query['year'] = date("Y") + $yearOffset[0];
    }

    return $query;
}

function search($query) {
    global $db;

    $entries = $db->multisearch($query);
    $sort = BIBBROWSE_SORT[$query['sort'] ?? null] ?? BIBBROWSE_DEFAULT_SORT;
    uasort($entries, $sort);

    return $entries;
}
