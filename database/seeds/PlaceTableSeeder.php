<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlaceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create("App\Modela\Place");

        for ($i = 8; $i < 150; $i++) {
            //$filePath = storage_path('place_category');
            //\Storage::disk('public')->put($pathe, (string) $image->encode());
            //dd($filePath);
            /*DB::table('product_categories')->insert([
             'parent_id'=>rand(100,150),
            'name'=>$faker->sentence,
            'image'=>null,
            'is_active'=>1
            ]);   */

            /*$product = DB::table('products')->insert([
            'name'=>$faker->sentence,
            'quantity'=>rand(10,100),
            'price'=>rand(100,150),
            'weight'=>1,
            'is_active'=>1
            ]);  */


            $image = DB::table('product_images')->insert(
                [
                    'product_id' => $i,
                    'image' => "place/8A7YUcqydFhZEO3WtHqPx0U5MkFVmsEtYRPDsoUg.png",
                ]
            );

            DB::table('product_images')->insert(
                [
                    'product_id' => $i,
                    'image' => "place/2goWqBRrcKFuzORzEpe2YyB9LbidU6CjO9JrDQqE.jpeg",
                ]
            );
        }
        /*$address_chiefdoms = "SELECT ANY_VALUE(`id`) as id FROM `address_chiefdoms` GROUP BY `address_id`, `name`";
        $addressChiefdoms =  DB::select(DB::raw($address_chiefdoms));
        $uids=array();
        foreach ($addressChiefdoms as $key => $value){

            $uids[] = $value->id;
        }

        $delete_chiefdoms ="DELETE FROM address_chiefdoms WHERE id NOT IN ('".implode("','",$uids)."')";
        $addressChiefdoms =  DB::select(DB::raw($delete_chiefdoms));*/


        /*$DB = DB::connection('mysqlOld');
        $sqlward = "SELECT ANY_VALUE(`id`) as id,`ward`,ANY_VALUE(`constituency`) as constituency,ANY_VALUE(`province`) as province,ANY_VALUE(`district`) as district FROM `boundary_delimitations` GROUP BY `ward` ORDER BY ANY_VALUE(`id`) ASC";
        $ward = $DB->select(DB::raw($sqlward));
        foreach ($ward as $key => $value) {

            $data = [
                        "ward_number" => $value->ward,
                        "constituency" => $value->constituency,
                        "district" => $value->district,
                        "province" => $value->province,

                    ];
            $address = \App\Models\Address::firstOrCreate($data);

            $sqlchiefdoms = "SELECT * FROM boundary_delimitations where ward={$value->ward}";

            $subchiefdoms = $DB->select(DB::raw($sqlchiefdoms));

            foreach ($subchiefdoms as $chiefdomskey => $chiefdomsvalue) {
                /* Chiefdoms
                $values = [
                    "name" => $chiefdomsvalue->chiefdom,
                ];

                $ChiefdomsArea =  new AddressChiefdom($values);
                $ChiefdomsArea->address()->associate(Address::findOrFail($address->id));
                $ChiefdomsArea->save();

                /* Section
                $sectionValues = [
                    "name" => $chiefdomsvalue->section,
                ];
                $sectionArea =  new AddressSection($sectionValues);
                $sectionArea->address()->associate(Address::findOrFail($address->id));
                $sectionArea->save();


                /* Area
                $sectionValues = [
                    "name" => $chiefdomsvalue->section,
                ];
                $Area =  new AddressArea($sectionValues);
                $Area->address()->associate(Address::findOrFail($address->id));
                $Area->save();
            }



        }*/

    }
}
