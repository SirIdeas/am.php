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
    // $p = new Person;
    // $p->age = 26;
    // $p->height = 1.80;
    // $p->born_date = '1989/04/04';
    // $p->register_date = '2000/02/02 05:45:02';
    // $p->last_session = '2016/03/03 19:12:45';
    // $p->check_in = null;
    // $p->dni = 'V18667932';
    // $p->name = 'Alex J. Rondon';
    // $p->email = 'arondn2@gmail.com';
    // $p->marriage_year = 1901;
    // $p->set('permissions', "b'1001'", true);
    // $p->children = 3;
    // var_dump(array(
    //   // 'drop' => Person::drop(),
    //   // 'create' => Person::create(),
    //   'save' => $p->save(),
    //   // 'errors' => $p->getErrors(),
    //   'array' => $p->toArray(),
    //   // 'all' => Person::all()->get('array')
    // ));

    // //-----------------------------------------------------------------------
    // // Depatament
    // $p = Person::find(31);
    // $d = new Departament;
    // $d->id_person = $p->id_person;
    // var_dump(array(
    //   // 'drop' => Departament::drop(),
    //   // 'create' => Departament::create(),
    //   'save' => $d->save(),
    //   // 'errors' => $d->getErrors(),
    //   'array' => $d->toArray(),
    //   // 'all' => Departament::all()->get('array')
    // ));

    //-----------------------------------------------------------------------
    // Seccion
    // $p = Person::find(31);
    // $d = Departament::find(29);
    // $s = new Section;

    // $s->name = 'seccion';
    // $s->id_departament = $d->id_departament;

    // var_dump($p->toArray());
    // var_dump($d->toArray());
    // var_dump($s->toArray());

    // var_dump(array(
    //   // 'drop' => Section::drop(),
    //   // 'create' => Section::create(),
    //   'save' => $s->save(),
    //   // 'errors' => $s->getErrors(),
    //   'array' => $s->toArray(),
    //   // 'all' => Section::all()->get('array')
    // ));

    // var_dump($p->departaments()->get('array'));
    // var_dump($d->chief()->row('array'));
    // var_dump($d->sections()->get('array'));
    
    //-----------------------------------------------------------------------
    // Product
    // $pp = new Product;
    // $pp->descripcion = 'producto';
    // $pp->cant = 10;
    // var_dump(array(
    //   // 'drop' => Product::drop(),
    //   // 'create' => Product::create(),
    //   'save' => $pp->save(),
    //   // 'errors' => $pp->getErrors(),
    //   'array' => $pp->toArray(),
    //   // 'all' => Product::all()->get('array')
    // ));

    // $pp->descripcion .= ' '.$pp->id;
    // $pp->save();

    //-----------------------------------------------------------------------
    // Invoice
    // $p = Person::find(31);
    // $i = new Invoice;
    // $i->invoice_date = date('c');
    // $i->id_person = $p->id_person;

    // var_dump(array(
    //   // 'drop' => Invoice::drop(),
    //   // 'create' => Invoice::create(),
    //   'save' => $i->save(),
    //   // 'errors' => $i->getErrors(),
    //   'array' => $i->toArray(),
    //   // 'all' => Invoice::all()->get('array')
    // ));

    // -----------------------------------------------------------------------
    // Probar todas las relaciones
    // $p = Person::find(31);
    // $d = Departament::find(29);
    // $pp = Product::find(1);
    // $i = Invoice::find(1);

    // var_dump([
    //     'departaments' => $p->departaments(),
    //     'chief' => $d->chief(),
    //     'sections' => $d->sections(),
    //     'invoices' => $pp->invoices(),
    //     'products' => $i->products(),
    // ]);
    
    //-----------------------------------------------------------------------
    // Probar validadores Uniques
    // $ppp = new Persona;

    // $ppp->ci = 'V1866792';
    // $ppp->nombre = 'alex22';
    // $ppp->email = 'alex2@sirideas.com';
    // // var_dump(Persona::me()->getValidators('nombre_email'));
    // // exit;
    // var_dump(array(
    //   // 'drop' => Persona::drop(),
    //   // 'create' => Persona::create(),
    //   'save' => $ppp->isValid(),
    //   'errors' => $ppp->getErrors(),
    //   'array' => $ppp->toArray(),
    // ));
    
    //-----------------------------------------------------------------------
    // Asignar valor a relacion belongTo
    $p = Person::find(30);

    $d = Departament::find(29);
    var_dump([
      $d->toArray(),
      $d->chief(null)->save(),
      $d->toArray(),
    ]);
    
    $d = Departament::find(29);
    var_dump([
      $d->toArray(),
      $d->chief($p)->save(),
      $d->toArray(),
    ]);

  }

}