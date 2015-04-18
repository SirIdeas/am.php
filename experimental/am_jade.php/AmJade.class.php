<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/
 
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
