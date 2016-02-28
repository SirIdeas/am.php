<?php

class Main extends AmController{

  public function action_index(){
    
  }

  public function action_model(){


    // var_dump(Am::loadAllClasses());

    // $this->addHeader('content-type:text/plain');
     
    // // ----------------------------------------------------------------------
    // // Creación eliminación y generación del modelo
    // $sch = AmScheme::get();
    // var_dump($sch->getTables());
    // var_dump($sch->generateScheme());
    // var_dump($sch->getGeneratedModels());
    // var_dump($sch->drop());
    // var_dump($sch->create());

    // //-----------------------------------------------------------------------
    // // Crear tabla sin model
    // $tbl = AmScheme::table('usuarios')
    //   ->addField('id_user', 'id')
    //   ->addField('name', 'varchar')
    //   ->addField('sueldo', 'float')
    //   ->addCreatedAtField()
    //   ->addUpdatedAtField()
    //   ;
    // var_dump($tbl->drop());
    // var_dump($tbl->create());
    
    // //-----------------------------------------------------------------------
    // // Crear tabla apartir del modelo
    // $tbl = Usuario::me();
    // var_dump($tbl->drop());
    // var_dump($tbl->create());
    // var_dump($tbl->truncate());

    // //-----------------------------------------------------------------------
    // // Buscar registro en el modelo
    // $u = Usuario::me()->find(2);
    // var_dump($u);

    //-----------------------------------------------------------------------
    // Crear usuario 
    $u = new Usuario;

    $u->name .= 'a';
    $u->sueldo = 120.0;

    var_dump($u->save());
    var_dump($u->toArray());
    var_dump($u->getErrors());
    var_dump(Usuario::all()->get('array'));

  }

}