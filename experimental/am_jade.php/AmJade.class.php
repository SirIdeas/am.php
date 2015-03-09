<?php

/**
 * Clase renderizar vistas
 */

 use Everzet\Jade\Dumper\PHPDumper,
     Everzet\Jade\Visitor\AutotagsVisitor,
     Everzet\Jade\Filter\JavaScriptFilter,
     Everzet\Jade\Filter\CDATAFilter,
     Everzet\Jade\Filter\PHPFilter,
     Everzet\Jade\Filter\CSSFilter,
     Everzet\Jade\Parser,
     Everzet\Jade\Lexer\Lexer,
     Everzet\Jade\Jade;

final class AmJade extends AmObject{

  public static function renderize($file, $paths, $options = array()){

    $dumper = new PHPDumper();
    $dumper->registerVisitor('tag', new AutotagsVisitor());
    $dumper->registerFilter('js', new JavaScriptFilter());
    $dumper->registerFilter('cdata', new CDATAFilter());
    $dumper->registerFilter('php', new PHPFilter());
    $dumper->registerFilter('css', new CSSFilter());

    // Initialize parser & Jade
    $parser = new Parser(new Lexer());
    $jade   = new Jade($parser, $dumper);

    // Parse a template (both string & file containers)
    echo $jade->render($file);

  }

}
