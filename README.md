[![Build Status](https://travis-ci.org/CMS-Garden/cmsscanner.svg?branch=develop)](https://travis-ci.org/CMS-Garden/cmsscanner)

# CMS-Garden CMSScanner

This tool is developed by the CMS-Garden project. It's designed to scan a local filesystem for installations of the well known FOSS CMS systems that are part of the [CMS-Garden Project](https://www.cms-garden.org/):

* Contao
* CONTENIDO
* Drupal
* Joomla
* TYPO3 CMS
* WordPress
* Prestashop
* Alchemy CMS
* PivotX
* Concrete5

It is designed to work on Linux, OS X and FreeBSD.

For available options, try running:

	cmsgarden.phar --list

## Installation instructions

1. Download the current version of CMSScanner as a phar archive:

	`wget https://cms-garden.github.io/cmsscanner/downloads/cmsscanner-0.5.0.phar && mv cmsscanner-0.5.0.phar cmsscanner.phar`

2. Make it executable

	`chmod +x cmsscanner.phar`

3. OPTIONAL: Configure your system to recognize where the executable resides. There are 3 options:
	1. Create a symbolic link in a directory that is already in your PATH, e.g.:

		`$ ln -s /path/to/cmsscanner/cmsscanner.phar /usr/bin/cmsscanner.phar`

	2. Explicitly add the executable to the PATH variable which is defined in the the shell configuration file called .profile, .bash_profile, .bash_aliases, or .bashrc that is located in your home folder, i.e.:

		`export PATH="$PATH:/path/to/cmsscanner:/usr/local/bin"`

	3. Add an alias for the executable by adding this to you shell configuration file (see list in previous option):

		`$ alias cmsscanner.phar=/path/to/cmsscanner/cmsscanner.phar`

		For options 2 and 3 above, you should log out and then back in to apply your changes to your current session.

4. OPTIONAL: Test that scanner executable is found by your system:

	`$ which scanner.phar`

## Using the scanner

You can start scanning for CMS installations by calling the phar, followed by the detection command and a path to scan in:

	cmsscanner.phar cmsscanner:detect /var/www

Depending on the amount of files and folders in this path, a scan take quite a while. After the scan is over, the tool will give you a summary of the results:

	machine:root$ ./bin/cmsscanner cmsscanner:detect /var/www
	Successfully finished scan!
	CMSScanner found 5 CMS installations!

	+--------+-----------------+
	| CMS    | # Installations |
	+--------+-----------------+
	| Joomla | 2               |
	| Drupal | 3               |
	+--------+-----------------+

It's also possible to pass multiple paths to the scanner:

	cmsscanner.phar cmsscanner:detect /var/www/docroot1 /var/www/docroot2

## Options

### Detect used versions:

	cmsscanner.phar cmsscanner:detect --versions /var/www

Output:

	Successfully finished scan!
	CMSScanner found 5 CMS installations!

	+--------+-----------------+
	| CMS    | # Installations |
	+--------+-----------------+
	| Joomla | 2               |
	| Drupal | 3               |
	+--------+-----------------+

	Version specific stats:
	Joomla:
	+---------+-----------------+
	| Version | # Installations |
	+---------+-----------------+
	| 3.6.5   | 2               |
	+---------+-----------------+
	Drupal:
	+---------+-----------------+
	| Version | # Installations |
	+---------+-----------------+
	| Unknown | 1               |
	| 8.2.7   | 2               |
	+---------+-----------------+

### Detect used modules/extensions:

	cmsscanner.phar cmsscanner:detect --modules /var/www

Output:

	Successfully finished scan!
	CMSScanner found 56 CMS installations!

	+------------+-----------------+-----------+
	| CMS        | # Installations | # Modules |
	+------------+-----------------+-----------+
	| Joomla     | 29              | 131       |
	| Prestashop | 1               | 0         |
	| Contao     | 4               | 7         |
	| WordPress  | 7               | 2         |
	| TYPO3 CMS  | 3               | 1         |
	| Drupal     | 4               | 8         |
	| Contenido  | 8               | 0         |
	+------------+-----------------+-----------+

	Module specific stats:
	Joomla:
	+------------------------------------+-----------------+
	| Module                             | # Installations |
	+------------------------------------+-----------------+
	| JSN_UNIFORM_PLUGIN_BUTTON_TITLE    | 1               |
	| JSN_UNIFORM_PLUGIN_CONTENT_TITLE   | 1               |
	| JSN ImageShow Quick Icons          | 1               |
	| PLG_SYSTEM_AKEEBAUPDATECHECK_TITLE | 3               |
	| PLG_SYSTEM_BACKUPONUPDATE_TITLE    | 3               |
	| Content - JSN ImageShow            | 1               |
	...

### Limit recursion depth
By using the --depth options, it's possible to limit the recursion depth of the scan. This will increase the performance but decrease the accuracy of the scan:

	cmsscanner.phar cmsscanner:detect --depth=3 /var/www

### Output a JSON report
If you want to use the scan results for something else, you can export them as a JSON report:

	cmsscanner.phar cmsscanner:detect --report=/tmp/cmsreport.json --versions /var/www

This results in a report file like this:

	[
	   {
		  "name":"Drupal",
		  "version":"5.23",
		  "path":"\/var\/www\/drupal-5.23"
	   },
	   {
		  "name":"Drupal",
		  "version":"6.14",
		  "path":"\/var\/www\/drupal-6.14"
	   }
	]


### Detect used modules and append them to report:

	cmsscanner.phar cmsscanner:detect --report=/tmp/cmsreport.json --versions --modules /var/www

Output:

	[
	   {
		  "name":"Joomla",
		  "version":"3.4.6",
		  "path":"\/var\/www\/joomla",
		  "modules":[
			 {
				"name":"mod_articles_archive",
				"version":"3.0.0",
				"path":"\/var\/www\/joomla\/modules\/mod_articles_archive",
				"type":"module"
			 }
		  ]
		}
	]

### Read paths from an input file
It's also possible to pass a file that contains a 0-byte separated list of paths:

	cmsscanner.phar cmsscanner:detect --readfromfile /absolute/path/to/file

## Developer Information

### Run the tests

#### set up the repo
- Install composer (if not done yet)
- cd into the cloned repository
- run `composer install` to install the dependencies

#### Run the tests

Run the PHP Unit tests

```
composer php:test
```

Run the PHPCS tests

```
composer php:cs
```

#### Build the phar

* grab `box.phar`: https://github.com/humbug/box or run `phive install
* ensure that in your `php.ini` for CLI you have set `phar.readonly` to off.
* run compile to build:
  ```
  box compile # via phive tools/box compile
  ```

## Prebuilt Packages (unofficial)

- [Archlinux](https://aur.archlinux.org/packages/cmsscanner) by @sanduhrs
