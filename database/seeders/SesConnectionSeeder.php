<?php

namespace Database\Seeders;

use App\Models\SesConnection;
use Illuminate\Database\Seeder;

class SesConnectionSeeder extends Seeder
{
    public function run(): void
    {
        $connections = [
            [
                'ses_name' => 'mumbai',
                'username' => 'AKIAZQ3DTFBVAW3AQEUA',
                'password' => 'BHAczIY2DE8FHvm1lMDFNMS1VLf5e5o+KFVL9repGTOu',
                'region' => 'ap-south-1',
                'hostname' => 'email-smtp.ap-south-1.amazonaws.com',
                'port' => 465,
                'active' => 'Y',
                'from_email' => 'info@indiaevoting.com',
            ],
            [
                'ses_name' => 'viriginia',
                'username' => 'AKIAWFUKXAAXJ52TRYPE',
                'password' => 'BFZFFt7QXcWOw/yvMgBGzIHZi2bwWrmQLBucyi1BvEHk',
                'region' => 'us-east-1',
                'hostname' => 'email-smtp.us-east-1.amazonaws.com',
                'port' => 465,
                'active' => 'N',
                'from_email' => 'info@indiaevoting.com',
            ],
            [
                'ses_name' => 'SMTP - Gmail',
                'username' => 'info@indiaevoting.com',
                'password' => 'ydvcrkxvedkheihn',
                'region' => 'gmail',
                'hostname' => 'smtp.gmail.com',
                'port' => 587,
                'active' => 'N',
                'from_email' => 'info@indiaevoting.com',
            ],
            [
                'ses_name' => 'Verginia',
                'username' => 'AKIAZQ3DTFBVBNZHBG6S',
                'password' => 'BGKcdd2QnETDuk8WU92Y83jxfDPhve5epK9i9Uz8zyTF',
                'region' => 'us-east-1',
                'hostname' => 'email-smtp.us-east-1.amazonaws.com',
                'port' => 465,
                'active' => 'N',
                'from_email' => 'info@indiaevoting.com',
            ],
            [
                'ses_name' => 'Mumbai 2',
                'username' => 'AKIAZQ3DTFBVAW3AQEUA',
                'password' => 'BHAczIY2DE8FHvm1lMDFNMS1VLf5e5o+KFVL9repGTOu',
                'region' => 'ap-south-1',
                'hostname' => 'email-smtp.ap-south-1.amazonaws.com',
                'port' => 465,
                'active' => 'Y',
                'from_email' => 'info@indiaevoting.com',
            ],
        ];

        foreach ($connections as $connection) {
            SesConnection::create($connection);
        }
    }
}
