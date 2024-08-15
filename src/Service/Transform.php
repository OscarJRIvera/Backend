<?php

namespace App\Service;

class Transform
{
    public function transformCsv(string $filename, array $data): void
    {
        $file = fopen($filename, 'w');

        // Write headers
        fputcsv($file, array_keys($data[0]));

        foreach ($data as $user) {
            foreach ($user as &$value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
            }
            fputcsv($file, $user);
        }

        fclose($file);
    }

    public function TransformSummaryCsv(string $filename, array $summary): void
    {
        $file = fopen($filename, 'w');

        fputcsv($file, ['register', $summary['register']]);

        // gender summary
        fputcsv($file, ['Gender', 'Total']);
        foreach ($summary['gender'] as $gender => $count) {
            fputcsv($file, [$gender, $count]);
        }

        // age summary
        fputcsv($file, []);
        fputcsv($file, ['Age Group', 'Male', 'Female', 'Other']);
        foreach ($summary['age'] as $ageGroup => $counts) {
            fputcsv($file, array_merge([$ageGroup], $counts));
        }

        // City summary
        fputcsv($file, []);
        fputcsv($file, ['City', 'Male', 'Female', 'Other']);
        foreach ($summary['city'] as $city => $counts) {
            fputcsv($file, array_merge([$city], $counts));
        }

        // OS summary
        fputcsv($file, []);
        fputcsv($file, ['OS', 'Total']);
        foreach ($summary['os'] as $os => $count) {
            fputcsv($file, [$os, $count]);
        }

        fclose($file);
    }
}
