[![Build Status](https://travis-ci.org/CMS-Garden/cmsscanner.svg?branch=develop)](https://travis-ci.org/CMS-Garden/cmsscanner)

# CMS-Garden CMSScanner

This tool is developed by the CMS-Garden project. It's designed to scan a local filesystem for installations of the well known FOSS CMS systems that are part of the [CMS-Garden Project](http://www.cms-garden.org/):

* Contao
* Drupal
* Joomla
* TYPO3 CMS
* WordPress
* Prestashop
* PivotX

It is designed to work on Linux, OS X and FreeBSD.

For available options, try running:

	cmsgarden.phar --list

## Installation instructions

1. Download the current version of CMSScanner as a phar archive:

	`wget http://cms-garden.github.io/cmsscanner/downloads/cmsscanner-0.4.0.phar && mv cmsscanner-0.4.0.phar cmsscanner.phar`

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
	| Drupal | 3               |
	+--------+-----------------+

	Version specific stats:
	Drupal:
	+---------+-----------------+
	| Version | # Installations |
	+---------+-----------------+
	| Unknown | 0               |
	| 5.23    | 1               |
	| 6.33    | 1               |
	| 7.32    | 1               |
	+---------+-----------------+

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

### Read paths from an input file
It's also possible to pass a file that contains a 0-byte separated list of paths:

	cmsscanner.phar cmsscanner:detect --readfromfile /absolute/path/to/file
