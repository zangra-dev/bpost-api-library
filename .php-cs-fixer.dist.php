<?php

use Symfony\Component\Finder\Finder;

$config = new PhpCsFixer\Config();
$rules = array(
    '@Symfony' => true,
    'visibility_required' => false, // PHP7.1+
    'array_syntax' => false, // PHP5.4+
    'phpdoc_summary' => false,
    'phpdoc_annotation_without_dot' => false,
    'no_superfluous_phpdoc_tags' => false,
    'yoda_style' => false,
    'concat_space' => array('spacing' => 'one'),
    'single_line_throw' => false,
    'phpdoc_to_comment' => false,
    'global_namespace_import' => true,
);
$config->setRules($rules);

/** @var Finder $finder */
$finder = $config
    ->setUsingCache(true)
    ->getFinder();

$finder
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->notName('tijsverkoyen_classes.php')
    ->sortByName();

return $config;
