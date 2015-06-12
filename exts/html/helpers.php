<?php

function isResourceAllow($url){
  $resourcesNotAllow = itemOr("resourcesNotAllow",
    Am::getAttribute("env", array()), array());

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
    "name" => $name,
    "content" => $content
  ));
}

function eMetaTag($name, $content){
  echo metaTag($name, $content);
}

function scriptTag($url){
  return HTML::t('script', null, array("src" => Am::url($url)));
}

function eScriptTag($url, $cond = null){
  if(isset($cond)) echo "<!--[if $cond]>";
  echo scriptTag($url);
  if(isset($cond)) echo "<![endif]-->";
}

function eScriptTagIfIsAllow($url, $cond = null){
  if(isResourceAllow($url)) eScriptTag($url, $cond);
}

function linkTag($url, $rel){
  return HTML::t('link', null, array(
    "rel" => $rel,
    "href" => Am::url($url))
  );
}

function eLinkTag($url, $rel){
  echo linkTag($url, $rel);
}

function styleTag($url){
  return linkTag($url, "stylesheet");
}

function eStyleTag($url, $cond = null){
  if(isset($cond)) echo "<!--[if $cond]>";
  echo styleTag($url);
  if(isset($cond)) echo "<![endif]-->";
}

function eStyleTagIfIsAllow($url, $cond = null){
  if(isResourceAllow($url)) eStyleTag($url, $cond);
}