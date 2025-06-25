<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Auto::cursor()->each(function(\App\Models\Auto $auto) {
            $auto->fill([
                'entity_id' => \Eav\Entity::findByCode('auto')->entity_id,
                'attribute_set_id' => 1
            ])->save();
        });

        \App\Models\RealEstate::cursor()->each(function(\App\Models\RealEstate $auto) {
            $auto->fill([
                'entity_id' => \Eav\Entity::findByCode('real_estate')->entity_id,
                'attribute_set_id' => 2
            ])->save();
        });
    }
}
