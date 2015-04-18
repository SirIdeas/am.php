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
 
class AmRenderizeFilter{
 protected
   $reg = null;

 public function match($line){
   if(preg_match($this->reg, $line, $matches)){
     array_shift($matches);
     return $matches;
   }
   return false;
 }

}

class AmRenderizeFilterBlockPHP extends AmRenderizeFilter{
  protected
    $reg = "/^:php/";

  public function open($ident){
    $out[] = $ident."<?php";
    $out[] = $ident."?>";
  }

}

class AmRenderizeFilterInlinePHP extends AmRenderizeFilter{
 protected
   $reg = "/^:php/";
}

class AmRenderizeFilterBlock extends AmRenderizeFilter{
 protected
   $reg = "/^block(|\.extend)(| ([^ ]+))/";
}

class AmRenderizeFilterInclude extends AmRenderizeFilter{
 protected
   $reg = "/^include [\'\"]([^ ]+)[\'\"]/";
}

class AmRenderizeFilterExtend extends AmRenderizeFilter{
 protected
   $reg = "/^extend [\'\"]([^ ]+)[\'\"]/";
}

class AmRenderizeFilterJS extends AmRenderizeFilter{
 protected
   $reg = "/^\:js/";
}

class AmRenderizeFilterCSS extends AmRenderizeFilter{
 protected
   $reg = "/^\:css/";
}

class AmRenderizeFilterTag extends AmRenderizeFilter{
 protected
   $reg = "/^([\w\W]*)(|#[\w\W0-9_\-]+)()(.*)/";
  //  $reg = "/^([\w\W]*)(|#[\w\W0-9_\-]+|\.[\w\W0-9_\-]+|\(([^=]+)=([^=]+)\))(.*)/";
}
