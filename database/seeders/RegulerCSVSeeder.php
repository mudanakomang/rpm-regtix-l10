<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\TicketType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class RegulerCSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = database_path('seeders/data/reguler.csv');

        if (!file_exists($file) || !is_readable($file)) {
            throw new \Exception("CSV file not found or not readable.");
        }

        // Ambil ticket_type_id dari nama
        $ticketType = TicketType::where('name', 'Regular')->first();

        if (!$ticketType) {
            throw new \Exception("Ticket type 'Regular' not found.");
        }

        $ticketTypeId = $ticketType->id;

        $header = null;

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                if (!$header) {
                    $header = $row;
                } else {

                    $data = array_combine($header, $row);
                    // dd($data);
                    $data['ticket_type_id'] = $ticketTypeId;

                    Registration::create($data);
                }
            }
            fclose($handle);
        }
    }
}
