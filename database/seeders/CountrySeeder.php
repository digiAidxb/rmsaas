<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            // North America
            ['name' => 'United States', 'code' => 'US', 'currency_code' => 'USD', 'tax_rate' => 8.50, 'tax_settings' => json_encode(['vat_included' => false, 'service_tax' => 0])],
            ['name' => 'Canada', 'code' => 'CA', 'currency_code' => 'CAD', 'tax_rate' => 13.00, 'tax_settings' => json_encode(['vat_included' => true, 'gst' => 5, 'pst' => 8])],
            ['name' => 'Mexico', 'code' => 'MX', 'currency_code' => 'MXN', 'tax_rate' => 16.00, 'tax_settings' => json_encode(['vat_included' => true])],

            // Europe
            ['name' => 'United Kingdom', 'code' => 'GB', 'currency_code' => 'GBP', 'tax_rate' => 20.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Germany', 'code' => 'DE', 'currency_code' => 'EUR', 'tax_rate' => 19.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'France', 'code' => 'FR', 'currency_code' => 'EUR', 'tax_rate' => 20.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Italy', 'code' => 'IT', 'currency_code' => 'EUR', 'tax_rate' => 22.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Spain', 'code' => 'ES', 'currency_code' => 'EUR', 'tax_rate' => 21.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Netherlands', 'code' => 'NL', 'currency_code' => 'EUR', 'tax_rate' => 21.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Switzerland', 'code' => 'CH', 'currency_code' => 'CHF', 'tax_rate' => 7.70, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Austria', 'code' => 'AT', 'currency_code' => 'EUR', 'tax_rate' => 20.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Belgium', 'code' => 'BE', 'currency_code' => 'EUR', 'tax_rate' => 21.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Norway', 'code' => 'NO', 'currency_code' => 'NOK', 'tax_rate' => 25.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Sweden', 'code' => 'SE', 'currency_code' => 'SEK', 'tax_rate' => 25.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Denmark', 'code' => 'DK', 'currency_code' => 'DKK', 'tax_rate' => 25.00, 'tax_settings' => json_encode(['vat_included' => true])],

            // Middle East
            ['name' => 'United Arab Emirates', 'code' => 'AE', 'currency_code' => 'AED', 'tax_rate' => 5.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Saudi Arabia', 'code' => 'SA', 'currency_code' => 'SAR', 'tax_rate' => 15.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Qatar', 'code' => 'QA', 'currency_code' => 'QAR', 'tax_rate' => 0.00, 'tax_settings' => json_encode(['vat_included' => false])],
            ['name' => 'Kuwait', 'code' => 'KW', 'currency_code' => 'KWD', 'tax_rate' => 0.00, 'tax_settings' => json_encode(['vat_included' => false])],
            ['name' => 'Bahrain', 'code' => 'BH', 'currency_code' => 'BHD', 'tax_rate' => 10.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Oman', 'code' => 'OM', 'currency_code' => 'OMR', 'tax_rate' => 5.00, 'tax_settings' => json_encode(['vat_included' => true])],

            // Asia Pacific
            ['name' => 'Australia', 'code' => 'AU', 'currency_code' => 'AUD', 'tax_rate' => 10.00, 'tax_settings' => json_encode(['gst' => 10, 'vat_included' => true])],
            ['name' => 'Japan', 'code' => 'JP', 'currency_code' => 'JPY', 'tax_rate' => 10.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'South Korea', 'code' => 'KR', 'currency_code' => 'KRW', 'tax_rate' => 10.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Singapore', 'code' => 'SG', 'currency_code' => 'SGD', 'tax_rate' => 7.00, 'tax_settings' => json_encode(['gst' => 7, 'vat_included' => true])],
            ['name' => 'Hong Kong', 'code' => 'HK', 'currency_code' => 'HKD', 'tax_rate' => 0.00, 'tax_settings' => json_encode(['vat_included' => false])],
            ['name' => 'New Zealand', 'code' => 'NZ', 'currency_code' => 'NZD', 'tax_rate' => 15.00, 'tax_settings' => json_encode(['gst' => 15, 'vat_included' => true])],
            ['name' => 'Malaysia', 'code' => 'MY', 'currency_code' => 'MYR', 'tax_rate' => 6.00, 'tax_settings' => json_encode(['sst' => 6, 'vat_included' => false])],
            ['name' => 'Thailand', 'code' => 'TH', 'currency_code' => 'THB', 'tax_rate' => 7.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Philippines', 'code' => 'PH', 'currency_code' => 'PHP', 'tax_rate' => 12.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Indonesia', 'code' => 'ID', 'currency_code' => 'IDR', 'tax_rate' => 11.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Vietnam', 'code' => 'VN', 'currency_code' => 'VND', 'tax_rate' => 10.00, 'tax_settings' => json_encode(['vat_included' => true])],

            // South Asia
            ['name' => 'India', 'code' => 'IN', 'currency_code' => 'INR', 'tax_rate' => 18.00, 'tax_settings' => json_encode(['gst' => 18, 'cgst' => 9, 'sgst' => 9])],
            ['name' => 'China', 'code' => 'CN', 'currency_code' => 'CNY', 'tax_rate' => 13.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Pakistan', 'code' => 'PK', 'currency_code' => 'PKR', 'tax_rate' => 17.00, 'tax_settings' => json_encode(['sales_tax' => 17])],
            ['name' => 'Bangladesh', 'code' => 'BD', 'currency_code' => 'BDT', 'tax_rate' => 15.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Sri Lanka', 'code' => 'LK', 'currency_code' => 'LKR', 'tax_rate' => 8.00, 'tax_settings' => json_encode(['vat_included' => true])],

            // South America
            ['name' => 'Brazil', 'code' => 'BR', 'currency_code' => 'BRL', 'tax_rate' => 17.00, 'tax_settings' => json_encode(['icms' => 17, 'pis_cofins' => 9.25])],
            ['name' => 'Argentina', 'code' => 'AR', 'currency_code' => 'ARS', 'tax_rate' => 21.00, 'tax_settings' => json_encode(['iva' => 21])],
            ['name' => 'Chile', 'code' => 'CL', 'currency_code' => 'CLP', 'tax_rate' => 19.00, 'tax_settings' => json_encode(['iva' => 19])],
            ['name' => 'Colombia', 'code' => 'CO', 'currency_code' => 'COP', 'tax_rate' => 19.00, 'tax_settings' => json_encode(['iva' => 19])],
            ['name' => 'Peru', 'code' => 'PE', 'currency_code' => 'PEN', 'tax_rate' => 18.00, 'tax_settings' => json_encode(['igv' => 18])],

            // Africa
            ['name' => 'South Africa', 'code' => 'ZA', 'currency_code' => 'ZAR', 'tax_rate' => 15.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Egypt', 'code' => 'EG', 'currency_code' => 'EGP', 'tax_rate' => 14.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Morocco', 'code' => 'MA', 'currency_code' => 'MAD', 'tax_rate' => 20.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Nigeria', 'code' => 'NG', 'currency_code' => 'NGN', 'tax_rate' => 7.50, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Kenya', 'code' => 'KE', 'currency_code' => 'KES', 'tax_rate' => 16.00, 'tax_settings' => json_encode(['vat_included' => true])],

            // Other Notable Countries
            ['name' => 'Turkey', 'code' => 'TR', 'currency_code' => 'TRY', 'tax_rate' => 18.00, 'tax_settings' => json_encode(['kdv' => 18])],
            ['name' => 'Israel', 'code' => 'IL', 'currency_code' => 'ILS', 'tax_rate' => 17.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Russia', 'code' => 'RU', 'currency_code' => 'RUB', 'tax_rate' => 20.00, 'tax_settings' => json_encode(['nds' => 20])],
            ['name' => 'Ukraine', 'code' => 'UA', 'currency_code' => 'UAH', 'tax_rate' => 20.00, 'tax_settings' => json_encode(['pdv' => 20])],
            ['name' => 'Poland', 'code' => 'PL', 'currency_code' => 'PLN', 'tax_rate' => 23.00, 'tax_settings' => json_encode(['vat_included' => true])],
            ['name' => 'Czech Republic', 'code' => 'CZ', 'currency_code' => 'CZK', 'tax_rate' => 21.00, 'tax_settings' => json_encode(['dph' => 21])],
        ];

        foreach ($countries as $country) {
            $country['created_at'] = now();
            $country['updated_at'] = now();
        }

        DB::connection('landlord')->table('countries')->insert($countries);
    }
}