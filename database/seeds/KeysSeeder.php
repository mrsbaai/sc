<?php

use Illuminate\Database\Seeder;

class KeysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $keys = array(
            array("key"=>"FE01CE2A7FBAC8FAFAED7C982A04E226",'created_at' => date("Y-m-d H:i:s"),'updated_at' => date("Y-m-d H:i:s")),
        );

        foreach($keys as $key)
        {
            DB::table('keys')->insert($key);
        };

    }
}
