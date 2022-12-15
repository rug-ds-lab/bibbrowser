<?php

define('BIBLIOGRAPHYSTYLE','DSBibliographyStyle');
define('BIBTEXBROWSER_LINK_STYLE','DSBib2links');


function DsBibliographyStyle($bibentry) {
  $title = $bibentry->getTitle();
  $type = $bibentry->getType();

  // later on, all values of $entry will be joined by a comma
  $entry=array();

  // title
  // usually in bold: .bibtitle { font-weight:bold; }
  $title = '<span class="bibtitle"  itemprop="name">'.$title.'</span>';
  if ($bibentry->hasField('doi')) $title = ' <a target="_blank" href="https://doi.org/'.$bibentry->getField('doi').'">'.$title.'</a>';
  elseif ($bibentry->hasField('url')) $title = ' <a target="_blank" href="'.$bibentry->getField('url').'">'.$title.'</a>';

  $coreInfo = $title;

  // adding author info
  if ($bibentry->hasField('author')) {
    $coreInfo .= ' (<span class="bibauthor">';

    $authors = array();
    foreach ($bibentry->getFormattedAuthorsArray() as $a) {
       $authors[]='<span itemprop="author" itemtype="http://schema.org/Person">'.$a.'</span>';
    }
    $coreInfo .= $bibentry->implodeAuthors($authors);

    $coreInfo .= '</span>)';
  }

  // core info usually contains title + author
  $entry[] = $coreInfo;

  // now the book title
  $booktitle = '';
  if ($type=="inproceedings" || $type=="conference") {
      $booktitle = __('In').' '.'<span itemprop="isPartOf">'.$bibentry->getField(BOOKTITLE).'</span>'; }
  if ($type=="incollection") {
      $booktitle = __('Chapter in').' '.'<span itemprop="isPartOf">'.$bibentry->getField(BOOKTITLE).'</span>';}
  if ($type=="inbook") {
      $booktitle = __('Chapter in').' '.$bibentry->getField('chapter');}
  if ($type=="article") {
      $booktitle = __('In').' '.'<span itemprop="isPartOf">'.$bibentry->getField("journal").'</span>';}

  //// we may add the editor names to the booktitle
  $editor='';
  if ($bibentry->hasField(EDITOR)) {
    $editor = $bibentry->getFormattedEditors();
  }
  if ($editor!='') $booktitle .=' ('.$editor.')';
  // end editor section

  // is the booktitle available
  if ($booktitle!='') {
    $entry[] = '<span class="bibbooktitle">'.$booktitle.'</span>';
  }


  $publisher='';
  if ($type=="phdthesis") {
      $publisher = __('PhD thesis').', '.$bibentry->getField(SCHOOL);
  }
  if ($type=="mastersthesis") {
      $publisher = __('Master\'s thesis').', '.$bibentry->getField(SCHOOL);
  }
  if ($type=="bachelorsthesis") {
      $publisher = __('Bachelor\'s thesis').', '.$bibentry->getField(SCHOOL);
  }
  if ($type=="techreport") {
      $publisher = __('Technical report');
      if ($bibentry->hasField("number")) {
          $publisher .= ' '.$bibentry->getField("number");
      }
      $publisher .= ', '.$bibentry->getField("institution");
  }

  if ($type=="misc") {
      $publisher = $bibentry->getField('howpublished');
  }

  if ($bibentry->hasField("publisher")) {
    $publisher = $bibentry->getField("publisher");
  }

  if ($publisher!='') $entry[] = '<span class="bibpublisher">'.$publisher.'</span>';


  if ($bibentry->hasField('volume')) $entry[] =  __('volume').' '.$bibentry->getField("volume");


  if ($bibentry->hasField(YEAR)) $entry[] = '<span itemprop="datePublished">'.$bibentry->getYear().'</span>';

  $bibid = str_replace(":","-",$bibentry->getKey());
  $collapse = '<a class="bibbtn collapsed" data-toggle="collapse" href="#bibentry'.$bibid.'" role="button" aria-expanded="false" aria-controls="bibentry'.$bibid.'"></a>';

  $collapse .= '<div id="bibentry'.str_replace(":","-",$bibentry->getKey()).'" class="collapse"><div class="card"><div class="card-body">';

  if ($bibentry->hasField('abstract')) {
      $collapse .= '<h3 class="card-title">Abstract</h3>';
      $collapse .= '<p class="card-text bibabstract">'.$bibentry->getField('abstract').'</p>';
	  if ($bibentry->hasField('keywords'))
	  	  $collapse .= '<p class="card-text bibkeywords"><br/><b class="bibkeyword">Keywords:</b> '.implode(", ", $bibentry->getKeywords()).'</p>';
	  $collapse .= '<hr>';
  }

  $collapse .= '<button class="btn btn-light bibcopy" data-clipboard-target="#ta-'.$bibid.'"></button>';
  $collapse .= '<h3 class="card-title">BibTeX</h3></br>';
  $collapse .= '<textarea id="ta-'.$bibid.'" class="bibtex">'.$bibentry->getText().'</textarea>';
  $collapse .= '<hr>';

  if ($bibentry->hasField('url'))
      $collapse .= '<a'.get_target().' class="card-link biblink" target="_blank" href="'.$bibentry->getField('url').'">url</a>';
  if ($bibentry->hasField('pdf'))
      $collapse .= '<a'.get_target().' class="card-link biblink" target="_blank" href="'.$bibentry->getField('pdf').'">pdf</a>';
  if ($bibentry->hasField('file'))
      $collapse .= '<a'.get_target().' class="card-link biblink" target="_blank" href="'.$bibentry->getField('file').'">file</a>';
  if ($bibentry->hasField('doi'))
      $collapse .= '<a'.get_target().' class="card-link biblink" target="_blank" href="https://doi.org/'.$bibentry->getField('doi').'">doi</a>';
  if ($bibentry->hasField('gsid'))
      $collapse .= '<a'.get_target().' class="card-link biblink" target="_blank" href="https://scholar.google.com/scholar?cites='.$bibentry->getField('gsid').'">scholar</a>';

  $collapse .= '</div></div></div>';

  $result = implode(", ",$entry).'.';
  
  $result .= $collapse;

  // add the Coin URL
  $result .=  $bibentry->toCoins();

  return '<span itemscope="" itemtype="http://schema.org/ScholarlyArticle">'.$result.'</span>';
}

function DSBib2links(&$bibentry) {
  return '';
}

?>