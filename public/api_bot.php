<?php
// api_bot.php
header('Content-Type: application/json');
ini_set('display_errors', 0); 

function consultarOllama($promptUsuario) {
    $url = 'http://127.0.0.1:11434/api/chat';
    
    // Prompt directo y sin complicaciones
    $sistema = "Eres un experto en viajes. Responde solo con un JSON que tenga la llave 'pais' y el nombre en inglés.";

    $data = [
        "model" => "llama3",
        "messages" => [
            ["role" => "system", "content" => $sistema],
            ["role" => "user", "content" => $promptUsuario]
        ],
        "format" => "json",
        "stream" => false
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Damos más tiempo por si la gráfica está "despertando"
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) return null;

    $json = json_decode($response, true);
    if (isset($json['message']['content'])) {
        $innerJson = json_decode($json['message']['content'], true);
        return $innerJson['pais'] ?? null;
    }
    return null;
}

function buscarDatosPais($nombre) {
    // Endpoint infalible de búsqueda por nombre
    $url = "https://restcountries.com/v3.1/name/" . rawurlencode($nombre) . "?fields=name,capital,flags,region,population,translations";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $res = json_decode($response, true);
    return (isset($res[0])) ? $res[0] : null;
}

$input = json_decode(file_get_contents('php://input'), true);
$mensaje = $input['mensaje'] ?? '';

if (!$mensaje) {
    echo json_encode(['status' => 'error', 'text' => 'Escribe algo primero.']);
    exit;
}

$paisSugerido = consultarOllama($mensaje);

if ($paisSugerido) {
    $datos = buscarDatosPais($paisSugerido);
    if ($datos) {
        // Traducimos al español usando la API
        $nombreEs = $datos['translations']['spa']['common'] ?? $paisSugerido;
        $datos['name']['common'] = $nombreEs;

        echo json_encode([
            'status' => 'success',
            'pais' => $nombreEs,
            'datos' => $datos
        ]);
    } else {
        echo json_encode(['status' => 'error', 'text' => "La IA dijo '$paisSugerido', pero la API de banderas no lo encontró."]);
    }
} else {
    echo json_encode(['status' => 'error', 'text' => "No recibí respuesta de la IA. Revisa que Ollama esté abierto."]);
}