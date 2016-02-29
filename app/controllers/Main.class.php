<?php

class Main extends AmController{

  public function action_index(){
    
  }

  public function action_model(){


    // var_dump(Am::loadAllClasses());

    // $this->addHeader('content-type:text/plain');
     
    // ----------------------------------------------------------------------
    // Creación eliminación y generación del modelo
    // $sch = AmScheme::get();
    // var_dump($sch->getTables());
    // var_dump($sch->generateScheme());
    // var_dump($sch->getGeneratedModels());
    // var_dump($sch->drop());
    // var_dump($sch->create());

    //-----------------------------------------------------------------------
    // Crear tabla sin model
    // $tbl = AmScheme::table('usuarios')
    //   ->addField('id_user', 'id')
    //   ->addField('name', 'varchar')
    //   ->addField('sueldo', 'float')
    //   ->addCreatedAtField()
    //   ->addUpdatedAtField()
    //   ;
    // var_dump($tbl->drop());
    // var_dump($tbl->create());
    
    //-----------------------------------------------------------------------
    // Crear tabla apartir del modelo
    // var_dump(Usuario::drop());
    // var_dump(Usuario::create());
    // var_dump(Usuario::truncate());

    //-----------------------------------------------------------------------
    // Buscar registro en el modelo
    // $u = Usuario::find(2);
    // var_dump($u);

    //-----------------------------------------------------------------------
    // Crear usuario
    // $u = new Usuario;

    // $u->name .= 'a';
    // $u->sueldo = 120.0;

    // var_dump($u->save());
    // var_dump($u->toArray());
    // var_dump($u->getErrors());
    // var_dump(Usuario::all()->get('array'));

  }

}