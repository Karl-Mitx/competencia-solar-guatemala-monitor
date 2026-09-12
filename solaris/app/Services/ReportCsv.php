<?php

namespace App\Services;

class ReportCsv
{
    public function filename(): string
    {
        return 'solaris-departamentos-'.now()->format('Ymd').'.csv';
    }

    public function content(array $rows): string
    {
        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Departamento', 'Granjas', 'Paneles', 'Capacidad kW', 'Generación kWh', 'Esperada kWh', 'Familias', 'CO2 kg', 'CO2 toneladas']);
        foreach ($rows as $row) {
            $values = array_map(fn ($key) => $row[$key], ['name', 'farms', 'panels', 'capacity_kw', 'generation_kwh', 'expected_kwh', 'families', 'co2_kg', 'co2_tonnes']);
            if (preg_match('/^[\s]*[=+@-]/u', (string) $values[0])) {
                $values[0] = "'".$values[0];
            }
            fputcsv($out, $values);
        }
        rewind($out);
        $content = stream_get_contents($out);
        fclose($out);

        return $content;
    }
}
