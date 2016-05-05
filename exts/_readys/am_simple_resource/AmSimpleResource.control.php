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

class AmSimpleResourceControl extends AmResourceControl{

  // Accion para mostrar el listado de un modelo
  public function action_index(){}
  public function get_index(){}
  public function post_index(){}

  // Acción para agregar un nuevo usuario
  public function action_new(){
    $this->r = new $this->classModel;
  }
  // Procesamiento del formulario new
  public function get_new($id){}
  public function post_new(){
    self::handleActionSimple(
      parent::post_new(),
      'Registro agregado satisfactoriamente',
      'Errores al intentar agregar el registro'
    );
  }

  // Accion para modificar los datos del registro
  public function action_edit($id){
    if(method_exists($this, 'format_edit'))
      $this->r = $this->format_edit($this->r);
  }
  public function get_edit($id){}
  public function post_edit($id){
    $ret = parent::post_edit();
    self::handleActionSimple(
      parent::post_edit(),
      'Registro actualizado satisfactoriamente',
      'Errores al intentar actualizar el registro'
    );
  }

  // Accion para eliminar un registro
  public function action_delete($id){}
  public function get_delete($id){}
  public function post_delete($id){
    self::handleActionSimple(
      parent::post_new(),
      'Registro eliminado satisfactoriamente',
      'Errores al intentar eliminar el registro'
    );
  }

  // Crear o actualizar un registro
  public function action_cou($id){}
  public function get_cou($id){}
  public function post_cou($id){
    self::handleActionSimple(
      parent::post_cou(),
      'Registro creado/actualizado satisfactoriamente',
      'Errores al intentar creado/actualizado el registro'
    );
  }

  // Accion para mostrar el detalle de los registros
  public function action_detail($id){}
  public function get_detail($id){
    if(method_exists($this, 'format_detail'))
      $this->r = $this->format_detail($this->r);
  }

  private function handleActionSimple(array $ret, $msgSuccess, $msgFail){
    if($ret['success']){
      AmFlash::success($msgSuccess);
      $this->redirect();
    }else{
      AmFlash::danger($msgFail);
      $this->errors = $ret['errors'];
    }
  }

}
