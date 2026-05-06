<?php
/**
 * api/chatbot.php
 * Endpoint for the travel chatbot using Ollama (local LLM) and RestCountries API.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 0);

/**
 * Consult Ollama for a country suggestion based on user input.
 */
function consultarOllama($promptUsuario) {
    $url = 'http://127.0.0.1:11434/api/chat';
    
    $sistema = "Eres un experto en viajes y guía turístico virtual para la plataforma MONAR. 
    Tu objetivo es sugerir un país basado en lo que el usuario pida. 
    Responde ÚNICAMENTE con un objeto JSON que tenga la llave 'pais' con el nombre del país en inglés y 'mensaje' con una breve descripción sugerente en español (máximo 100 caracteres).
    Ejemplo: {\"pais\": \"Japan\", \"mensaje\": \"¡Excelente elección! Japón te espera con sus cerezos en flor y tecnología punta.\"}";

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
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) return null;

    $json = json_decode($response, true);
    if (isset($json['message']['content'])) {
        $content = $json['message']['content'];
        // Ensure we handle cases where LLM might add extra text
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            return json_decode($matches[0], true);
        }
    }
    return null;
}

/**
 * Fetch detailed country data from RestCountries API.
 */
function buscarDatosPais($nombre) {
    $url = "https://restcountries.com/v3.1/name/" . rawurlencode($nombre) . "?fields=name,capital,flags,region,population,translations,latlng,cca2";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $res = json_decode($response, true);
    return (isset($res[0])) ? $res[0] : null;
}

// Handle Input
$input = json_decode(file_get_contents('php://input'), true);
$mensaje = $input['mensaje'] ?? '';

if (!$mensaje) {
    echo json_encode(['status' => 'error', 'text' => '¿En qué puedo ayudarte hoy?']);
    exit;
}

try {
    $aiResponse = consultarOllama($mensaje);

    if ($aiResponse && isset($aiResponse['pais'])) {
        $paisSugerido = $aiResponse['pais'];
        $mensajeIA = $aiResponse['mensaje'] ?? "He encontrado este destino para ti.";
        
        $datos = buscarDatosPais($paisSugerido);
        if ($datos) {
            $nombreEs = $datos['translations']['spa']['common'] ?? $paisSugerido;
            $datos['name']['common_es'] = $nombreEs;

            echo json_encode([
                'status' => 'success',
                'text' => $mensajeIA,
                'pais' => $nombreEs,
                'datos' => $datos
            ]);
        } else {
            echo json_encode([
                'status' => 'partial', 
                'text' => "Te sugiero visitar **$paisSugerido**, aunque no pude obtener detalles adicionales en este momento.",
                'pais' => $paisSugerido
            ]);
        }
    } else {
        // Fallback for general conversation if LLM didn't return a specific country JSON
        // Or if we want to support general chat, we could modify the prompt.
        // For now, let's keep it travel-focused.
        echo json_encode(['status' => 'error', 'text' => "No pude identificar un país en tu mensaje. ¡Prueba preguntándome por un destino!"]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'text' => "Hubo un problema al conectar con el cerebro de la IA. Revisa si Ollama está activo."]);
}
