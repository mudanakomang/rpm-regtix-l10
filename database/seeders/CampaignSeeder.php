<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $campaign = [
            'title' => 'Pengambilan Race Pack',
            'event_id' => Event::first()->id,
            'status' => 'active',
            'subject' => 'Pengambilan Race Pack',
            'html_template' => '<html><body><h1>This is a transactional email {{params.name}}</h1><p>{{params.reg_id}}</p></body></html>' 
        ];

        Campaign::create($campaign);
    }
}
