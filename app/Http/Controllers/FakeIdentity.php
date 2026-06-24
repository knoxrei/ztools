<?php

namespace App\Http\Controllers;

use Faker\Factory as Faker;
use Faker\Generator;

class FakeIdentity extends Controller
{
    // Supported locales mapped to region
    private static array $localesByRegion = [
        'global' => ['en_US', 'en_GB', 'en_CA', 'en_AU', 'en_NZ'],
        'asian' => ['ja_JP', 'zh_TW', 'ko_KR', 'id_ID', 'en_SG'],
        'european' => ['de_DE', 'fr_FR', 'it_IT', 'es_ES', 'nl_NL', 'pl_PL', 'pt_PT', 'sv_SE', 'ro_RO'],
    ];

    // Country meta: [name, code, phone_prefix, currency, currency_code]
    private static array $countryMeta = [
        'en_US' => ['United States',  'US', '+1',   '$',  'USD'],
        'en_GB' => ['United Kingdom', 'GB', '+44',  '£',  'GBP'],
        'en_CA' => ['Canada',         'CA', '+1',   '$',  'CAD'],
        'en_AU' => ['Australia',      'AU', '+61',  '$',  'AUD'],
        'en_NZ' => ['New Zealand',    'NZ', '+64',  '$',  'NZD'],
        'ja_JP' => ['Japan',          'JP', '+81',  '¥',  'JPY'],
        'zh_CN' => ['China',          'CN', '+86',  '¥',  'CNY'],
        'zh_TW' => ['Taiwan',         'TW', '+886', 'NT$', 'TWD'],
        'ko_KR' => ['South Korea',    'KR', '+82',  '₩',  'KRW'],
        'id_ID' => ['Indonesia',      'ID', '+62',  'Rp', 'IDR'],
        'en_SG' => ['Singapore',      'SG', '+65',  '$',  'SGD'],
        'de_DE' => ['Germany',        'DE', '+49',  '€',  'EUR'],
        'fr_FR' => ['France',         'FR', '+33',  '€',  'EUR'],
        'it_IT' => ['Italy',          'IT', '+39',  '€',  'EUR'],
        'es_ES' => ['Spain',          'ES', '+34',  '€',  'EUR'],
        'nl_NL' => ['Netherlands',    'NL', '+31',  '€',  'EUR'],
        'pl_PL' => ['Poland',         'PL', '+48',  'zł', 'PLN'],
        'pt_PT' => ['Portugal',       'PT', '+351', '€',  'EUR'],
        'sv_SE' => ['Sweden',         'SE', '+46',  'kr', 'SEK'],
        'ro_RO' => ['Romania',        'RO', '+40',  'lei', 'RON'],
    ];

    private static array $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    private static array $eyeColors = ['Brown', 'Blue', 'Green', 'Hazel', 'Gray', 'Amber', 'Violet'];

    private static array $hairColors = ['Black', 'Brown', 'Blonde', 'Red', 'Gray', 'White', 'Auburn', 'Chestnut'];

    private static array $hairLength = ['Short', 'Medium', 'Long', 'Very Long', 'Bald', 'Buzzcut'];

    private static array $maritalStatus = ['Single', 'Married', 'Divorced', 'Widowed', 'In Relationship'];

    private static array $education = ['High School', 'Associate Degree', 'Bachelor\'s Degree', "Master's Degree", 'PhD', 'Vocational', 'Some College'];

    private static array $religions = ['Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Judaism', 'Atheist', 'Agnostic', 'Other'];

    private static array $zodiac = ['Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'];

    private static array $cardBrands = [
        ['Visa',       '4',    16, 3],
        ['Mastercard', '51',   16, 3],
        ['Mastercard', '52',   16, 3],
        ['Amex',       '34',   15, 4],
        ['Amex',       '37',   15, 4],
        ['Discover',   '6011', 16, 3],
        ['UnionPay',   '62',   16, 3],
        ['JCB',        '3528', 16, 3],
    ];

    private static array $browsers = [
        ['Chrome',  '120.0.0.0'], ['Chrome', '121.0.0.0'], ['Firefox', '122.0'],
        ['Safari',  '17.3'],      ['Edge',   '121.0.0.0'], ['Opera', '107.0.0.0'],
        ['Brave',   '1.63.165'],  ['Vivaldi', '6.5.3206.48'],
    ];

    private static array $operatingSystems = [
        'Windows 11', 'Windows 10', 'macOS 14 Sonoma', 'macOS 13 Ventura',
        'Ubuntu 22.04', 'Ubuntu 23.10', 'Fedora 39', 'Arch Linux',
        'Android 14', 'Android 13', 'iOS 17', 'iPadOS 17', 'Debian 12',
    ];

    private static array $networkSpeeds = ['100 Mbps', '250 Mbps', '500 Mbps', '1 Gbps', '25 Mbps', '50 Mbps'];

    private static array $ispProviders = ['Comcast', 'AT&T', 'Verizon', 'T-Mobile', 'British Telecom', 'Deutsche Telekom',
        'Orange', 'Telkom', 'Singtel', 'SoftBank', 'China Unicom', 'KT Corp'];

    private static array $vehicleBrands = ['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes-Benz', 'Volkswagen',
        'Hyundai', 'Nissan', 'Chevrolet', 'Audi', 'Mazda', 'Kia', 'Subaru'];

    private static array $vehicleColors = ['Black', 'White', 'Silver', 'Red', 'Blue', 'Gray', 'Green', 'Yellow', 'Orange'];

    private static array $vehicleTypes = ['Sedan', 'SUV', 'Hatchback', 'Pickup Truck', 'Coupe', 'Convertible', 'Van', 'Minivan'];

    // ─────────────────────────────────────────────────────────────────────────

    public function generate(string $region = 'global'): array
    {
        $region = array_key_exists($region, self::$localesByRegion) ? $region : 'global';
        $locales = self::$localesByRegion[$region];
        $locale = $locales[array_rand($locales)];
        $meta = self::$countryMeta[$locale];
        [$country, $countryCode, $phonePrefix, $currencySymbol, $currencyCode] = $meta;

        // Primary faker uses the locale for localized names/addresses
        $faker = Faker::create($locale);
        // en_US faker for things that need reliable English fallback
        $fakerEN = Faker::create('en_US');

        $gender = $faker->randomElement(['Male', 'Female', 'Non-binary']);
        $isMale = $gender === 'Male';
        $firstName = $isMale ? $faker->firstName('male') : $faker->firstName('female');
        $lastName = $faker->lastName();
        $age = $faker->numberBetween(18, 72);
        $dob = $faker->dateTimeBetween("-{$age} years", '-18 years')->format('Y-m-d');
        $username = $this->buildUsername($fakerEN, $firstName, $lastName);

        $photoId = $faker->numberBetween(0, 99);
        if ($gender === 'Male') {
            $avatar = "https://randomuser.me/api/portraits/men/{$photoId}.jpg";
        } elseif ($gender === 'Female') {
            $avatar = "https://randomuser.me/api/portraits/women/{$photoId}.jpg";
        } else {
            $category = $faker->randomElement(['men', 'women']);
            $avatar = "https://randomuser.me/api/portraits/{$category}/{$photoId}.jpg";
        }

        [$card] = [$this->generateCreditCard($faker)];
        $iban = $this->generateIban($faker, $countryCode);
        $bitcoin = $this->generateBitcoinAddress($faker);
        $ethereum = '0x'.$faker->regexify('[0-9a-f]{40}');
        $salary = $faker->numberBetween(35, 300) * 1000;
        [$ipv4, $ipv6, $mac] = $this->generateNetworkInfo($faker);
        $ua = $this->generateUserAgent($faker);
        $ssn = $this->generateSSN($faker, $countryCode);

        $vehicleYear = $faker->numberBetween(2005, 2024);
        $vehicleBrand = $faker->randomElement(self::$vehicleBrands);
        $vehicleModel = $fakerEN->word();

        return [
            'personal' => [
                'full_name' => "{$firstName} {$lastName}",
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'gender' => $gender,
                'age' => $age,
                'date_of_birth' => $dob,
                'avatar' => $avatar,
                'nationality' => $country,
                'marital_status' => $faker->randomElement(self::$maritalStatus),
                'education' => $faker->randomElement(self::$education),
                'religion' => $faker->randomElement(self::$religions),
                'zodiac' => $faker->randomElement(self::$zodiac),
                'ssn_or_id' => $ssn,
                'passport_no' => strtoupper($faker->bothify('??#######')),
                'drivers_license' => strtoupper($faker->bothify('??-######-??')),
                'mothers_maiden' => $faker->lastName(),
                'blood_type' => $faker->randomElement(self::$bloodTypes),
                'height' => $faker->numberBetween(155, 200).' cm',
                'weight' => $faker->numberBetween(48, 110).' kg',
                'eye_color' => $faker->randomElement(self::$eyeColors),
                'hair_color' => $faker->randomElement(self::$hairColors),
                'hair_length' => $faker->randomElement(self::$hairLength),
            ],
            'contact' => [
                'email' => strtolower($this->buildEmail($fakerEN, $firstName, $lastName)),
                'email_alt' => strtolower($faker->safeEmail()),
                'phone' => $this->buildPhone($faker, $phonePrefix),
                'phone_mobile' => $this->buildPhone($faker, $phonePrefix),
                'website' => "https://{$username}.".$faker->randomElement(['dev', 'io', 'me', 'net', 'xyz']),
                'linkedin' => "https://linkedin.com/in/{$username}",
                'twitter' => "@{$username}",
                'github' => "https://github.com/{$username}",
            ],
            'address' => [
                'street' => $faker->streetAddress(),
                'city' => $faker->city(),
                'state' => $this->safeState($faker),
                'zip' => $faker->postcode(),
                'country' => $country,
                'country_code' => $countryCode,
                'latitude' => round($faker->latitude(), 6),
                'longitude' => round($faker->longitude(), 6),
                'timezone' => $faker->timezone(),
            ],
            'financial' => [
                'card_brand' => $card['brand'],
                'card_number' => $card['number'],
                'card_expiry' => $card['expiry'],
                'card_cvv' => $card['cvv'],
                'card_pin' => str_pad((string) $faker->numberBetween(0, 9999), 4, '0', STR_PAD_LEFT),
                'iban' => $iban,
                'swift_bic' => strtoupper($faker->bothify('????').$countryCode.$faker->bothify('??').'###'),
                'bitcoin' => $bitcoin,
                'ethereum' => $ethereum,
                'salary' => number_format($salary).' '.$currencyCode,
                'net_worth' => $currencySymbol.number_format($faker->numberBetween(5000, 9_000_000)),
                'currency' => $currencyCode,
            ],
            'internet' => [
                'ipv4' => $ipv4,
                'ipv6' => $ipv6,
                'mac_address' => $mac,
                'browser' => $ua['browser'],
                'browser_ver' => $ua['version'],
                'os' => $ua['os'],
                'user_agent' => $ua['ua'],
                'isp' => $faker->randomElement(self::$ispProviders),
                'connection' => $faker->randomElement(['WiFi', 'Ethernet', 'LTE', '5G', 'DSL']),
                'speed' => $faker->randomElement(self::$networkSpeeds),
                'domain' => $fakerEN->domainName(),
                'tld' => $faker->tld(),
            ],
            'employment' => [
                'company' => $fakerEN->company(),
                'company_email' => strtolower($this->toAsciiInitial($firstName).'.'.$this->toAsciiSlug($lastName).'@'.$fakerEN->domainName()),
                'job_title' => $fakerEN->jobTitle(),
                'department' => $faker->randomElement(['Engineering', 'Product', 'Design', 'Marketing', 'Sales',
                    'Finance', 'HR', 'Operations', 'Legal', 'R&D', 'IT', 'Security']),
                'salary' => number_format($salary).' '.$currencyCode,
                'employee_id' => strtoupper($faker->bothify('??-#####')),
                'work_phone' => $this->buildPhone($faker, $phonePrefix),
                'start_date' => $faker->dateTimeBetween('-15 years', '-6 months')->format('Y-m-d'),
            ],
            'vehicle' => [
                'brand' => $vehicleBrand,
                'model' => ucfirst($vehicleModel),
                'year' => $vehicleYear,
                'color' => $faker->randomElement(self::$vehicleColors),
                'type' => $faker->randomElement(self::$vehicleTypes),
                'plate' => strtoupper($faker->bothify('???-####')),
                'vin' => strtoupper($faker->bothify('?????????????????')),
                'fuel' => $faker->randomElement(['Gasoline', 'Diesel', 'Electric', 'Hybrid', 'Plug-in Hybrid']),
                'insurance' => strtoupper($faker->bothify('INS-########')),
            ],
            'medical' => [
                'blood_type' => $faker->randomElement(self::$bloodTypes),
                'allergies' => $faker->randomElement(['None', 'Peanuts', 'Shellfish', 'Lactose', 'Gluten', 'Pollen', 'Penicillin']),
                'conditions' => $faker->randomElement(['None', 'Hypertension', 'Diabetes Type 2', 'Asthma', 'Arthritis', 'Anxiety']),
                'medications' => $faker->randomElement(['None', 'Metformin', 'Lisinopril', 'Atorvastatin', 'Levothyroxine', 'Amlodipine']),
                'doctor' => "Dr. {$fakerEN->lastName()}, {$faker->randomElement(['MD', 'DO', 'PhD', 'MBBS'])}",
                'insurance_id' => strtoupper($faker->bothify('HLT-########')),
                'organ_donor' => $faker->randomElement(['Yes', 'No']),
            ],
            'meta' => [
                'locale' => $locale,
                'region' => $region,
                'generated' => now()->toIso8601String(),
            ],
        ];

        return $this->sanitizeForJson($identity);
    }

    /**
     * Recursively sanitize all strings in the array to valid UTF-8
     * so Livewire / PHP json_encode never throws JsonException.
     */
    private function sanitizeForJson(array $data): array
    {
        array_walk_recursive($data, function (mixed &$value): void {
            if (! is_string($value)) {
                return;
            }
            // Try to convert to UTF-8 via iconv, silently drop bad bytes
            $clean = @iconv('UTF-8', 'UTF-8//IGNORE//TRANSLIT', $value);
            if ($clean === false || $clean === '') {
                // Fallback: strip everything that is not valid UTF-8
                $clean = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
            // Final guard: json_encode test
            if (json_encode($clean) === false) {
                $clean = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/u', '', (string) $clean) ?? '';
            }
            $value = (string) $clean;
        });

        return $data;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function buildUsername(Generator $faker, string $first, string $last): string
    {
        $sep = $faker->randomElement(['_', '.', '']);
        $num = $faker->boolean(50) ? $faker->numberBetween(1, 999) : '';

        return strtolower("{$first}{$sep}{$last}{$num}");
    }

    private function buildEmail(Generator $faker, string $first, string $last): string
    {
        $domain = $faker->randomElement(['gmail.com', 'yahoo.com', 'outlook.com', 'protonmail.com',
            'hotmail.com', 'icloud.com', 'fastmail.com', 'zoho.com']);
        $formats = [
            "{$first}.{$last}",
            "{$first}{$last}",
            $this->toAsciiInitial($first).$last,
            "{$first}.{$last}".$faker->numberBetween(10, 999),
            "{$first}".$faker->numberBetween(10, 99),
        ];

        return $faker->randomElement($formats)."@{$domain}";
    }

    private function buildPhone(Generator $faker, string $prefix): string
    {
        $n = $faker->numerify('###-###-####');

        return "{$prefix} {$n}";
    }

    private function safeState(Generator $faker): string
    {
        try {
            return $faker->state();
        } catch (\Throwable) {
            return $faker->city();
        }
    }

    private function generateSSN(Generator $faker, string $countryCode): string
    {
        return match ($countryCode) {
            'US' => $faker->numerify('###-##-####'),
            'GB' => strtoupper($faker->bothify('?? ## ## ## ?')),
            'DE' => $faker->numerify('## ######## ###'),
            'FR' => $faker->numerify('# ## ## ## ### ### ##'),
            'JP' => $faker->numerify('####-########'),
            'ID' => $faker->numerify('################'), // NIK
            default => $faker->numerify('###-##-####'),
        };
    }

    private function generateCreditCard(Generator $faker): array
    {
        [$brand, $prefix, $length, $cvvLen] = $faker->randomElement(self::$cardBrands);

        $number = $prefix;
        while (strlen($number) < $length - 1) {
            $number .= $faker->numberBetween(0, 9);
        }
        $number .= $this->luhnCheckDigit($number);

        $formatted = match ($brand) {
            'Amex' => implode(' ', [substr($number, 0, 4), substr($number, 4, 6), substr($number, 10)]),
            default => implode(' ', str_split($number, 4)),
        };

        $month = str_pad((string) $faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);
        $year = (int) date('y') + $faker->numberBetween(1, 7);
        $cvv = str_pad((string) $faker->numberBetween(0, 10 ** $cvvLen - 1), $cvvLen, '0', STR_PAD_LEFT);

        return ['brand' => $brand, 'number' => $formatted, 'expiry' => "{$month}/{$year}", 'cvv' => $cvv];
    }

    private function luhnCheckDigit(string $number): int
    {
        $sum = 0;
        $flip = true;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = (int) $number[$i];
            if ($flip) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
            $flip = ! $flip;
        }

        return (10 - ($sum % 10)) % 10;
    }

    private function generateIban(Generator $faker, string $countryCode): string
    {
        $lengths = [
            'US' => 20, 'GB' => 22, 'DE' => 22, 'FR' => 27, 'IT' => 27,
            'NL' => 18, 'ES' => 24, 'AU' => 20, 'CA' => 20, 'ID' => 20,
            'JP' => 20, 'KR' => 20, 'SG' => 20, 'TW' => 20,
        ];
        $len = $lengths[$countryCode] ?? 20;
        $digits = '';
        for ($i = 0; $i < $len - 4; $i++) {
            $digits .= $faker->numberBetween(0, 9);
        }
        $check = str_pad((string) $faker->numberBetween(2, 98), 2, '0', STR_PAD_LEFT);

        return "{$countryCode}{$check}{$digits}";
    }

    private function generateBitcoinAddress(Generator $faker): string
    {
        $chars = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $prefix = $faker->randomElement(['1', '3', 'bc1q']);
        $len = $faker->numberBetween(25, 34);
        $addr = $prefix;
        for ($i = strlen($prefix); $i < $len; $i++) {
            $addr .= $chars[$faker->numberBetween(0, strlen($chars) - 1)];
        }

        return $addr;
    }

    private function generateNetworkInfo(Generator $faker): array
    {
        $ipv4 = implode('.', [
            $faker->numberBetween(1, 223),
            $faker->numberBetween(0, 255),
            $faker->numberBetween(0, 255),
            $faker->numberBetween(1, 254),
        ]);

        $ipv6Parts = [];
        for ($i = 0; $i < 8; $i++) {
            $ipv6Parts[] = sprintf('%04x', $faker->numberBetween(0, 0xFFFF));
        }

        $mac = [];
        for ($i = 0; $i < 6; $i++) {
            $mac[] = sprintf('%02x', $faker->numberBetween(0, 255));
        }
        $mac[0] = sprintf('%02x', hexdec($mac[0]) | 0x02);

        return [$ipv4, implode(':', $ipv6Parts), implode(':', $mac)];
    }

    private function generateUserAgent(Generator $faker): array
    {
        [$browser, $version] = $faker->randomElement(self::$browsers);
        $os = $faker->randomElement(self::$operatingSystems);

        $osStr = match (true) {
            str_contains($os, 'Windows') => 'Windows NT 10.0; Win64; x64',
            str_contains($os, 'macOS 14') => 'Macintosh; Intel Mac OS X 14_0',
            str_contains($os, 'macOS 13') => 'Macintosh; Intel Mac OS X 13_0',
            str_contains($os, 'Ubuntu') => 'X11; Ubuntu; Linux x86_64',
            str_contains($os, 'iOS') => 'iPhone; CPU iPhone OS 17_0 like Mac OS X',
            str_contains($os, 'Android') => 'Linux; Android 14; Pixel 8',
            str_contains($os, 'iPad') => 'iPad; CPU OS 17_0 like Mac OS X',
            default => 'X11; Linux x86_64',
        };

        $ua = match ($browser) {
            'Chrome' => "Mozilla/5.0 ({$osStr}) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/{$version} Safari/537.36",
            'Firefox' => "Mozilla/5.0 ({$osStr}; rv:{$version}) Gecko/20100101 Firefox/{$version}",
            'Safari' => "Mozilla/5.0 ({$osStr}) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/{$version} Safari/605.1.15",
            'Edge' => "Mozilla/5.0 ({$osStr}) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/{$version} Safari/537.36 Edg/{$version}",
            'Brave' => "Mozilla/5.0 ({$osStr}) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/{$version} Safari/537.36",
            default => "Mozilla/5.0 ({$osStr}) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/{$version} Safari/537.36",
        };

        return ['ua' => $ua, 'browser' => $browser, 'version' => $version, 'os' => $os];
    }

    /**
     * Return a single ASCII letter from a name (safe for CJK multibyte names).
     * Falls back to 'x' when no ASCII letter exists.
     */
    private function toAsciiInitial(string $name): string
    {
        // Transliterate to ASCII first
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        if (! $ascii) {
            $ascii = preg_replace('/[^\x20-\x7E]/u', '', $name);
        }
        $ascii = preg_replace('/[^a-zA-Z]/', '', (string) $ascii);

        return strtolower(strlen($ascii) > 0 ? $ascii[0] : 'x');
    }

    /**
     * Transliterate a name to an ASCII-only slug safe for email addresses.
     */
    private function toAsciiSlug(string $name): string
    {
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        if (! $ascii) {
            $ascii = preg_replace('/[^\x20-\x7E]/u', '', $name);
        }
        $ascii = preg_replace('/[^a-zA-Z0-9]+/', '', (string) $ascii);

        return strtolower(strlen($ascii) > 0 ? $ascii : 'user');
    }
}
