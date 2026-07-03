<?php

require_once __DIR__.'/vendor/autoload.php';

use App\Modules\Listings\Services\Parsers\OpenGraphParser;

$parser = new OpenGraphParser;

$html = '<html><head><meta property="og:title" content="Test Car"><meta property="og:description" content="Test Description"><meta property="og:image" content="https://example.com/image.jpg"></head></html>';

var_dump('canParse result:', $parser->canParse($html));
var_dump('parse result:', $parser->parse($html));
