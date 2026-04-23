<?php
/**
 * Script de ayuda para insertar países en la base de datos
 * 
 * Este script proporciona una lista de países con sus códigos ISO-3166-1 numéricos
 * que coinciden con el dataset world-atlas usado en el mapa.
 * 
 * Para usar: Ejecuta este script desde la terminal con:
 * php insert_countries.php
 */

require_once __DIR__ . '/../../config/monar_database.php';

$paises = [
    // Europa
    ['nombre' => 'España', 'continente' => 'Europa', 'codigo_iso' => '724'],
    ['nombre' => 'Francia', 'continente' => 'Europa', 'codigo_iso' => '250'],
    ['nombre' => 'Italia', 'continente' => 'Europa', 'codigo_iso' => '380'],
    ['nombre' => 'Alemania', 'continente' => 'Europa', 'codigo_iso' => '276'],
    ['nombre' => 'Reino Unido', 'continente' => 'Europa', 'codigo_iso' => '826'],
    ['nombre' => 'Portugal', 'continente' => 'Europa', 'codigo_iso' => '620'],
    ['nombre' => 'Países Bajos', 'continente' => 'Europa', 'codigo_iso' => '528'],
    ['nombre' => 'Bélgica', 'continente' => 'Europa', 'codigo_iso' => '056'],
    ['nombre' => 'Suiza', 'continente' => 'Europa', 'codigo_iso' => '756'],
    ['nombre' => 'Austria', 'continente' => 'Europa', 'codigo_iso' => '040'],
    ['nombre' => 'Grecia', 'continente' => 'Europa', 'codigo_iso' => '300'],
    ['nombre' => 'Suecia', 'continente' => 'Europa', 'codigo_iso' => '752'],
    ['nombre' => 'Noruega', 'continente' => 'Europa', 'codigo_iso' => '578'],
    ['nombre' => 'Dinamarca', 'continente' => 'Europa', 'codigo_iso' => '208'],
    ['nombre' => 'Polonia', 'continente' => 'Europa', 'codigo_iso' => '616'],
    
    // Asia
    ['nombre' => 'Japón', 'continente' => 'Asia', 'codigo_iso' => '392'],
    ['nombre' => 'China', 'continente' => 'Asia', 'codigo_iso' => '156'],
    ['nombre' => 'India', 'continente' => 'Asia', 'codigo_iso' => '356'],
    ['nombre' => 'Corea del Sur', 'continente' => 'Asia', 'codigo_iso' => '410'],
    ['nombre' => 'Tailandia', 'continente' => 'Asia', 'codigo_iso' => '764'],
    ['nombre' => 'Vietnam', 'continente' => 'Asia', 'codigo_iso' => '704'],
    ['nombre' => 'Indonesia', 'continente' => 'Asia', 'codigo_iso' => '360'],
    ['nombre' => 'Malasia', 'continente' => 'Asia', 'codigo_iso' => '458'],
    ['nombre' => 'Singapur', 'continente' => 'Asia', 'codigo_iso' => '702'],
    ['nombre' => 'Turquía', 'continente' => 'Asia', 'codigo_iso' => '792'],
    ['nombre' => 'Emiratos Árabes Unidos', 'continente' => 'Asia', 'codigo_iso' => '784'],
    
    // América del Norte
    ['nombre' => 'Estados Unidos', 'continente' => 'América del Norte', 'codigo_iso' => '840'],
    ['nombre' => 'Canadá', 'continente' => 'América del Norte', 'codigo_iso' => '124'],
    ['nombre' => 'México', 'continente' => 'América del Norte', 'codigo_iso' => '484'],
    ['nombre' => 'Cuba', 'continente' => 'América del Norte', 'codigo_iso' => '192'],
    ['nombre' => 'República Dominicana', 'continente' => 'América del Norte', 'codigo_iso' => '214'],
    
    // América del Sur
    ['nombre' => 'Brasil', 'continente' => 'América del Sur', 'codigo_iso' => '076'],
    ['nombre' => 'Argentina', 'continente' => 'América del Sur', 'codigo_iso' => '032'],
    ['nombre' => 'Chile', 'continente' => 'América del Sur', 'codigo_iso' => '152'],
    ['nombre' => 'Perú', 'continente' => 'América del Sur', 'codigo_iso' => '604'],
    ['nombre' => 'Colombia', 'continente' => 'América del Sur', 'codigo_iso' => '170'],
    ['nombre' => 'Ecuador', 'continente' => 'América del Sur', 'codigo_iso' => '218'],
    ['nombre' => 'Venezuela', 'continente' => 'América del Sur', 'codigo_iso' => '862'],
    ['nombre' => 'Uruguay', 'continente' => 'América del Sur', 'codigo_iso' => '858'],
    
    // Oceanía
    ['nombre' => 'Australia', 'continente' => 'Oceanía', 'codigo_iso' => '036'],
    ['nombre' => 'Nueva Zelanda', 'continente' => 'Oceanía', 'codigo_iso' => '554'],
    
    // África
    ['nombre' => 'Egipto', 'continente' => 'África', 'codigo_iso' => '818'],
    ['nombre' => 'Sudáfrica', 'continente' => 'África', 'codigo_iso' => '710'],
    ['nombre' => 'Marruecos', 'continente' => 'África', 'codigo_iso' => '504'],
    ['nombre' => 'Túnez', 'continente' => 'África', 'codigo_iso' => '788'],
    ['nombre' => 'Kenia', 'continente' => 'África', 'codigo_iso' => '404'],
    ['nombre' => 'Tanzania', 'continente' => 'África', 'codigo_iso' => '834'],
    ['nombre' => 'Nigeria', 'continente' => 'África', 'codigo_iso' => '566'],
];

try {
    $pdo = getDBConnection();
    
    echo "Insertando países en la base de datos...\n\n";
    
    $insertados = 0;
    $omitidos = 0;
    
    foreach ($paises as $pais) {
        // Verificar si el país ya existe
        $stmt = $pdo->prepare('SELECT id FROM pais WHERE codigo_iso = :iso');
        $stmt->execute(['iso' => $pais['codigo_iso']]);
        
        if ($stmt->fetch()) {
            echo "⏭️  {$pais['nombre']} ({$pais['codigo_iso']}) ya existe\n";
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
        
        echo "✅ {$pais['nombre']} ({$pais['codigo_iso']}) insertado\n";
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
