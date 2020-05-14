# bibbrowser
A bibliography recipe for PmWiki

# Installation
  * Download `bibtexbrowser.php` from the [BibTexBrowser GitHub](https://github.com/monperrus/bibtexbrowser)
  * Place `bibbrowser.php` and `bibtexbrowser.php` in PmWiki's cookbook directory

# Setup
To use this plugin, it must be enabled in PmWiki's `local/config.php` file. It can be enabled by including the following lines:
```
$BibtexBib = 'path/to/your/bib.bib';
include_once('cookbook/bibbrowser.php');
```

# Usage
Below is the list of available commands. Remember to always put a line feed after each command.

```
(:bibyear year:)
```
Displays bibentries from the year `year`. For example, `(:bibyear 2020:)` displays all entries from the year 2020.

```
(:bibyear offset:)
```
Displays bibentries from the current year plus `offset`. For example, `(:bibyear -1:)` displays all entries from last year.

```
(:bibtype type:)
```
Displays bibentries of the type `type`. For example, `(:bibauth book:)` displays all books.

```
(:bibauth author type:)
```
Displays bibentries by `author` and the optional parameter `type`. For example, `(:bibauth Doe phdthesis:)` displays the PhD thesis of Doe.

```
(:biblast author number:)
```
Displays `number` of bibentries by `author`. For example, `(:bibylast Doe 5:)` displays five entries by Doe. Note that entries are sorted by the order of the bib file.
