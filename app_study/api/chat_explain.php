<?php
require_once "../config/database.php";

header("Content-Type: application/json");

function keywordSet($text) {
    $text = mb_strtolower($text);
    $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
    $parts = preg_split('/\s+/', $text);
    $out = [];
    foreach ($parts as $part) {
        if (mb_strlen($part) >= 3) {
            $out[$part] = true;
        }
    }
    return array_keys($out);
}

function overlapScore($a, $b) {
    $setA = array_flip($a);
    $score = 0;
    foreach ($b as $word) {
        if (isset($setA[$word])) {
            $score++;
        }
    }
    return $score;
}

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->message) || empty($data->deck_id)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing message or deck_id"]);
    exit;
}

$message = trim($data->message);
$deckId = (int)$data->deck_id;

$query = "SELECT question, answer FROM cards WHERE deck_id = :deck_id LIMIT 200";
$stmt = $conn->prepare($query);
$stmt->bindParam(':deck_id', $deckId, PDO::PARAM_INT);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$cards || count($cards) === 0) {
    echo json_encode(["status" => "success", "reply" => "This deck has no cards to explain yet."]);
    exit;
}

$msgKeys = keywordSet($message);
$best = null;
$bestScore = -1;

foreach ($cards as $card) {
    $keys = keywordSet($card['question'] . ' ' . $card['answer']);
    $score = overlapScore($msgKeys, $keys);
    if ($score > $bestScore) {
        $bestScore = $score;
        $best = $card;
    }
}

if ($bestScore <= 0) {
    $best = $cards[array_rand($cards)];
}

$reply = "Based on your current deck:\n";
$reply .= "- Closest topic: " . $best['question'] . "\n";
$reply .= "- Short explanation: " . $best['answer'] . "\n";
$reply .= "- Study tip: repeat this idea in your own words after 30 seconds.";

echo json_encode(["status" => "success", "reply" => $reply, "matched_question" => $best['question']]);
