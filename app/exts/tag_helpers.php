<?php

class AmTag{
	function tag($tag, array $attrs = array(), $content = null){

		foreach($attrs as $attr => $val)
			$attrs[$attr] = '"'.$attr.'"="'.$val.'"';

		$attrsStr = implode(" ", $attrs);

		if(isset($content))
			return "<{$tag} $attrsStr>".((string)$content)."</{$tag}>";

		return "<{$tag} $attrsStr ></{$tag}>";


	}

}
