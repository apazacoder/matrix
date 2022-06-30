<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   * @return void
   */
  public function run() {
    $now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
    $items =
      [
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Carlos",
          "second_name" => "Sebastián",
          "first_surname" => "Mamani",
          "second_surname" => "Cuenca",
          "position" => "",
          "email" => "sebastian.mamani@hidrocarburos.gob.bo",
          "password" => bcrypt("sebastian.mamani"),
        ],
        [
          "ci" => '',
          "exp" => "LP",
          "first_name" => "Alcides",
          "second_name" => "",
          "first_surname" => "Apaza",
          "second_surname" => "",
          "position" => "Developer",
          "email" => "alcides.apaza@ylb.gob.bo",
          "password" => bcrypt("alcides.apaza"),
        ],
        [
          "ci" => '',
          "exp" => "LP",
          "first_name" => "Victor",
          "second_name" => "",
          "first_surname" => "Fuentes",
          "second_surname" => "",
          "position" => "RESPONSABLE UNIDAD DE TECNOLOGIAS DE LA INFORMACION",
          "email" => "victor.fuentes@ylb.gob.bo",
          "password" => bcrypt("victor.fuentes"),
        ],
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Omar",
          "second_name" => "",
          "first_surname" => "Salvador",
          "second_surname" => "Beltrán",
          "position" => "DIRECTOR DE INVESTIGACIÓN Y DESARROLLO",
          "email" => "salvador.beltran@ylb.gob.bo",
          "password" => bcrypt("salvador.beltran"),
        ],
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Celia",
          "second_name" => "",
          "first_surname" => "Mendez",
          "second_surname" => "Chara",
          "position" => "DIRECTORA ADMINISTRATIVA FINANCIERA",
          "email" => "celia.mendez@ylb.gob.bo",
          "password" => bcrypt("celia.mendez"),
        ],
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Rodney",
          "second_name" => "Arnaldo",
          "first_surname" => "Goitia",
          "second_surname" => "Sanchez",
          "position" => "DIRECTOR JURÍDICO",
          "email" => "rodney.goitia@ylb.gob.bo",
          "password" => bcrypt("rodney.goitia"),
        ],
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Ximena",
          "second_name" => "Patricia",
          "first_surname" => "Lozano",
          "second_surname" => "Vargas",
          "position" => "JEFE DE DEPARTAMENTO DE COMERCIALIZACIÓN",
          "email" => "ximena.lozano@ylb.gob.bo",
          "password" => bcrypt("ximena.lozano"),
        ],
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Saúl",
          "second_name" => "",
          "first_surname" => "Cuiza",
          "second_surname" => "",
          "position" => "DIRECTOR DE OPERACIONES",
          "email" => "saul.cuiza@ylb.gob.bo",
          "password" => bcrypt("saul.cuiza"),
        ],
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Omar",
          "second_name" => "",
          "first_surname" => "Salvador",
          "second_surname" => "Beltrán",
          "position" => "DIRECTOR DE ELECTROQUIMICA Y BATERIAS a.i.",
          "email" => "salvador.beltran.ai@ylb.gob.bo",
          "password" => bcrypt("salvador.beltran.ai"),
        ],
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Igor",
          "second_name" => "Ismael",
          "first_surname" => "Durán",
          "second_surname" => "Cornejo",
          "position" => "DIRECTOR DE PLANIFICACIÓN",
          "email" => "igor.duran@ylb.gob.bo",
          "password" => bcrypt("igor.duran"),
        ],
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Nelson",
          "second_name" => "Román",
          "first_surname" => "Carvajal",
          "second_surname" => "Velasco",
          "position" => "JEFE DE GEOLOGÍA",
          "email" => "nelson.carvajal@ylb.gob.bo",
          "password" => bcrypt("nelson.carvajal"),
        ],
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Roger",
          "second_name" => "Reynaldo",
          "first_surname" => "Filipps",
          "second_surname" => "Diaz",
          "position" => "JEFE DE UNIDAD DE AUDITORÍA INTERNA",
          "email" => "roger.filipps@ylb.gob.bo",
          "password" => bcrypt("roger.filipps"),
        ],
        [
          "ci" => '',
          "exp" => "",
          "first_name" => "Carlos",
          "second_name" => "Sebastián",
          "first_surname" => "Mamani",
          "second_surname" => "Cuenca",
          "position" => "",
          "email" => "carlossebastianmc@gmail.com",
          "password" => bcrypt("carlossebastianmc"),
        ],
      ];
    foreach ($items as $item) {
      DB::table('users')->insert(
        [
          "ci" => array_key_exists("ci", $item) ? $item["ci"] : "",
          "exp" => array_key_exists("exp", $item) ? $item["exp"] : "",
          "first_name" => array_key_exists("first_name", $item) ? $item["first_name"] : "",
          "second_name" => array_key_exists("second_name", $item) ? $item["second_name"] : "",
          "first_surname" => array_key_exists("first_surname", $item) ? $item["first_surname"] : "",
          "second_surname" => array_key_exists("second_surname", $item) ? $item["second_surname"] : "",
          "position" => $item["position"],
          "email" => $item["email"],
          "password" => $item["password"],
          "api_token" => Str::random(80),
          'created_at' => $now,
          'updated_at' => $now
        ]
      );
    }
  }
}
