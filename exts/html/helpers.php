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

function isResourceAllow($url){
  $resourcesNotAllow = itemOr('resourcesNotAllow',
    Am::getAttribute('env', array()), array());

  return !in_array($url, $resourcesNotAllow);
  
}

function titleTag($title){
  return HTML::t('title', $title);
}

function eTitleTag($title){
  return titleTag($title);
}

function metaTag($name, $content){
  return HTML::t('meta', null, array(
    'name' => $name,
    'content' => $content
  ));
}

function eMetaTag($name, $content){
  echo metaTag($name, $content);
}

function scriptTag($url){
  return HTML::t('script', null, array('src' => Am::url($url)));
}

function eScriptTag($url, $cond = null){
  if(isset($cond)) echo "<!--[if $cond]>";
  echo scriptTag($url);
  if(isset($cond)) echo '<![endif]-->';
}

function eScriptTagIfIsAllow($url, $cond = null){
  if(isResourceAllow($url)) eScriptTag($url, $cond);
}

function linkTag($url, $rel){
  return HTML::t('link', null, array(
    'rel' => $rel,
    'href' => Am::url($url))
  );
}

function eLinkTag($url, $rel){
  echo linkTag($url, $rel);
}

function styleTag($url){
  return linkTag($url, 'stylesheet');
}

function eStyleTag($url, $cond = null){
  if(isset($cond)) echo "<!--[if $cond]>";
  echo styleTag($url);
  if(isset($cond)) echo '<![endif]-->';
}

function eStyleTagIfIsAllow($url, $cond = null){
  if(isResourceAllow($url)) eStyleTag($url, $cond);
}