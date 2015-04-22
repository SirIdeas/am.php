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

$signinForm = new HTMLForm(
  array(
    "attrs" => array(
      "role" => "form",
      "name" => "signin",
      "onsubmit" => "return amathista.submit(this)"
    ),
    "defaults"  => array(
      "label"   => HTML::t("label"),
      "wrapper" => HTML::t("div", null, array(
        "class" => "validate"
      )),
      "required" => ""
    ),
    "head" => HTML::t("h5", "Inicio de sesión"),
    "fields" => array(
      "username" => array(
        "name"        => "username",
        "label"       => "Username",
        "type"        => "text",
        "placeholder" => "Username",
        "autofocus"   => ""
      ),
      "password" => array(
        "name"        => "password",
        "label"       => "Password",
        "type"        => "password",
        "placeholder" => "Password",
      ),
    ),
    "foot" => HTML::t("div",
      array(
        HTML::t("a","Registro", array("href" => $url."signin")),
        HTML::t("a","¿Olvidó su contraseña?", array("href" => "recovery")),
        HTML::t("button", "Enviar", array("type" => "submit"))
      )
    )
  )
);

?>
