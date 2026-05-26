<?php
/**
 * Script completo para insertar TODOS los países del mundo
 * con sus códigos ISO-3166-1 numéricos (coincidentes con world-atlas)
 */

require_once __DIR__ . '/../../config/monar_database.php';

$paises = [
    // Europa
    ['nombre' => 'Albania', 'continente' => 'Europa', 'codigo_iso' => '008'],
    ['nombre' => 'Alemania', 'continente' => 'Europa', 'codigo_iso' => '276'],
    ['nombre' => 'Andorra', 'continente' => 'Europa', 'codigo_iso' => '020'],
    ['nombre' => 'Austria', 'continente' => 'Europa', 'codigo_iso' => '040'],
    ['nombre' => 'Bélgica', 'continente' => 'Europa', 'codigo_iso' => '056'],
    ['nombre' => 'Bielorrusia', 'continente' => 'Europa', 'codigo_iso' => '112'],
    ['nombre' => 'Bosnia y Herzegovina', 'continente' => 'Europa', 'codigo_iso' => '070'],
    ['nombre' => 'Bulgaria', 'continente' => 'Europa', 'codigo_iso' => '100'],
    ['nombre' => 'Croacia', 'continente' => 'Europa', 'codigo_iso' => '191'],
    ['nombre' => 'Dinamarca', 'continente' => 'Europa', 'codigo_iso' => '208'],
    ['nombre' => 'Eslovaquia', 'continente' => 'Europa', 'codigo_iso' => '703'],
    ['nombre' => 'Eslovenia', 'continente' => 'Europa', 'codigo_iso' => '705'],
    ['nombre' => 'España', 'continente' => 'Europa', 'codigo_iso' => '724'],
    ['nombre' => 'Estonia', 'continente' => 'Europa', 'codigo_iso' => '233'],
    ['nombre' => 'Finlandia', 'continente' => 'Europa', 'codigo_iso' => '246'],
    ['nombre' => 'Francia', 'continente' => 'Europa', 'codigo_iso' => '250'],
    ['nombre' => 'Grecia', 'continente' => 'Europa', 'codigo_iso' => '300'],
    ['nombre' => 'Hungría', 'continente' => 'Europa', 'codigo_iso' => '348'],
    ['nombre' => 'Irlanda', 'continente' => 'Europa', 'codigo_iso' => '372'],
    ['nombre' => 'Islandia', 'continente' => 'Europa', 'codigo_iso' => '352'],
    ['nombre' => 'Italia', 'continente' => 'Europa', 'codigo_iso' => '380'],
    ['nombre' => 'Letonia', 'continente' => 'Europa', 'codigo_iso' => '428'],
    ['nombre' => 'Lituania', 'continente' => 'Europa', 'codigo_iso' => '440'],
    ['nombre' => 'Luxemburgo', 'continente' => 'Europa', 'codigo_iso' => '442'],
    ['nombre' => 'Macedonia del Norte', 'continente' => 'Europa', 'codigo_iso' => '807'],
    ['nombre' => 'Malta', 'continente' => 'Europa', 'codigo_iso' => '470'],
    ['nombre' => 'Moldavia', 'continente' => 'Europa', 'codigo_iso' => '498'],
    ['nombre' => 'Mónaco', 'continente' => 'Europa', 'codigo_iso' => '492'],
    ['nombre' => 'Montenegro', 'continente' => 'Europa', 'codigo_iso' => '499'],
    ['nombre' => 'Noruega', 'continente' => 'Europa', 'codigo_iso' => '578'],
    ['nombre' => 'Países Bajos', 'continente' => 'Europa', 'codigo_iso' => '528'],
    ['nombre' => 'Polonia', 'continente' => 'Europa', 'codigo_iso' => '616'],
    ['nombre' => 'Portugal', 'continente' => 'Europa', 'codigo_iso' => '620'],
    ['nombre' => 'Reino Unido', 'continente' => 'Europa', 'codigo_iso' => '826'],
    ['nombre' => 'República Checa', 'continente' => 'Europa', 'codigo_iso' => '203'],
    ['nombre' => 'Rumania', 'continente' => 'Europa', 'codigo_iso' => '642'],
    ['nombre' => 'Rusia', 'continente' => 'Europa', 'codigo_iso' => '643'],
    ['nombre' => 'San Marino', 'continente' => 'Europa', 'codigo_iso' => '674'],
    ['nombre' => 'Serbia', 'continente' => 'Europa', 'codigo_iso' => '688'],
    ['nombre' => 'Suecia', 'continente' => 'Europa', 'codigo_iso' => '752'],
    ['nombre' => 'Suiza', 'continente' => 'Europa', 'codigo_iso' => '756'],
    ['nombre' => 'Ucrania', 'continente' => 'Europa', 'codigo_iso' => '804'],
    ['nombre' => 'Vaticano', 'continente' => 'Europa', 'codigo_iso' => '336'],

    // Asia
    ['nombre' => 'Afganistán', 'continente' => 'Asia', 'codigo_iso' => '004'],
    ['nombre' => 'Arabia Saudita', 'continente' => 'Asia', 'codigo_iso' => '682'],
    ['nombre' => 'Armenia', 'continente' => 'Asia', 'codigo_iso' => '051'],
    ['nombre' => 'Azerbaiyán', 'continente' => 'Asia', 'codigo_iso' => '031'],
    ['nombre' => 'Bangladés', 'continente' => 'Asia', 'codigo_iso' => '050'],
    ['nombre' => 'Baréin', 'continente' => 'Asia', 'codigo_iso' => '048'],
    ['nombre' => 'Brunéi', 'continente' => 'Asia', 'codigo_iso' => '096'],
    ['nombre' => 'Bután', 'continente' => 'Asia', 'codigo_iso' => '064'],
    ['nombre' => 'Camboya', 'continente' => 'Asia', 'codigo_iso' => '116'],
    ['nombre' => 'Catar', 'continente' => 'Asia', 'codigo_iso' => '634'],
    ['nombre' => 'China', 'continente' => 'Asia', 'codigo_iso' => '156'],
    ['nombre' => 'Chipre', 'continente' => 'Asia', 'codigo_iso' => '196'],
    ['nombre' => 'Corea del Norte', 'continente' => 'Asia', 'codigo_iso' => '408'],
    ['nombre' => 'Corea del Sur', 'continente' => 'Asia', 'codigo_iso' => '410'],
    ['nombre' => 'Emiratos Árabes Unidos', 'continente' => 'Asia', 'codigo_iso' => '784'],
    ['nombre' => 'Filipinas', 'continente' => 'Asia', 'codigo_iso' => '608'],
    ['nombre' => 'Georgia', 'continente' => 'Asia', 'codigo_iso' => '268'],
    ['nombre' => 'India', 'continente' => 'Asia', 'codigo_iso' => '356'],
    ['nombre' => 'Indonesia', 'continente' => 'Asia', 'codigo_iso' => '360'],
    ['nombre' => 'Irak', 'continente' => 'Asia', 'codigo_iso' => '368'],
    ['nombre' => 'Irán', 'continente' => 'Asia', 'codigo_iso' => '364'],
    ['nombre' => 'Israel', 'continente' => 'Asia', 'codigo_iso' => '376'],
    ['nombre' => 'Japón', 'continente' => 'Asia', 'codigo_iso' => '392'],
    ['nombre' => 'Jordania', 'continente' => 'Asia', 'codigo_iso' => '400'],
    ['nombre' => 'Kazajistán', 'continente' => 'Asia', 'codigo_iso' => '398'],
    ['nombre' => 'Kirguistán', 'continente' => 'Asia', 'codigo_iso' => '417'],
    ['nombre' => 'Kuwait', 'continente' => 'Asia', 'codigo_iso' => '414'],
    ['nombre' => 'Laos', 'continente' => 'Asia', 'codigo_iso' => '418'],
    ['nombre' => 'Líbano', 'continente' => 'Asia', 'codigo_iso' => '422'],
    ['nombre' => 'Malasia', 'continente' => 'Asia', 'codigo_iso' => '458'],
    ['nombre' => 'Maldivas', 'continente' => 'Asia', 'codigo_iso' => '462'],
    ['nombre' => 'Mongolia', 'continente' => 'Asia', 'codigo_iso' => '496'],
    ['nombre' => 'Myanmar', 'continente' => 'Asia', 'codigo_iso' => '104'],
    ['nombre' => 'Nepal', 'continente' => 'Asia', 'codigo_iso' => '524'],
    ['nombre' => 'Omán', 'continente' => 'Asia', 'codigo_iso' => '512'],
    ['nombre' => 'Pakistán', 'continente' => 'Asia', 'codigo_iso' => '586'],
    ['nombre' => 'Palestina', 'continente' => 'Asia', 'codigo_iso' => '275'],
    ['nombre' => 'Singapur', 'continente' => 'Asia', 'codigo_iso' => '702'],
    ['nombre' => 'Siria', 'continente' => 'Asia', 'codigo_iso' => '760'],
    ['nombre' => 'Sri Lanka', 'continente' => 'Asia', 'codigo_iso' => '144'],
    ['nombre' => 'Tailandia', 'continente' => 'Asia', 'codigo_iso' => '764'],
    ['nombre' => 'Tayikistán', 'continente' => 'Asia', 'codigo_iso' => '762'],
    ['nombre' => 'Timor Oriental', 'continente' => 'Asia', 'codigo_iso' => '626'],
    ['nombre' => 'Turkmenistán', 'continente' => 'Asia', 'codigo_iso' => '795'],
    ['nombre' => 'Turquía', 'continente' => 'Asia', 'codigo_iso' => '792'],
    ['nombre' => 'Uzbekistán', 'continente' => 'Asia', 'codigo_iso' => '860'],
    ['nombre' => 'Vietnam', 'continente' => 'Asia', 'codigo_iso' => '704'],
    ['nombre' => 'Yemen', 'continente' => 'Asia', 'codigo_iso' => '887'],

    // África
    ['nombre' => 'Angola', 'continente' => 'África', 'codigo_iso' => '024'],
    ['nombre' => 'Argelia', 'continente' => 'África', 'codigo_iso' => '012'],
    ['nombre' => 'Benín', 'continente' => 'África', 'codigo_iso' => '204'],
    ['nombre' => 'Botsuana', 'continente' => 'África', 'codigo_iso' => '072'],
    ['nombre' => 'Burkina Faso', 'continente' => 'África', 'codigo_iso' => '854'],
    ['nombre' => 'Burundi', 'continente' => 'África', 'codigo_iso' => '108'],
    ['nombre' => 'Cabo Verde', 'continente' => 'África', 'codigo_iso' => '132'],
    ['nombre' => 'Camerún', 'continente' => 'África', 'codigo_iso' => '120'],
    ['nombre' => 'Chad', 'continente' => 'África', 'codigo_iso' => '148'],
    ['nombre' => 'Costa de Marfil', 'continente' => 'África', 'codigo_iso' => '384'],
    ['nombre' => 'Egipto', 'continente' => 'África', 'codigo_iso' => '818'],
    ['nombre' => 'Eritrea', 'continente' => 'África', 'codigo_iso' => '232'],
    ['nombre' => 'Etiopía', 'continente' => 'África', 'codigo_iso' => '231'],
    ['nombre' => 'Gabón', 'continente' => 'África', 'codigo_iso' => '266'],
    ['nombre' => 'Gambia', 'continente' => 'África', 'codigo_iso' => '270'],
    ['nombre' => 'Ghana', 'continente' => 'África', 'codigo_iso' => '288'],
    ['nombre' => 'Guinea', 'continente' => 'África', 'codigo_iso' => '324'],
    ['nombre' => 'Guinea-Bisáu', 'continente' => 'África', 'codigo_iso' => '624'],
    ['nombre' => 'Guinea Ecuatorial', 'continente' => 'África', 'codigo_iso' => '226'],
    ['nombre' => 'Kenia', 'continente' => 'África', 'codigo_iso' => '404'],
    ['nombre' => 'Lesoto', 'continente' => 'África', 'codigo_iso' => '426'],
    ['nombre' => 'Liberia', 'continente' => 'África', 'codigo_iso' => '430'],
    ['nombre' => 'Libia', 'continente' => 'África', 'codigo_iso' => '434'],
    ['nombre' => 'Madagascar', 'continente' => 'África', 'codigo_iso' => '450'],
    ['nombre' => 'Malaui', 'continente' => 'África', 'codigo_iso' => '454'],
    ['nombre' => 'Malí', 'continente' => 'África', 'codigo_iso' => '466'],
    ['nombre' => 'Marruecos', 'continente' => 'África', 'codigo_iso' => '504'],
    ['nombre' => 'Mauricio', 'continente' => 'África', 'codigo_iso' => '480'],
    ['nombre' => 'Mauritania', 'continente' => 'África', 'codigo_iso' => '478'],
    ['nombre' => 'Mozambique', 'continente' => 'África', 'codigo_iso' => '508'],
    ['nombre' => 'Namibia', 'continente' => 'África', 'codigo_iso' => '516'],
    ['nombre' => 'Níger', 'continente' => 'África', 'codigo_iso' => '562'],
    ['nombre' => 'Nigeria', 'continente' => 'África', 'codigo_iso' => '566'],
    ['nombre' => 'República Centroafricana', 'continente' => 'África', 'codigo_iso' => '140'],
    ['nombre' => 'República del Congo', 'continente' => 'África', 'codigo_iso' => '178'],
    ['nombre' => 'República Democrática del Congo', 'continente' => 'África', 'codigo_iso' => '180'],
    ['nombre' => 'Ruanda', 'continente' => 'África', 'codigo_iso' => '646'],
    ['nombre' => 'Senegal', 'continente' => 'África', 'codigo_iso' => '686'],
    ['nombre' => 'Sierra Leona', 'continente' => 'África', 'codigo_iso' => '694'],
    ['nombre' => 'Somalia', 'continente' => 'África', 'codigo_iso' => '706'],
    ['nombre' => 'Sudáfrica', 'continente' => 'África', 'codigo_iso' => '710'],
    ['nombre' => 'Sudán', 'continente' => 'África', 'codigo_iso' => '729'],
    ['nombre' => 'Sudán del Sur', 'continente' => 'África', 'codigo_iso' => '728'],
    ['nombre' => 'Suazilandia', 'continente' => 'África', 'codigo_iso' => '748'],
    ['nombre' => 'Tanzania', 'continente' => 'África', 'codigo_iso' => '834'],
    ['nombre' => 'Togo', 'continente' => 'África', 'codigo_iso' => '768'],
    ['nombre' => 'Túnez', 'continente' => 'África', 'codigo_iso' => '788'],
    ['nombre' => 'Uganda', 'continente' => 'África', 'codigo_iso' => '800'],
    ['nombre' => 'Yibuti', 'continente' => 'África', 'codigo_iso' => '262'],
    ['nombre' => 'Zambia', 'continente' => 'África', 'codigo_iso' => '894'],
    ['nombre' => 'Zimbabue', 'continente' => 'África', 'codigo_iso' => '716'],

    // América del Norte y Central
    ['nombre' => 'Antigua y Barbuda', 'continente' => 'América del Norte', 'codigo_iso' => '028'],
    ['nombre' => 'Bahamas', 'continente' => 'América del Norte', 'codigo_iso' => '044'],
    ['nombre' => 'Barbados', 'continente' => 'América del Norte', 'codigo_iso' => '052'],
    ['nombre' => 'Belice', 'continente' => 'América del Norte', 'codigo_iso' => '084'],
    ['nombre' => 'Canadá', 'continente' => 'América del Norte', 'codigo_iso' => '124'],
    ['nombre' => 'Costa Rica', 'continente' => 'América del Norte', 'codigo_iso' => '188'],
    ['nombre' => 'Cuba', 'continente' => 'América del Norte', 'codigo_iso' => '192'],
    ['nombre' => 'Dominica', 'continente' => 'América del Norte', 'codigo_iso' => '212'],
    ['nombre' => 'El Salvador', 'continente' => 'América del Norte', 'codigo_iso' => '222'],
    ['nombre' => 'Estados Unidos', 'continente' => 'América del Norte', 'codigo_iso' => '840'],
    ['nombre' => 'Granada', 'continente' => 'América del Norte', 'codigo_iso' => '308'],
    ['nombre' => 'Guatemala', 'continente' => 'América del Norte', 'codigo_iso' => '320'],
    ['nombre' => 'Haití', 'continente' => 'América del Norte', 'codigo_iso' => '332'],
    ['nombre' => 'Honduras', 'continente' => 'América del Norte', 'codigo_iso' => '340'],
    ['nombre' => 'Jamaica', 'continente' => 'América del Norte', 'codigo_iso' => '388'],
    ['nombre' => 'México', 'continente' => 'América del Norte', 'codigo_iso' => '484'],
    ['nombre' => 'Nicaragua', 'continente' => 'América del Norte', 'codigo_iso' => '558'],
    ['nombre' => 'Panamá', 'continente' => 'América del Norte', 'codigo_iso' => '591'],
    ['nombre' => 'República Dominicana', 'continente' => 'América del Norte', 'codigo_iso' => '214'],
    ['nombre' => 'San Cristóbal y Nieves', 'continente' => 'América del Norte', 'codigo_iso' => '659'],
    ['nombre' => 'San Vicente y las Granadinas', 'continente' => 'América del Norte', 'codigo_iso' => '670'],
    ['nombre' => 'Santa Lucía', 'continente' => 'América del Norte', 'codigo_iso' => '662'],
    ['nombre' => 'Trinidad y Tobago', 'continente' => 'América del Norte', 'codigo_iso' => '780'],

    // América del Sur
    ['nombre' => 'Argentina', 'continente' => 'América del Sur', 'codigo_iso' => '032'],
    ['nombre' => 'Bolivia', 'continente' => 'América del Sur', 'codigo_iso' => '068'],
    ['nombre' => 'Brasil', 'continente' => 'América del Sur', 'codigo_iso' => '076'],
    ['nombre' => 'Chile', 'continente' => 'América del Sur', 'codigo_iso' => '152'],
    ['nombre' => 'Colombia', 'continente' => 'América del Sur', 'codigo_iso' => '170'],
    ['nombre' => 'Ecuador', 'continente' => 'América del Sur', 'codigo_iso' => '218'],
    ['nombre' => 'Guyana', 'continente' => 'América del Sur', 'codigo_iso' => '328'],
    ['nombre' => 'Paraguay', 'continente' => 'América del Sur', 'codigo_iso' => '600'],
    ['nombre' => 'Perú', 'continente' => 'América del Sur', 'codigo_iso' => '604'],
    ['nombre' => 'Surinam', 'continente' => 'América del Sur', 'codigo_iso' => '740'],
    ['nombre' => 'Uruguay', 'continente' => 'América del Sur', 'codigo_iso' => '858'],
    ['nombre' => 'Venezuela', 'continente' => 'América del Sur', 'codigo_iso' => '862'],

    // Oceanía
    ['nombre' => 'Australia', 'continente' => 'Oceanía', 'codigo_iso' => '036'],
    ['nombre' => 'Fiyi', 'continente' => 'Oceanía', 'codigo_iso' => '242'],
    ['nombre' => 'Islas Marshall', 'continente' => 'Oceanía', 'codigo_iso' => '584'],
    ['nombre' => 'Islas Salomón', 'continente' => 'Oceanía', 'codigo_iso' => '090'],
    ['nombre' => 'Kiribati', 'continente' => 'Oceanía', 'codigo_iso' => '296'],
    ['nombre' => 'Micronesia', 'continente' => 'Oceanía', 'codigo_iso' => '583'],
    ['nombre' => 'Nauru', 'continente' => 'Oceanía', 'codigo_iso' => '520'],
    ['nombre' => 'Nueva Zelanda', 'continente' => 'Oceanía', 'codigo_iso' => '554'],
    ['nombre' => 'Palaos', 'continente' => 'Oceanía', 'codigo_iso' => '585'],
    ['nombre' => 'Papúa Nueva Guinea', 'continente' => 'Oceanía', 'codigo_iso' => '598'],
    ['nombre' => 'Samoa', 'continente' => 'Oceanía', 'codigo_iso' => '882'],
    ['nombre' => 'Tonga', 'continente' => 'Oceanía', 'codigo_iso' => '776'],
    ['nombre' => 'Tuvalu', 'continente' => 'Oceanía', 'codigo_iso' => '798'],
    ['nombre' => 'Vanuatu', 'continente' => 'Oceanía', 'codigo_iso' => '548'],
];

try {
    $pdo = getDBConnection();
    
    echo "═══════════════════════════════════════\n";
    echo "  Insertando TODOS los países del mundo\n";
    echo "═══════════════════════════════════════\n\n";
    
    $insertados = 0;
    $omitidos = 0;
    
    foreach ($paises as $pais) {
        // Verificar si el país ya existe
        $stmt = $pdo->prepare('SELECT id FROM pais WHERE codigo_iso = :iso');
        $stmt->execute(['iso' => $pais['codigo_iso']]);
        
        if ($stmt->fetch()) {
            echo "⏭️  {$pais['nombre']} ya existe\n";
            $omitidos++;
            continue;
        }
        
        // Insertar el país
        $stmt = $pdo->prepare('INSERT INTO pais (nombre, continente, codigo_iso) VALUES (:nombre, :continente, :codigo_iso)');
        $stmt->execute([
            'nombre' => $pais['nombre'],
            'continente' => $pais['continente'],
            'codigo_iso' => $pais['codigo_iso']
        ]);
        
        echo "✅ {$pais['nombre']} insertado\n";
        $insertados++;
    }
    
    echo "\n";
    echo "═══════════════════════════════════════\n";
    echo "Resumen:\n";
    echo "  ✅ Países insertados: $insertados\n";
    echo "  ⏭️  Países omitidos: $omitidos\n";
    echo "  📊 Total procesados: " . count($paises) . "\n";
    echo "═══════════════════════════════════════\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
