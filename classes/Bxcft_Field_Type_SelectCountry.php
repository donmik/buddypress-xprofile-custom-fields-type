<?php
/**
 * Select Country
 */
if (!class_exists('Bxcft_Field_Type_SelectCountry'))
{
    class Bxcft_Field_Type_SelectCountry extends BP_XProfile_Field_Type
    {

        public static function getCountries() {
            return array(
                'AF' => array( 'name' => __('Afghanistan', 'bxcft'), 'cca3' => 'AFG'),
                'AX' => array( 'name' => __('Aland Islands', 'bxcft'), 'cca3' => 'ALA'),
                'AL' => array( 'name' => __('Albania', 'bxcft'), 'cca3' => 'ALB'),
                'DZ' => array( 'name' => __('Algeria', 'bxcft'), 'cca3' => 'DZA'),
                'AS' => array( 'name' => __('American Samoa', 'bxcft'), 'cca3' => 'ASM'),
                'AD' => array( 'name' => __('Andorra', 'bxcft'), 'cca3' => 'AND'),
                'AO' => array( 'name' => __('Angola', 'bxcft'), 'cca3' => 'AGO'),
                'AI' => array( 'name' => __('Anguilla', 'bxcft'), 'cca3' => 'AIA'),
                'AQ' => array( 'name' => __('Antarctica', 'bxcft'), 'cca3' => 'ATA'),
                'AG' => array( 'name' => __('Antigua and Barbuda', 'bxcft'), 'cca3' => 'ATG'),
                'AR' => array( 'name' => __('Argentina', 'bxcft'), 'cca3' => 'ARG'),
                'AM' => array( 'name' => __('Armenia', 'bxcft'), 'cca3' => 'ARM'),
                'AW' => array( 'name' => __('Aruba', 'bxcft'), 'cca3' => 'ABW'),
                'AU' => array( 'name' => __('Australia', 'bxcft'), 'cca3' => 'AUS'),
                'AT' => array( 'name' => __('Austria', 'bxcft'), 'cca3' => 'AUT'),
                'AZ' => array( 'name' => __('Azerbaijan', 'bxcft'), 'cca3' => 'AZE'),
                'BS' => array( 'name' => __('Bahamas', 'bxcft'), 'cca3' => 'BHS'),
                'BH' => array( 'name' => __('Bahrain', 'bxcft'), 'cca3' => 'BHR'),
                'BD' => array( 'name' => __('Bangladesh', 'bxcft'), 'cca3' => 'BGD'),
                'BB' => array( 'name' => __('Barbados', 'bxcft'), 'cca3' => 'BRB'),
                'BY' => array( 'name' => __('Belarus', 'bxcft'), 'cca3' => 'BLR'),
                'BE' => array( 'name' => __('Belgium', 'bxcft'), 'cca3' => 'BEL'),
                'BZ' => array( 'name' => __('Belize', 'bxcft'), 'cca3' => 'BLZ'),
                'BJ' => array( 'name' => __('Benin', 'bxcft'), 'cca3' => 'BEN'),
                'BM' => array( 'name' => __('Bermuda', 'bxcft'), 'cca3' => 'BMU'),
                'BT' => array( 'name' => __('Bhutan', 'bxcft'), 'cca3' => 'BTN'),
                'BO' => array( 'name' => __('Bolivia (Plurinational State of)', 'bxcft'), 'cca3' => 'BOL'),
                'BQ' => array( 'name' => __('Bonaire, Sint Eustatius and Saba', 'bxcft'), 'cca3' => 'BES'),
                'BA' => array( 'name' => __('Bosnia and Herzegovina', 'bxcft'), 'cca3' => 'BIH'),
                'BW' => array( 'name' => __('Botswana', 'bxcft'), 'cca3' => 'BWA'),
                'BV' => array( 'name' => __('Bouvet Island', 'bxcft'), 'cca3' => 'BVT'),
                'BR' => array( 'name' => __('Brazil', 'bxcft'), 'cca3' => 'BRA'),
                'IO' => array( 'name' => __('British Indian Ocean Territory', 'bxcft'), 'cca3' => 'IOT'),
                'BN' => array( 'name' => __('Brunei Darussalam', 'bxcft'), 'cca3' => 'BRN'),
                'BG' => array( 'name' => __('Bulgaria', 'bxcft'), 'cca3' => 'BGR'),
                'BF' => array( 'name' => __('Burkina Faso', 'bxcft'), 'cca3' => 'BFA'),
                'BI' => array( 'name' => __('Burundi', 'bxcft'), 'cca3' => 'BDI'),
                'CV' => array( 'name' => __('Cabo Verde', 'bxcft'), 'cca3' => 'CPV'),
                'KH' => array( 'name' => __('Cambodia', 'bxcft'), 'cca3' => 'KHM'),
                'CM' => array( 'name' => __('Cameroon', 'bxcft'), 'cca3' => 'CMR'),
                'CA' => array( 'name' => __('Canada', 'bxcft'), 'cca3' => 'CAN'),
                'KY' => array( 'name' => __('Cayman Islands', 'bxcft'), 'cca3' => 'CYM'),
                'CF' => array( 'name' => __('Central African Republic', 'bxcft'), 'cca3' => 'CAF'),
                'TD' => array( 'name' => __('Chad', 'bxcft'), 'cca3' => 'TCD'),
                'CL' => array( 'name' => __('Chile', 'bxcft'), 'cca3' => 'CHL'),
                'CN' => array( 'name' => __('China', 'bxcft'), 'cca3' => 'CHN'),
                'CX' => array( 'name' => __('Christmas Island', 'bxcft'), 'cca3' => 'CXR'),
                'CC' => array( 'name' => __('Cocos (Keeling) Islands', 'bxcft'), 'cca3' => 'CCK'),
                'CO' => array( 'name' => __('Colombia', 'bxcft'), 'cca3' => 'COL'),
                'KM' => array( 'name' => __('Comoros', 'bxcft'), 'cca3' => 'COM'),
                'CG' => array( 'name' => __('Congo', 'bxcft'), 'cca3' => 'COG'),
                'CD' => array( 'name' => __('Congo (Democratic Republic of the)', 'bxcft'), 'cca3' => 'COD'),
                'CK' => array( 'name' => __('Cook Islands', 'bxcft'), 'cca3' => 'COK'),
                'CR' => array( 'name' => __('Costa Rica', 'bxcft'), 'cca3' => 'CRI'),
                'CI' => array( 'name' => __('Côte d\'Ivoire', 'bxcft'), 'cca3' => 'CIV'),
                'HR' => array( 'name' => __('Croatia', 'bxcft'), 'cca3' => 'HRV'),
                'CU' => array( 'name' => __('Cuba', 'bxcft'), 'cca3' => 'CUB'),
                'CW' => array( 'name' => __('Curaçao', 'bxcft'), 'cca3' => 'CUW'),
                'CY' => array( 'name' => __('Cyprus', 'bxcft'), 'cca3' => 'CYP'),
                'CZ' => array( 'name' => __('Czechia', 'bxcft'), 'cca3' => 'CZE'),
                'DK' => array( 'name' => __('Denmark', 'bxcft'), 'cca3' => 'DNK'),
                'DJ' => array( 'name' => __('Djibouti', 'bxcft'), 'cca3' => 'DJI'),
                'DM' => array( 'name' => __('Dominica', 'bxcft'), 'cca3' => 'DMA'),
                'DO' => array( 'name' => __('Dominican Republic', 'bxcft'), 'cca3' => 'DOM'),
                'EC' => array( 'name' => __('Ecuador', 'bxcft'), 'cca3' => 'ECU'),
                'EG' => array( 'name' => __('Egypt', 'bxcft'), 'cca3' => 'EGY'),
                'SV' => array( 'name' => __('El Salvador', 'bxcft'), 'cca3' => 'SLV'),
                'GQ' => array( 'name' => __('Equatorial Guinea', 'bxcft'), 'cca3' => 'GNQ'),
                'ER' => array( 'name' => __('Eritrea', 'bxcft'), 'cca3' => 'ERI'),
                'EE' => array( 'name' => __('Estonia', 'bxcft'), 'cca3' => 'EST'),
                'ET' => array( 'name' => __('Ethiopia', 'bxcft'), 'cca3' => 'ETH'),
                'FK' => array( 'name' => __('Falkland Islands (Malvinas)', 'bxcft'), 'cca3' => 'FLK'),
                'FO' => array( 'name' => __('Faroe Islands', 'bxcft'), 'cca3' => 'FRO'),
                'FJ' => array( 'name' => __('Fiji', 'bxcft'), 'cca3' => 'FJI'),
                'FI' => array( 'name' => __('Finland', 'bxcft'), 'cca3' => 'FIN'),
                'FR' => array( 'name' => __('France', 'bxcft'), 'cca3' => 'FRA'),
                'GF' => array( 'name' => __('French Guiana', 'bxcft'), 'cca3' => 'GUF'),
                'PF' => array( 'name' => __('French Polynesia', 'bxcft'), 'cca3' => 'PYF'),
                'TF' => array( 'name' => __('French Southern Territories', 'bxcft'), 'cca3' => 'ATF'),
                'GA' => array( 'name' => __('Gabon', 'bxcft'), 'cca3' => 'GAB'),
                'GM' => array( 'name' => __('Gambia', 'bxcft'), 'cca3' => 'GMB'),
                'GE' => array( 'name' => __('Georgia', 'bxcft'), 'cca3' => 'GEO'),
                'DE' => array( 'name' => __('Germany', 'bxcft'), 'cca3' => 'DEU'),
                'GH' => array( 'name' => __('Ghana', 'bxcft'), 'cca3' => 'GHA'),
                'GI' => array( 'name' => __('Gibraltar', 'bxcft'), 'cca3' => 'GIB'),
                'GR' => array( 'name' => __('Greece', 'bxcft'), 'cca3' => 'GRC'),
                'GL' => array( 'name' => __('Greenland', 'bxcft'), 'cca3' => 'GRL'),
                'GD' => array( 'name' => __('Grenada', 'bxcft'), 'cca3' => 'GRD'),
                'GP' => array( 'name' => __('Guadeloupe', 'bxcft'), 'cca3' => 'GLP'),
                'GU' => array( 'name' => __('Guam', 'bxcft'), 'cca3' => 'GUM'),
                'GT' => array( 'name' => __('Guatemala', 'bxcft'), 'cca3' => 'GTM'),
                'GG' => array( 'name' => __('Guernsey', 'bxcft'), 'cca3' => 'GGY'),
                'GN' => array( 'name' => __('Guinea', 'bxcft'), 'cca3' => 'GIN'),
                'GW' => array( 'name' => __('Guinea-Bissau', 'bxcft'), 'cca3' => 'GNB'),
                'GY' => array( 'name' => __('Guyana', 'bxcft'), 'cca3' => 'GUY'),
                'HT' => array( 'name' => __('Haiti', 'bxcft'), 'cca3' => 'HTI'),
                'HM' => array( 'name' => __('Heard Island and McDonald Islands', 'bxcft'), 'cca3' => 'HMD'),
                'VA' => array( 'name' => __('Holy See', 'bxcft'), 'cca3' => 'VAT'),
                'HN' => array( 'name' => __('Honduras', 'bxcft'), 'cca3' => 'HND'),
                'HK' => array( 'name' => __('Hong Kong', 'bxcft'), 'cca3' => 'HKG'),
                'HU' => array( 'name' => __('Hungary', 'bxcft'), 'cca3' => 'HUN'),
                'IS' => array( 'name' => __('Iceland', 'bxcft'), 'cca3' => 'ISL'),
                'IN' => array( 'name' => __('India', 'bxcft'), 'cca3' => 'IND'),
                'ID' => array( 'name' => __('Indonesia', 'bxcft'), 'cca3' => 'IDN'),
                'IR' => array( 'name' => __('Iran (Islamic Republic of)', 'bxcft'), 'cca3' => 'IRN'),
                'IQ' => array( 'name' => __('Iraq', 'bxcft'), 'cca3' => 'IRQ'),
                'IE' => array( 'name' => __('Ireland', 'bxcft'), 'cca3' => 'IRL'),
                'IM' => array( 'name' => __('Isle of Man', 'bxcft'), 'cca3' => 'IMN'),
                'IL' => array( 'name' => __('Israel', 'bxcft'), 'cca3' => 'ISR'),
                'IT' => array( 'name' => __('Italy', 'bxcft'), 'cca3' => 'ITA'),
                'JM' => array( 'name' => __('Jamaica', 'bxcft'), 'cca3' => 'JAM'),
                'JP' => array( 'name' => __('Japan', 'bxcft'), 'cca3' => 'JPN'),
                'JE' => array( 'name' => __('Jersey', 'bxcft'), 'cca3' => 'JEY'),
                'JO' => array( 'name' => __('Jordan', 'bxcft'), 'cca3' => 'JOR'),
                'KZ' => array( 'name' => __('Kazakhstan', 'bxcft'), 'cca3' => 'KAZ'),
                'KE' => array( 'name' => __('Kenya', 'bxcft'), 'cca3' => 'KEN'),
                'KI' => array( 'name' => __('Kiribati', 'bxcft'), 'cca3' => 'KIR'),
                'KP' => array( 'name' => __('Korea (Democratic People\'s Republic of)', 'bxcft'), 'cca3' => 'PRK'),
                'KR' => array( 'name' => __('Korea (Republic of)', 'bxcft'), 'cca3' => 'KOR'),
                'KW' => array( 'name' => __('Kuwait', 'bxcft'), 'cca3' => 'KWT'),
                'KG' => array( 'name' => __('Kyrgyzstan', 'bxcft'), 'cca3' => 'KGZ'),
                'LA' => array( 'name' => __('Lao People\'s Democratic Republic', 'bxcft'), 'cca3' => 'LAO'),
                'LV' => array( 'name' => __('Latvia', 'bxcft'), 'cca3' => 'LVA'),
                'LB' => array( 'name' => __('Lebanon', 'bxcft'), 'cca3' => 'LBN'),
                'LS' => array( 'name' => __('Lesotho', 'bxcft'), 'cca3' => 'LSO'),
                'LR' => array( 'name' => __('Liberia', 'bxcft'), 'cca3' => 'LBR'),
                'LY' => array( 'name' => __('Libya', 'bxcft'), 'cca3' => 'LBY'),
                'LI' => array( 'name' => __('Liechtenstein', 'bxcft'), 'cca3' => 'LIE'),
                'LT' => array( 'name' => __('Lithuania', 'bxcft'), 'cca3' => 'LTU'),
                'LU' => array( 'name' => __('Luxembourg', 'bxcft'), 'cca3' => 'LUX'),
                'MO' => array( 'name' => __('Macao', 'bxcft'), 'cca3' => 'MAC'),
                'MK' => array( 'name' => __('Macedonia (the former Yugoslav Republic of)', 'bxcft'), 'cca3' => 'MKD'),
                'MG' => array( 'name' => __('Madagascar', 'bxcft'), 'cca3' => 'MDG'),
                'MW' => array( 'name' => __('Malawi', 'bxcft'), 'cca3' => 'MWI'),
                'MY' => array( 'name' => __('Malaysia', 'bxcft'), 'cca3' => 'MYS'),
                'MV' => array( 'name' => __('Maldives', 'bxcft'), 'cca3' => 'MDV'),
                'ML' => array( 'name' => __('Mali', 'bxcft'), 'cca3' => 'MLI'),
                'MT' => array( 'name' => __('Malta', 'bxcft'), 'cca3' => 'MLT'),
                'MH' => array( 'name' => __('Marshall Islands', 'bxcft'), 'cca3' => 'MHL'),
                'MQ' => array( 'name' => __('Martinique', 'bxcft'), 'cca3' => 'MTQ'),
                'MR' => array( 'name' => __('Mauritania', 'bxcft'), 'cca3' => 'MRT'),
                'MU' => array( 'name' => __('Mauritius', 'bxcft'), 'cca3' => 'MUS'),
                'YT' => array( 'name' => __('Mayotte', 'bxcft'), 'cca3' => 'MYT'),
                'MX' => array( 'name' => __('Mexico', 'bxcft'), 'cca3' => 'MEX'),
                'FM' => array( 'name' => __('Micronesia (Federated States of)', 'bxcft'), 'cca3' => 'FSM'),
                'MD' => array( 'name' => __('Moldova (Republic of)', 'bxcft'), 'cca3' => 'MDA'),
                'MC' => array( 'name' => __('Monaco', 'bxcft'), 'cca3' => 'MCO'),
                'MN' => array( 'name' => __('Mongolia', 'bxcft'), 'cca3' => 'MNG'),
                'ME' => array( 'name' => __('Montenegro', 'bxcft'), 'cca3' => 'MNE'),
                'MS' => array( 'name' => __('Montserrat', 'bxcft'), 'cca3' => 'MSR'),
                'MA' => array( 'name' => __('Morocco', 'bxcft'), 'cca3' => 'MAR'),
                'MZ' => array( 'name' => __('Mozambique', 'bxcft'), 'cca3' => 'MOZ'),
                'MM' => array( 'name' => __('Myanmar', 'bxcft'), 'cca3' => 'MMR'),
                'NA' => array( 'name' => __('Namibia', 'bxcft'), 'cca3' => 'NAM'),
                'NR' => array( 'name' => __('Nauru', 'bxcft'), 'cca3' => 'NRU'),
                'NP' => array( 'name' => __('Nepal', 'bxcft'), 'cca3' => 'NPL'),
                'NL' => array( 'name' => __('Netherlands', 'bxcft'), 'cca3' => 'NLD'),
                'NC' => array( 'name' => __('New Caledonia', 'bxcft'), 'cca3' => 'NCL'),
                'NZ' => array( 'name' => __('New Zealand', 'bxcft'), 'cca3' => 'NZL'),
                'NI' => array( 'name' => __('Nicaragua', 'bxcft'), 'cca3' => 'NIC'),
                'NE' => array( 'name' => __('Niger', 'bxcft'), 'cca3' => 'NER'),
                'NG' => array( 'name' => __('Nigeria', 'bxcft'), 'cca3' => 'NGA'),
                'NU' => array( 'name' => __('Niue', 'bxcft'), 'cca3' => 'NIU'),
                'NF' => array( 'name' => __('Norfolk Island', 'bxcft'), 'cca3' => 'NFK'),
                'MP' => array( 'name' => __('Northern Mariana Islands', 'bxcft'), 'cca3' => 'MNP'),
                'NO' => array( 'name' => __('Norway', 'bxcft'), 'cca3' => 'NOR'),
                'OM' => array( 'name' => __('Oman', 'bxcft'), 'cca3' => 'OMN'),
                'PK' => array( 'name' => __('Pakistan', 'bxcft'), 'cca3' => 'PAK'),
                'PW' => array( 'name' => __('Palau', 'bxcft'), 'cca3' => 'PLW'),
                'PS' => array( 'name' => __('Palestine, State of', 'bxcft'), 'cca3' => 'PSE'),
                'PA' => array( 'name' => __('Panama', 'bxcft'), 'cca3' => 'PAN'),
                'PG' => array( 'name' => __('Papua New Guinea', 'bxcft'), 'cca3' => 'PNG'),
                'PY' => array( 'name' => __('Paraguay', 'bxcft'), 'cca3' => 'PRY'),
                'PE' => array( 'name' => __('Peru', 'bxcft'), 'cca3' => 'PER'),
                'PH' => array( 'name' => __('Philippines', 'bxcft'), 'cca3' => 'PHL'),
                'PN' => array( 'name' => __('Pitcairn', 'bxcft'), 'cca3' => 'PCN'),
                'PL' => array( 'name' => __('Poland', 'bxcft'), 'cca3' => 'POL'),
                'PT' => array( 'name' => __('Portugal', 'bxcft'), 'cca3' => 'PRT'),
                'PR' => array( 'name' => __('Puerto Rico', 'bxcft'), 'cca3' => 'PRI'),
                'QA' => array( 'name' => __('Qatar', 'bxcft'), 'cca3' => 'QAT'),
                'RE' => array( 'name' => __('Réunion', 'bxcft'), 'cca3' => 'REU'),
                'RO' => array( 'name' => __('Romania', 'bxcft'), 'cca3' => 'ROU'),
                'RU' => array( 'name' => __('Russian Federation', 'bxcft'), 'cca3' => 'RUS'),
                'RW' => array( 'name' => __('Rwanda', 'bxcft'), 'cca3' => 'RWA'),
                'BL' => array( 'name' => __('Saint Barthélemy', 'bxcft'), 'cca3' => 'BLM'),
                'SH' => array( 'name' => __('Saint Helena, Ascension and Tristan da Cunha', 'bxcft'), 'cca3' => 'SHN'),
                'KN' => array( 'name' => __('Saint Kitts and Nevis', 'bxcft'), 'cca3' => 'KNA'),
                'LC' => array( 'name' => __('Saint Lucia', 'bxcft'), 'cca3' => 'LCA'),
                'MF' => array( 'name' => __('Saint Martin (French part)', 'bxcft'), 'cca3' => 'MAF'),
                'PM' => array( 'name' => __('Saint Pierre and Miquelon', 'bxcft'), 'cca3' => 'SPM'),
                'VC' => array( 'name' => __('Saint Vincent and the Grenadines', 'bxcft'), 'cca3' => 'VCT'),
                'WS' => array( 'name' => __('Samoa', 'bxcft'), 'cca3' => 'WSM'),
                'SM' => array( 'name' => __('San Marino', 'bxcft'), 'cca3' => 'SMR'),
                'ST' => array( 'name' => __('Sao Tome and Principe', 'bxcft'), 'cca3' => 'STP'),
                'SA' => array( 'name' => __('Saudi Arabia', 'bxcft'), 'cca3' => 'SAU'),
                'SN' => array( 'name' => __('Senegal', 'bxcft'), 'cca3' => 'SEN'),
                'RS' => array( 'name' => __('Serbia', 'bxcft'), 'cca3' => 'SRB'),
                'SC' => array( 'name' => __('Seychelles', 'bxcft'), 'cca3' => 'SYC'),
                'SL' => array( 'name' => __('Sierra Leone', 'bxcft'), 'cca3' => 'SLE'),
                'SG' => array( 'name' => __('Singapore', 'bxcft'), 'cca3' => 'SGP'),
                'SX' => array( 'name' => __('Sint Maarten (Dutch part)', 'bxcft'), 'cca3' => 'SXM'),
                'SK' => array( 'name' => __('Slovakia', 'bxcft'), 'cca3' => 'SVK'),
                'SI' => array( 'name' => __('Slovenia', 'bxcft'), 'cca3' => 'SVN'),
                'SB' => array( 'name' => __('Solomon Islands', 'bxcft'), 'cca3' => 'SLB'),
                'SO' => array( 'name' => __('Somalia', 'bxcft'), 'cca3' => 'SOM'),
                'ZA' => array( 'name' => __('South Africa', 'bxcft'), 'cca3' => 'ZAF'),
                'GS' => array( 'name' => __('South Georgia and the South Sandwich Islands', 'bxcft'), 'cca3' => 'SGS'),
                'SS' => array( 'name' => __('South Sudan', 'bxcft'), 'cca3' => 'SSD'),
                'ES' => array( 'name' => __('Spain', 'bxcft'), 'cca3' => 'ESP'),
                'LK' => array( 'name' => __('Sri Lanka', 'bxcft'), 'cca3' => 'LKA'),
                'SD' => array( 'name' => __('Sudan', 'bxcft'), 'cca3' => 'SDN'),
                'SR' => array( 'name' => __('Suriname', 'bxcft'), 'cca3' => 'SUR'),
                'SJ' => array( 'name' => __('Svalbard and Jan Mayen', 'bxcft'), 'cca3' => 'SJM'),
                'SZ' => array( 'name' => __('Swaziland', 'bxcft'), 'cca3' => 'SWZ'),
                'SE' => array( 'name' => __('Sweden', 'bxcft'), 'cca3' => 'SWE'),
                'CH' => array( 'name' => __('Switzerland', 'bxcft'), 'cca3' => 'CHE'),
                'SY' => array( 'name' => __('Syrian Arab Republic', 'bxcft'), 'cca3' => 'SYR'),
                'TW' => array( 'name' => __('Taiwan (Province of China)', 'bxcft'), 'cca3' => 'TWN'),
                'TJ' => array( 'name' => __('Tajikistan', 'bxcft'), 'cca3' => 'TJK'),
                'TZ' => array( 'name' => __('Tanzania, United Republic of', 'bxcft'), 'cca3' => 'TZA'),
                'TH' => array( 'name' => __('Thailand', 'bxcft'), 'cca3' => 'THA'),
                'TL' => array( 'name' => __('Timor-Leste', 'bxcft'), 'cca3' => 'TLS'),
                'TG' => array( 'name' => __('Togo', 'bxcft'), 'cca3' => 'TGO'),
                'TK' => array( 'name' => __('Tokelau', 'bxcft'), 'cca3' => 'TKL'),
                'TO' => array( 'name' => __('Tonga', 'bxcft'), 'cca3' => 'TON'),
                'TT' => array( 'name' => __('Trinidad and Tobago', 'bxcft'), 'cca3' => 'TTO'),
                'TN' => array( 'name' => __('Tunisia', 'bxcft'), 'cca3' => 'TUN'),
                'TR' => array( 'name' => __('Turkey', 'bxcft'), 'cca3' => 'TUR'),
                'TM' => array( 'name' => __('Turkmenistan', 'bxcft'), 'cca3' => 'TKM'),
                'TC' => array( 'name' => __('Turks and Caicos Islands', 'bxcft'), 'cca3' => 'TCA'),
                'TV' => array( 'name' => __('Tuvalu', 'bxcft'), 'cca3' => 'TUV'),
                'UG' => array( 'name' => __('Uganda', 'bxcft'), 'cca3' => 'UGA'),
                'UA' => array( 'name' => __('Ukraine', 'bxcft'), 'cca3' => 'UKR'),
                'AE' => array( 'name' => __('United Arab Emirates', 'bxcft'), 'cca3' => 'ARE'),
                'GB' => array( 'name' => __('United Kingdom of Great Britain and Northern Ireland', 'bxcft'), 'cca3' => 'GBR'),
                'US' => array( 'name' => __('United States of America', 'bxcft'), 'cca3' => 'USA'),
                'UM' => array( 'name' => __('United States Minor Outlying Islands', 'bxcft'), 'cca3' => 'UMI'),
                'UY' => array( 'name' => __('Uruguay', 'bxcft'), 'cca3' => 'URY'),
                'UZ' => array( 'name' => __('Uzbekistan', 'bxcft'), 'cca3' => 'UZB'),
                'VU' => array( 'name' => __('Vanuatu', 'bxcft'), 'cca3' => 'VUT'),
                'VE' => array( 'name' => __('Venezuela (Bolivarian Republic of)', 'bxcft'), 'cca3' => 'VEN'),
                'VN' => array( 'name' => __('Viet Nam', 'bxcft'), 'cca3' => 'VNM'),
                'VG' => array( 'name' => __('Virgin Islands (British)', 'bxcft'), 'cca3' => 'VGB'),
                'VI' => array( 'name' => __('Virgin Islands (U.S.)', 'bxcft'), 'cca3' => 'VIR'),
                'WF' => array( 'name' => __('Wallis and Futuna', 'bxcft'), 'cca3' => 'WLF'),
                'EH' => array( 'name' => __('Western Sahara', 'bxcft'), 'cca3' => 'ESH'),
                'YE' => array( 'name' => __('Yemen', 'bxcft'), 'cca3' => 'YEM'),
                'ZM' => array( 'name' => __('Zambia', 'bxcft'), 'cca3' => 'ZMB'),
                'ZW' => array( 'name' => __('Zimbabwe', 'bxcft'), 'cca3' => 'ZWE'),
            );
        }

        public function __construct() {
            parent::__construct();

            $this->name = _x( 'Country Selector', 'xprofile field type', 'bxcft' );

            $this->supports_options = true;

            $this->set_format( '/^.+$/', 'replace' );
            do_action( 'bp_xprofile_field_type_select_country', $this );
        }

        public function admin_field_html( array $raw_properties = array() ) {
            $html = $this->get_edit_field_html_elements( $raw_properties );
        ?>
            <select <?php echo $html; ?>>
                <?php bp_the_profile_field_options(); ?>
            </select>
        <?php
        }

        public function admin_new_field_html (\BP_XProfile_Field $current_field, $control_type = '')
        {
            $type = array_search( get_class( $this ), bp_xprofile_get_field_types() );
            if ( false === $type ) {
                return;
            }

            $class            = $current_field->type != $type ? 'display: none;' : '';
            $current_type_obj = bp_xprofile_create_field_type( $type );

            $options = $current_field->get_children( true );
            if ( ! $options ) {
                $options = array();
                $i       = 1;
                while ( isset( $_POST[$type . '_option'][$i] ) ) {
                    $is_default_option = true;

                    $options[] = (object) array(
                        'id'                => -1,
                        'is_default_option' => $is_default_option,
                        'name'              => sanitize_text_field( stripslashes( $_POST[$type . '_option'][$i] ) ),
                    );

                    ++$i;
                }

                if ( ! $options ) {
                    $options[] = (object) array(
                        'id'                => -1,
                        'is_default_option' => false,
                        'name'              => '',
                    );
                }
            }

            $html_countries_options = $this->generate_country_options($options[1]->name);

        ?>
            <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
                <h3><?php esc_html_e( 'Please enter options for this Field:', 'buddypress' ); ?></h3>
                <div class="inside">
                    <p>
                        <label for="<?php echo esc_attr( "{$type}_option0" ); ?>"><?php esc_html_e( 'Display type:', 'bxcft' ); ?></label>
                        <select name="<?php echo esc_attr( "{$type}_option[0]" ); ?>"
                            id="<?php echo esc_attr( "{$type}_option0" ); ?>" >
                            <option value="name" <?php selected( 'name', $options[0]->name ); ?>><?php esc_html_e( 'Country name (e.g. United States)', 'bxcft' ); ?></option>
                            <option value="cca2"    <?php selected( 'cca2',    $options[0]->name ); ?>><?php esc_html_e( 'Country code 2 (e.g. US)',  'bxcft' ); ?></option>
                            <option value="cca3"   <?php selected( 'cca3',   $options[0]->name ); ?>><?php esc_html_e( 'Country code 3 (e.g. USA)', 'bxcft' ); ?></option>
                        </select>
                    </p>
                    <p>
                        <label for="<?php echo esc_attr( "{$type}_option1" ); ?>"><?php esc_html_e( 'Default country:', 'bxcft' ); ?></label>
                        <select  name="<?php echo esc_attr( "{$type}_option[1]" ); ?>"
                            id="<?php echo esc_attr( "{$type}_option1" ); ?>" >
                            <?php echo $html_countries_options; ?>
                        </select>
                    </p>
                </div>
            </div>
        <?php

        }

        public function edit_field_html (array $raw_properties = array ())
        {
            $user_id = bp_displayed_user_id();

            if ( isset( $raw_properties['user_id'] ) ) {
                $user_id = (int) $raw_properties['user_id'];
                unset( $raw_properties['user_id'] );
            }

            // HTML5 required attribute.
            if ( bp_get_the_profile_field_is_required() ) {
                $raw_properties['required'] = 'required';
            }

            $html = $this->get_edit_field_html_elements( $raw_properties );
        ?>
            <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php esc_html_e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
            <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
            <select <?php echo $html; ?>>
                <option value=""><?php _e('Select...', 'bxcft'); ?></option>
                <?php bp_the_profile_field_options( "user_id={$user_id}" ); ?>
            </select>
        <?php
        }

        private function generate_country_options($country_selected_cca2) {
            $html = '';
            foreach (Bxcft_Field_Type_SelectCountry::getCountries() as $cca2 => $country) {
                $html .= sprintf('<option value="%s"%s>%s</option>',
                            $cca2,
                            ($country_selected_cca2==$cca2)?' selected="selected"':'',
                            $country['name']);
            }

            return $html;
        }

        public function edit_field_options_html( array $args = array() ) {
            $options        = $this->field_obj->get_children();
            $country_selected_cca2  = BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] );

            $html = '';
            if ($options) {
                if ( !empty($_POST['field_' . $this->field_obj->id]) ) {
                    $country_selected_cca2 = sanitize_text_field(  $_POST['field_' . $this->field_obj->id] );
                }
                
                // default value
                if( empty($country_selected_cca2) ){
                    $country_selected_cca2 = $options[1]->name;
                }
                $html .= $this->generate_country_options($country_selected_cca2);
            }

            echo apply_filters( 'bp_get_the_profile_field_select_country', $html, $args['type'], $country_selected_cca2, $this->field_obj->id );
        }

        /**
         * Overriden, we cannot validate against the whitelist.
         * @param type $values
         * @return type
         */
        public function is_valid( $values ) {
            $validated = false;

            // Some types of field (e.g. multi-selectbox) may have multiple values to check
            foreach ( (array) $values as $value ) {

                // Validate the $value against the type's accepted format(s).
                foreach ( $this->validation_regex as $format ) {
                    if ( 1 === preg_match( $format, $value ) ) {
                        $validated = true;
                        continue;

                    } else {
                        $validated = false;
                    }
                }
            }

            // Handle field types with accepts_null_value set if $values is an empty array
            if ( ! $validated && is_array( $values ) && empty( $values ) && $this->accepts_null_value ) {
                $validated = true;
            }

            return (bool) apply_filters( 'bp_xprofile_field_type_is_valid', $validated, $values, $this );
        }

        /**
         * Modify the appearance of value. Apply autolink if enabled.
         *
         * @param  string   $value      Original value of field
         * @param  int      $field_id   Id of field
         * @return string   Value formatted
         */
        public static function display_filter($field_value, $field_id = '') {

            $country_selected_cca2 = strtoupper(trim($field_value));
            $new_field_value = $country_selected_cca2;

            if (!empty($field_value) && !empty($field_id)) {
                $field = BP_XProfile_Field::get_instance($field_id);
                if ($field) {
                    // default options: 
                    // Display country in profile as:
                    // cca2 - US
                    // cca3 - USA
                    // name - United States
                    $display_type = 'cca2';

                    // find display type option
                    $childs = $field->get_children();
                    if (!empty($childs) && isset($childs[0])) {
                        $display_type = $childs[0]->name;
                    }

                    // change output depends on options
                    if($display_type === 'cca3' || $display_type === 'name'){
                        $countries = Bxcft_Field_Type_SelectCountry::getCountries();
                        $new_field_value = $countries[$country_selected_cca2][$display_type];
                    }

                    $do_autolink = apply_filters('bxcft_do_autolink',
                        $field->get_do_autolink());

                    if ($do_autolink) {
                        $query_arg = bp_core_get_component_search_query_arg( 'members' );
                        $search_url = add_query_arg( array(
                                    $query_arg => urlencode( $field_value )
                                ), bp_get_members_directory_permalink() );
                        $new_field_value = '<a href="' . esc_url( $search_url ) .
                                    '" rel="nofollow">' . $new_field_value . '</a>';
                    }
                }
            }

            /**
             * bxcft_select_country_display_filter
             *
             * Use this filter to modify the appearance of Selector
             * Country field value.
             * @param  $new_field_value Value of field
             * @param  $field_id Id of field.
             * @return  Filtered value of field.
             */
            return apply_filters('bxcft_select_country_display_filter',
                $new_field_value, $field_id);
        }
    }
}
