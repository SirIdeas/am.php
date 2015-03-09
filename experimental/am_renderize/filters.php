<?php

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
