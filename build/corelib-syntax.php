<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Gacela\Framework\Gacela;
use Phel\Api\Transfer\PhelFunction;
use Phel\Phel;
use PhelDocBuild\FileGenerator\Facade as FileGeneratorFacade;

Gacela::bootstrap(__DIR__, Phel::configFn());

//======================================================================================
// Utility to update the phel syntax highlight defined in `syntaxes/phel.sublime-syntax`
//======================================================================================

$facade = new FileGeneratorFacade();
$allPhelFunctions = $facade->getAllPhelFunctions();

$names = array_map(fn(PhelFunction $fn) => str_replace(
    ['/', '*', '+', '?'],
    ['//', '\\\\*', '\\\\+', '\\\\?'],
    $fn->fnName()
), $allPhelFunctions);

$coreFunctions = implode('|', $names);

$macros = 'defstruct|definterface|defmacro-|defmacro|defn-|defn|def-|def|recur|fn|throw|try|catch|finally|loop|ns|quote|dofor|do|if-not|if|when-not|when|apply|let|foreach|for|case|cond|list|vector|hash-map|set';

$template = <<<'EOT'
(?<=\\(|\\{|\\[|\\s)({{MACROS}}|{{CORE_FUNCTIONS}})(?=\\s|\\)|\\]|\\})
EOT;

$result = str_replace(
    ['{{CORE_FUNCTIONS}}', '{{MACROS}}'],
    [$coreFunctions, $macros],
    $template
);

# Add the regex result to the `syntaxes/phel.sublime-syntax` file in `corelib.match` section
echo $result . PHP_EOL;
