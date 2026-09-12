<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // National department codes; coordinates are reference points near capitals.
        $departments = [
            ['01', 'Guatemala', 14.6349, -90.5069, '#10b981'],
            ['02', 'El Progreso', 14.8542, -90.0647, '#f59e0b'],
            ['03', 'Sacatepéquez', 14.5611, -90.7344, '#6366f1'],
            ['04', 'Chimaltenango', 14.6611, -90.8208, '#06b6d4'],
            ['05', 'Escuintla', 14.3050, -90.7850, '#84cc16'],
            ['06', 'Santa Rosa', 14.2781, -90.2989, '#f97316'],
            ['07', 'Sololá', 14.7739, -91.1875, '#8b5cf6'],
            ['08', 'Totonicapán', 14.9117, -91.3611, '#ec4899'],
            ['09', 'Quetzaltenango', 14.8347, -91.5181, '#3b82f6'],
            ['10', 'Suchitepéquez', 14.5361, -91.5033, '#14b8a6'],
            ['11', 'Retalhuleu', 14.5361, -91.6778, '#eab308'],
            ['12', 'San Marcos', 14.9653, -91.7958, '#a855f7'],
            ['13', 'Huehuetenango', 15.3192, -91.4722, '#0ea5e9'],
            ['14', 'Quiché', 15.0306, -91.1489, '#d946ef'],
            ['15', 'Baja Verapaz', 15.1039, -90.3181, '#22c55e'],
            ['16', 'Alta Verapaz', 15.4697, -90.3708, '#0891b2'],
            ['17', 'Petén', 16.9297, -89.8917, '#65a30d'],
            ['18', 'Izabal', 15.7278, -88.5944, '#0284c7'],
            ['19', 'Zacapa', 14.9722, -89.5306, '#ea580c'],
            ['20', 'Chiquimula', 14.8000, -89.5439, '#db2777'],
            ['21', 'Jalapa', 14.6328, -89.9889, '#7c3aed'],
            ['22', 'Jutiapa', 14.2917, -89.8958, '#d97706'],
        ];

        foreach ($departments as [$code, $name, $latitude, $longitude, $color]) {
            Department::updateOrCreate(['code' => $code], compact('name', 'latitude', 'longitude', 'color'));
        }
    }
}
