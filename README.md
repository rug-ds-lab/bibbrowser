# bibbrowser
A bibliography recipe for PmWiki

# Installation
  * Download `bibtexbrowser.php` from the [BibTexBrowser GitHub](https://github.com/monperrus/bibtexbrowser)
  * Place `bibbrowser.php` and `bibtexbrowser.php` in PmWiki's cookbook directory

# Setup
To use this plugin, it must be enabled in PmWiki's `local/config.php` file. It can be enabled by including the following lines:
```
include_once('cookbook/bibbrowser.php');
BibLoad('path/to/your/bib.bib');
```

# Usage
Below is the list of available commands. Remember to always put a line feed after each command.

```
(:bib field="value" field2="value" ... limit="x":)
```

Displays an x number of bibentries with values for the given fields, e.g., `(:bib author="Doe" type="Book" year="2000":) ` list books from the year 2000 by Doe.
Limit is optional.


## Depricated

The below commands are depricated but maintained for compatibility.

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
Displays `number` of bibentries by `author`. For example, `(:bibylast Doe 5:)` displays five entries by Doe.

# Sorting
Sorting is automatic by year, type (academic order), month, and finally author.
