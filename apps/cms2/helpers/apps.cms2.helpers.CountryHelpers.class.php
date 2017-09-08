<?php

/**
 * TODO: seria util tener todos estos datos en la db.
 */

class CountryHelpers {
    
   // Mas datos
   // - http://cloford.com/resources/codes/index.htm
   // - http://msdn.microsoft.com/en-us/library/ee783932(v=cs.10).aspx
   // - http://www.geekality.net/2011/08/21/country-names-continent-names-and-iso-3166-codes-for-mysql/
   // - http://en.wikipedia.org/wiki/ISO_3166-1
   
   // Codigos de regiones dento de paises
   // - http://en.wikipedia.org/wiki/List_of_FIPS_region_codes

   // Continentes por codigo y nombre en espa;ol
   private static $continents = array(
     'AF'=> 'Africa',
     'AS'=> 'Asia',
     'EU'=> 'Europa',
     'NA'=> 'America del norte',
     'SA'=> 'America del sur',
     'OC'=> 'Oceania',
     'AN'=> 'Antartida'
   );

   // Paises por codigo, ordenados por nombre de pais en espa;ol
   // FIXME: cuidado que los codigos posta son el mayusculas!
   private static $countries = array(
'af'=>'Afganistán',
'al'=>'Albania',
'de'=>'Alemania',
'dz'=>'Argelia',
'ad'=>'Andorra',
'ao'=>'Angola',
'ai'=>'Anguila',
'aq'=>'Antártida',
'ag'=>'Antigua y Barbuda',
'an'=>'Antillas Holandesas',
'ar'=>'Argentina',
'am'=>'Armenia',
'sa'=>'Arabia Saudí',
'aw'=>'Aruba',
'au'=>'Australia',
'at'=>'Austria',
'az'=>'Azerbaiyán',

'bs'=>'Bahamas',
'bh'=>'Bahréin',
'bd'=>'Bangladesh',
'bb'=>'Barbados',
'by'=>'Bielorusia',
'be'=>'Bélgica',
'bz'=>'Belice',
'bj'=>'Benin',
'bm'=>'Bermuda',
'bt'=>'Bután',
'bo'=>'Bolivia',
'ba'=>'Bosnia y Herzegovina',
'bw'=>'Botswana',
'br'=>'Brasil',
'bn'=>'Brunei',
'bg'=>'Bulgaria',
'bf'=>'Burkina Faso',
'bi'=>'Burundi',
'bq'=>'Bonaire, Sint Eustatius and Saba',

'kh'=>'Camboya',
'cm'=>'Camerún',
'ca'=>'Canadá',
'cv'=>'Cabo Verde',
'td'=>'Chad',
'cl'=>'Chile',
'cn'=>'China',
'cy'=>'Chipre',
'co'=>'Colombia',
'km'=>'Comores',
'cg'=>'Congo',
'kp'=>'Corea del Norte',
'kr'=>'Corea del Sur',
'cr'=>'Costa Rica',
'ci'=>'Costa de Marfil',
'hr'=>'Croacia',
'cu'=>'Cuba',
'cw'=>'Curaçao',

'dk'=>'Dinamarca',
'dj'=>'Djibouti',
'dm'=>'Dominica',

'ec'=>'Ecuador',
'eg'=>'Egipto',
'sv'=>'El Salvador',
'er'=>'Eritrea',
'es'=>'España',
'ee'=>'Estonia',
'et'=>'Etiopía',
'fm'=>'Estados Federados de Micronesia',
'sk'=>'Eslovaquia',
'si'=>'Eslovenia',
'ae'=>'Emiratos Árabes Unidos',
'us'=>'Estados Unidos',

'fj'=>'Fiji',
'fi'=>'Finlandia',
'ph'=>'Filipinas',
'fr'=>'Francia',

'ga'=>'Gabón',
'gm'=>'Gambia',
'gf'=>'Guayana Francesa',
'gq'=>'Guinea Ecuatorial',
'ge'=>'Georgia',
'gh'=>'Ghana',
'gi'=>'Gibraltar',
'gr'=>'Grecia',
'gl'=>'Groenlandia',
'gd'=>'Grenada',
'gp'=>'Guadalupe',
'gu'=>'Guam',
'gt'=>'Guatemala',
'gn'=>'Guinea',
'gw'=>'Guinea-Bissau',
'gy'=>'Guyana',

'ht'=>'Haití',
'hn'=>'Honduras',
'hk'=>'Hong Kong',
'hu'=>'Hungría',

'hm'=>'Islas Heard y McDonald',
'is'=>'Islandia',
'in'=>'India',
'id'=>'Indonesia',
'ir'=>'Irán',
'iq'=>'Iraq',
'ie'=>'Irlanda',
'il'=>'Israel',
'it'=>'Italia',
'im'=>'Isla de Man',
'ax'=>'Islas Aland ',
'ky'=>'Islas Caimán',
'mh'=>'Islas Marshall',
'cx'=>'Isla de Navidad',
'cc'=>'Islas Cocos (Keeling)',
'ck'=>'Islas Cook',
'fk'=>'Islas Malvinas',
'fo'=>'Islas Faroe',
'mp'=>'Islas Mariana del Norte',
'pn'=>'Islas Pitcairn',
'nf'=>'Isla Norfolk',
'pi'=>'Islas Spratly',
'sb'=>'Islas Salomón',
'gs'=>'Islas Georgia del Sur e Islas Sandwich del Sur',
'sj'=>'Islas Svalbard y Jan Mayen',
'tc'=>'Islas Turks y Caicos',
'um'=>'Islas Menores de los Estados Unidos',
'vi'=>'Islas Vírgenes de los Estados Unidos',
'bv'=>'Isla Bouvet',
'vg'=>'Islas Vírgenes (Reino Unido)',

'jm'=>'Jamaica',
'jp'=>'Japón',
'je'=>'Jersey',
'jo'=>'Jordania',

'kz'=>'Kazajstán',
'ke'=>'Kenia',
'ki'=>'Kiribati',
'kw'=>'Kuwait',
'kg'=>'Kirguistán',

'la'=>'Laos',
'lv'=>'Letonia',
'lb'=>'Líbano',
'ls'=>'Lesotho',
'lr'=>'Liberia',
'ly'=>'Libia',
'li'=>'Liechtenstein',
'lt'=>'Lituania',
'lu'=>'Luxemburgo',

'mo'=>'Macau',
'mk'=>'Macedonia',
'mg'=>'Madagascar',
'mw'=>'Malawi',
'my'=>'Malasia',
'mv'=>'Maldivas',
'ml'=>'Mali',
'mt'=>'Malta',
'mq'=>'Martinica',
'mr'=>'Mauritania',
'mu'=>'Mauricio',
'yt'=>'Mayotte',
'mx'=>'México',
'md'=>'Moldova',
'mc'=>'Mónaco',
'mn'=>'Mongolia',
'ms'=>'Montserrat',
'ma'=>'Marruecos',
'mz'=>'Mozambique',
'mm'=>'Myanmar',

'na'=>'Namibia',
'nr'=>'Nauru',
'np'=>'Nepal',
'nc'=>'Nueva Caledonia',
'nz'=>'Nueva Zelanda',
'ni'=>'Nicaragua',
'ne'=>'Níger',
'ng'=>'Nigeria',
'nu'=>'Niue',
'no'=>'Noruega',

'om'=>'Omán',

'pk'=>'Pakistán',
'pw'=>'Palau',
'pa'=>'Panamá',

'pg'=>'Papúa-Nueva Guinea',
'py'=>'Paraguay',
'pe'=>'Perú',
'pl'=>'Polonia',
'pt'=>'Portugal',
'pr'=>'Puerto Rico',
'pf'=>'Polinesia Francesa',
'nl'=>'Países Bajos',

'qa'=>'Qatar',

'uk'=>'Reino Unido',
//'gb'=>'Reino Unido',
'cf'=>'República Centroafricana',
'cz'=>'República Checa',
'cd'=>'República Democrática del Congo',
'do'=>'República Dominicana',
're'=>'Reunión',
'ro'=>'Rumanía',
'ru'=>'Rusia',
'rw'=>'Ruanda',

'kn'=>'Saint Kitts y Nevis',
'bl'=>'Saint Barthélemy',
'mf'=>'Saint Martin',
'ws'=>'Samoa',
'as'=>'Samoa Americana',
'sh'=>'Santa Elena y Dependencias',
'lc'=>'Santa Lucía',
'pm'=>'San Pedro y Miquelón',
'vc'=>'San Vicente y Granadinas',
'sm'=>'San Marino',
'st'=>'Santo Tomé y Príncipe',
'sn'=>'Senegal',
'cs'=>'Serbia y Montenegro',
'sc'=>'Seychelles',
'sl'=>'Sierra Leona',
'sg'=>'Singapur',
'so'=>'Somalia',
'za'=>'Sudáfrica',
'eh'=>'Sáhara Occidental',
'lk'=>'Sri Lanka',
'sd'=>'Sudán',
'sr'=>'Surinám',
'sz'=>'Swazilandia',
'se'=>'Suecia',
'ch'=>'Suiza',
'sy'=>'Siria',

'io'=>'Territorio Británico en el Océano Indico',
'tf'=>'Territorios Franceses del Sur',
'tl'=>'Timor Occidental',
'tw'=>'Taiwán',
'tj'=>'Tayikistán',
'tz'=>'Tanzania',
'th'=>'Tailandia',
'tg'=>'Togo',
'tk'=>'Tokelau',
'to'=>'Tonga',
'tt'=>'Trinidad y Tobago',
'tn'=>'Túnez',
'tr'=>'Turquía',
'tm'=>'Turkmenistán',
'tv'=>'Tuvalu',

'ua'=>'Ucrania',
'ug'=>'Uganda',
'uy'=>'Uruguay',
'uz'=>'Uzbekistán',

'vu'=>'Vanuatu',
'va'=>'Vaticano',
've'=>'Venezuela',
'vn'=>'Vietnám',

'wf'=>'Wallis y Futuna',

'ye'=>'Yemen',
'zm'=>'Zambia',
'zw'=>'Zimbabwe'
   );
   
   /**
    * Paises agrupados por continente
    */
   private static $continentCountries = array(
     'SA' => array(
       'ar', 'bo', 'br', 'cl', 'co', 'ec', 'fk', 'gf', 'gy', 'pe', 'py', 'sr', 'uy', 've'
     ),
     'EU' => array(
       'ad', 'al', 'at', 'ax', 'ba', 'be', 'bg', 'by', 'ch', 'cz', 'de', 'dk', 'ee', 'es', 'fi', 'fo', 'fr', 'uk', 'gi', 'gr', 'hr', 'hu', 'ie', 'im', 'is', 'it', 'je', 'li', 'lt'
     ),
     'OC' => array(
       'as', 'au', 'ck', 'fj', 'fm', 'gu', 'ki', 'mh', 'mp', 'nc', 'nf', 'nr', 'nu', 'nz', 'pf', 'pg', 'pn', 'pw', 'sb', 'tk', 'to', 'tv', 'um', 'vu', 'wf', 'ws' 
     ),
     'AF' => array(
       'ao', 'bf', 'bi', 'bj', 'bw', 'cd', 'cf', 'cg', 'ci', 'cm', 'cv', 'dj', 'dz', 'eg', 'eh', 'er', 'et', 'ga', 'gh', 'gm', 'gn', 'gq', 'gw', 'ke', 'km', 'lr', 'ls', 'ly', 'ma', 'mg'
     ),
     'AS' => array(
       'ae', 'af', 'am', 'az', 'bd', 'bh', 'bn', 'bt', 'cc', 'cn', 'cx', 'cy', 'ge', 'hk', 'id', 'il', 'in', 'io', 'iq', 'ir', 'jo', 'jp', 'kg', 'kh', 'kp', 'kr', 'kw', 'kz', 'la', 'lb' 
     ),
     'NA' => array(
       'ag', 'ai', 'aw', 'bb', 'bl', 'bm', 'bq', 'bs', 'bz', 'ca', 'cr', 'cu', 'cw', 'dm', 'do', 'gd', 'gl', 'gp', 'gt', 'hn', 'ht', 'jm', 'kn', 'ky', 'lc', 'mf', 'mq', 'ms', 'mx', 'ni' 
     ),
     'AN' => array(
       'aq', 'bv', 'gs', 'hm', 'tf'
     )
   );
   
   public static function getCountries()
   {
      return self::$countries;
   }
   
   public static function getCountryName($code)
   {
      if (isset(self::$countries[$code])) return self::$countries[$code];
   }
   
   public static function getContinents()
   {
      return self::$continents;
   }
   
   public static function getContinentName($code)
   {
      if (isset(self::$continents[$code])) return self::$continents[$code];
   }
   
   public static function getContinentCountries()
   {
      return self::$continentCountries;
   }

}

?>