<?php
$year = date('Y');
$header = <<<EOF
@package    CMSScanner
@copyright  Copyright (C) 2014 - $year CMS-Garden.org
@license    MIT <https://tldrlegal.com/license/mit-license>
@link       https://www.cms-garden.org
EOF;

$finder = PhpCsFixer\Finder::create()
    ->exclude('tests/mockfiles')
    ->exclude('vendor')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
$config->setUsingCache(FALSE);
$config->setRules(array(
    'header_comment' => array(
      'header' => $header,
      'comment_type' => 'PHPDoc',
      'location' => 'after_open',
      'separate' => 'bottom',
    )
  ));
$config->setFinder($finder);

return $config;
