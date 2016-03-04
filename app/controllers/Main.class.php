<?php

class Main extends AmController{

  public function action_index(){
    
  }

  public function action_model(){

    $this->addHeader('content-type:text/plain');


    // var_export(['Am::loadAllClasses()' => Am::loadAllClasses()]);
     
    // ----------------------------------------------------------------------
    // Creación eliminación y generación del modelo
    // $sch = AmScheme::get();
    // var_export(['$sch->getTables()' => $sch->getTables()]);
    // var_export(['$sch->generateScheme()' => $sch->generateScheme()]);
    // var_export(['$sch->getGeneratedModels()' => $sch->getGeneratedModels()]);
    // var_export(['$sch->drop()' => $sch->drop()]);
    // var_export(['$sch->create()' => $sch->create()]);

    //-----------------------------------------------------------------------
    // Crear tabla sin model
    // $tbl = AmScheme::table('persons')
    //   ->addField('id_user', 'id')
    //   ->addField('name', 'varchar')
    //   ->addField('sueldo', 'float')
    //   ->addCreatedAtField()
    //   ->addUpdatedAtField()
    //   ;
    // var_export(['$tbl->drop()' => $tbl->drop()]);
    // var_export(['$tbl->create()' => $tbl->create()]);
    
    //-----------------------------------------------------------------------
    // Crear tabla apartir del modelo
    var_export(['Person::drop' => Person::drop()]);
    var_export(['Person::create' => Person::create()]);
    var_export(['Person::truncate' => Person::truncate()]);

    //-----------------------------------------------------------------------
    // Buscar registro en el modelo
    // $u = Person::find(2);
    // var_export($u);

    //-----------------------------------------------------------------------
    // Crear usuario
    $u = new Person;

    $u->name .= 'a';
    $u->sueldo = 120.0;

    // var_export($u->save());
    // var_export($u->toArray());
    // var_export($u->getErrors());
    // var_export(Person::all()->get('array'));

  }

}