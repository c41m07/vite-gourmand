<?php


$finder = new TwigCsFixer\File\Finder()
    ->in(['templates']);

// Créez un Ruleset personnalisé sans la règle qui modifie l'indentation des commentaires
$ruleset = new TwigCsFixer\Ruleset\Ruleset()
    ->removeRule('CommentIndentation')
    ->overrideRule(
        new TwigCsFixer\Rules\Punctuation\PunctuationSpacingRule(
            ['}' => 0, ':' => 0, '}}' => 1],
            ['{' => 0, ':' => 1, '{{' => 1],
        ),
    );

$config = new TwigCsFixer\Config\Config()
    ->setFinder($finder)
    // Appliquez le Ruleset personnalisé à la configuration
    ->setRuleset($ruleset)
    ->setCacheFile(null);

return $config;
