<?php

class Main extends AmController{

  public function action_index(){
    
  }

  public function action_model(){

    // $this->addHeader('content-type:text/plain');

    // var_dump(['Am::loadAllClasses()' => Am::loadAllClasses()]);
     
    // ----------------------------------------------------------------------
    // Creación eliminación y generación del modelo
    // $sch = AmScheme::get();
    // var_dump(['$sch->getTables()' => $sch->getTables()]);
    // var_dump(['$sch->generateScheme()' => $sch->generateScheme()]);
    // var_dump(['$sch->getGeneratedModels()' => $sch->getGeneratedModels()]);
    // var_dump(['$sch->drop()' => $sch->drop()]);
    // var_dump(['$sch->create()' => $sch->create()]);

    //-----------------------------------------------------------------------
    // Crear tabla sin model
    // $tbl = AmScheme::table('persons')
    //   ->addField('id_user', 'id')
    //   ->addField('name', 'varchar')
    //   ->addField('sueldo', 'float')
    //   ->addCreatedAtField()
    //   ->addUpdatedAtField()
    //   ;
    // var_dump(['$tbl->drop()' => $tbl->drop()]);
    // var_dump(['$tbl->create()' => $tbl->create()]);
    
    //-----------------------------------------------------------------------
    // Crear tabla apartir del modelo
    // Person::drop();
    // Person::create();
    // Person::truncate();

    //-----------------------------------------------------------------------
    // Buscar registro en el modelo
    // $p = Person::find(2);
    // var_dump($u);

    //-----------------------------------------------------------------------
    // Crear usuario
    $p = new Person;
    $p->age = 26;
    $p->height = 1.80;
    $p->born_date = '1989/04/04';
    $p->register_date = '2000/02/02 05:45:02';
    $p->last_session = '2016/03/03 19:12:45';
    $p->check_in = null;
    $p->dni = 'V18667932';
    $p->name = 'Alex J. Rondon';
    $p->email = 'arondn2@gmail.com';
    $p->marriage_year = 1901;
    $p->set('permissions', "b'1001'", true);
    $p->children = 3;
    var_dump(array(
      // 'drop' => Person::drop(),
      // 'create' => Person::create(),
      'save' => $p->save(),
      // 'errors' => $p->getErrors(),
      'save' => $p->toArray(),
      // 'all' => Person::all()->get('array')
    ));

    //-----------------------------------------------------------------------
    // Depatament
    $d = new Departament;
    $d->id_person = $p->id_person;
    var_dump(array(
      // 'drop' => Departament::drop(),
      // 'create' => Departament::create(),
      'save' => $d->save(),
      // 'errors' => $d->getErrors(),
      'save' => $p->toArray(),
      // 'all' => Departament::all()->get('array')
    ));

    var_dump($d->chief()->row('array'));
    
  }

}