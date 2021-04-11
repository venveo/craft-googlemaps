<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by the Google Maps API.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2021 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\helpers;

use doublesecretagency\googlemaps\models\Lookup;

/**
 * Class GeocodingHelper
 * @since 4.0.0
 */
class GeocodingHelper
{

    /**
     * @var array Countries whose street number precedes the street name.
     */
    private static $_countriesWithNumberFirst = [
        'Australia',
        'Canada',
        'France',
        'Hong Kong',
        'India',
        'Ireland',
        'Malaysia',
        'New Zealand',
        'Pakistan',
        'Singapore',
        'Sri Lanka',
        'Taiwan',
        'Thailand',
        'United Kingdom',
        'United States',
    ];

    /**
     * @var array Countries with a comma after the street name.
     */
    private static $_companiesWithCommaAfterStreet = [
        'Italy',
    ];

    // ========================================================================= //

    /**
     * Clean and format raw address data.
     *
     * @param array $unformatted
     * @return array
     */
    public static function restructureComponents($unformatted)
    {
        // Initialize formatted address data
        $formatted = [];

        // Loop through address components
        foreach ($unformatted['address_components'] as $component) {
            // If types aren't specified, skip
            if (!isset($component['types']) || !$component['types']) {
                continue;
            }
            // Generate formatted array of address data
            $c = $component['types'][0];
            switch ($c) {
                case 'locality':
                case 'country':
                    $formatted[$c] = $component['long_name'];
                    break;
                default:
                    $formatted[$c] = $component['short_name'];
                    break;
            }
        }

        // Get components
        $streetNumber = ($formatted['street_number'] ?? null);
        $streetName   = ($formatted['route'] ?? null);
        $city         = ($formatted['locality'] ?? null);
        $state        = ($formatted['administrative_area_level_1'] ?? null);
        $zip          = ($formatted['postal_code'] ?? null);
        $country      = ($formatted['country'] ?? null);

        // Country-specific adjustments
        switch ($country) {
            case 'United Kingdom':
                $city  = ($formatted['postal_town'] ?? null);
                $state = ($formatted['administrative_area_level_2'] ?? null);
                break;
        }

        // Get coordinates
        $lat = ($unformatted['geometry']['location']['lat'] ?? null);
        $lng = ($unformatted['geometry']['location']['lng'] ?? null);

        // Default street format
        $street1 = "{$streetName} {$streetNumber}";

        // If country uses a different street format, apply that format instead
        if (in_array($country, static::$_countriesWithNumberFirst, true)) {
            $street1 = "{$streetNumber} {$streetName}";
        } else if (in_array($country, static::$_companiesWithCommaAfterStreet, true)) {
            $street1 = "{$streetName}, {$streetNumber}";
        }

        // Trim whitespace from street
        $street1 = (trim($street1) ?: null);

        // Return formatted address data
        return [
            'street1' => $street1,
            'street2' => null,
            'city'    => $city,
            'state'   => $state,
            'zip'     => $zip,
            'country' => $country,
            'lat'     => $lat,
            'lng'     => $lng,
            'raw'     => $unformatted,
        ];
    }

    // ========================================================================= //

    /**
     * Initialize a geocoding lookup by configuring a Lookup Model.
     *
     * @param array|string $target
     * @return Lookup|false
     */
    public static function lookup($target = [])
    {
        // If a string target was specified, convert to array
        if (is_string($target)) {
            $target = ['address' => $target];
        }

        // If target is not an array, bail
        if (!is_array($target)) {
            return false;
        }

        // If no target specified, bail
        if (!isset($target['address']) || !$target['address']) {
            return false;
        }

        // Create a fresh lookup
        return new Lookup($target);
    }

}
