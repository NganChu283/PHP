<?php
require_once "../config/database.php";

header("Content-Type: application/json");

function failJson($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(["status" => "error", "message" => $message]);
    exit;
}

function bytesFromIniSize($val) {
    $val = trim($val);
    if ($val === '') {
        return 0;
    }
    $last = strtolower($val[strlen($val) - 1]);
    $num = (int)$val;
    if ($last === 'g') {
        return $num * 1024 * 1024 * 1024;
    }
    if ($last === 'm') {
        return $num * 1024 * 1024;
    }
    if ($last === 'k') {
        return $num * 1024;
    }
    return $num;
}

function safeSubstr($text, $start, $length) {
    if (function_exists('mb_substr')) {
        return mb_substr($text, $start, $length);
    }
    return substr($text, $start, $length);
}

function tableHasColumn($conn, $table, $column) {
    $query = "SELECT COUNT(*) AS c
              FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND COLUMN_NAME = :column_name";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':table_name', $table);
    $stmt->bindParam(':column_name', $column);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row && (int)$row['c'] > 0;
}

function sanitizeLine($line) {
    $line = trim($line);
    $line = preg_replace('/\s+/', ' ', $line);
    return $line;
}

function startsWithAny($text, $prefixes) {
    $t = strtolower(trim($text));
    foreach ($prefixes as $prefix) {
        if (strpos($t, $prefix) === 0) {
            return true;
        }
    }
    return false;
}

function stripQaPrefix($text) {
    return trim((string)preg_replace('/^(q|q\.|question|cau hoi|a|a\.|answer|tra loi|dap an|giai thich)\s*[:\-\)]\s*/i', '', trim($text)));
}

function looksLikeQuestion($line) {
    $l = trim($line);
    if ($l === '') {
        return false;
    }

    if (substr($l, -1) === '?') {
        return true;
    }

    $questionStarters = [
        'what ', 'why ', 'how ', 'when ', 'where ', 'which ', 'who ',
        'define ', 'explain ', 'list ', 'is ', 'are ', 'can ', 'does ', 'do ',
        'cau hoi', 'neu', 'trinh bay', 'tai sao', 'nhu the nao', 'la gi', 'vi sao',
        'hay cho biet', 'phan tich', 'so sanh', 'cau ', 'câu ', 'chon ', 'chọn '
    ];

    if (startsWithAny($l, $questionStarters)) {
        return true;
    }

    if (preg_match('/^(c[aâ]u|question)\s*\d+\s*[\.:\)]/iu', $l) === 1) {
        return true;
    }

    if (preg_match('/^\d+\s*[\.)\-:]\s+.{6,}$/u', $l) === 1) {
        return true;
    }

    return preg_match('/^\d+[\.)]\s+.+\?$/', $l) === 1;
}

function isQuestionHeader($line) {
    $l = trim($line);
    return looksLikeQuestion($l)
        || preg_match('/^(c[aâ]u|question)\s*\d+\s*[\.:\)]/iu', $l) === 1
        || preg_match('/^\d+\s*[\.)\-:]\s+.{3,}$/u', $l) === 1;
}

function buildPairsWithChatbotParser($text, $maxPairs = 20) {
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $clean = [];
    foreach ($lines as $line) {
        $line = sanitizeLine($line);
        if (strlen($line) >= 4) {
            $clean[] = $line;
        }
    }

    $pairs = [];
    $pendingQuestion = null;

    for ($i = 0; $i < count($clean) && count($pairs) < $maxPairs; $i++) {
        $line = $clean[$i];
        $isQTag = preg_match('/^(q|q\.|question|cau hoi)\s*[:\-\)]/i', $line) === 1;
        $isATag = preg_match('/^(a|a\.|answer|tra loi)\s*[:\-\)]/i', $line) === 1;

        if ($isQTag) {
            $pendingQuestion = stripQaPrefix($line);
            continue;
        }

        if ($isATag && $pendingQuestion) {
            $answer = stripQaPrefix($line);
            if ($answer !== '' && $pendingQuestion !== '') {
                $pairs[] = [
                    'question' => safeSubstr($pendingQuestion, 0, 220),
                    'answer' => safeSubstr($answer, 0, 260)
                ];
            }
            $pendingQuestion = null;
            continue;
        }

        if ($pendingQuestion && !looksLikeQuestion($line)) {
            $pairs[] = [
                'question' => safeSubstr($pendingQuestion, 0, 220),
                'answer' => safeSubstr($line, 0, 260)
            ];
            $pendingQuestion = null;
            continue;
        }

        if (looksLikeQuestion($line)) {
            $next = isset($clean[$i + 1]) ? $clean[$i + 1] : '';
            if ($next !== '' && !looksLikeQuestion($next)) {
                $pairs[] = [
                    'question' => safeSubstr(stripQaPrefix($line), 0, 220),
                    'answer' => safeSubstr(stripQaPrefix($next), 0, 260)
                ];
                $i++;
            } else {
                $pendingQuestion = stripQaPrefix($line);
            }
        }
    }

    return $pairs;
}

function buildPairsFromQuestionBlocks($text, $maxPairs = 20) {
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $clean = [];
    foreach ($lines as $line) {
        $line = sanitizeLine($line);
        if (strlen($line) >= 2) {
            $clean[] = $line;
        }
    }

    $pairs = [];
    $currentQuestion = null;
    $answerLines = [];

    $flushPair = function () use (&$pairs, &$currentQuestion, &$answerLines, $maxPairs) {
        if ($currentQuestion === null || count($pairs) >= $maxPairs) {
            return;
        }
        $answer = trim(implode(' ', $answerLines));
        if ($answer === '') {
            $answer = 'See source context for details.';
        }
        $pairs[] = [
            'question' => safeSubstr(stripQaPrefix($currentQuestion), 0, 220),
            'answer' => safeSubstr(stripQaPrefix($answer), 0, 260)
        ];
        $currentQuestion = null;
        $answerLines = [];
    };

    foreach ($clean as $line) {
        if (isQuestionHeader($line)) {
            $flushPair();
            $currentQuestion = $line;
            $answerLines = [];
            continue;
        }

        if ($currentQuestion !== null) {
            $answerLines[] = $line;
        }
    }

    $flushPair();
    return $pairs;
}

function commandCandidates($commandName) {
    $candidates = [$commandName];

    $localAppData = getenv('LOCALAPPDATA');
    if ($localAppData) {
        $wingetRoot = $localAppData . DIRECTORY_SEPARATOR . 'Microsoft' . DIRECTORY_SEPARATOR . 'WinGet' . DIRECTORY_SEPARATOR . 'Packages';
        if (is_dir($wingetRoot)) {
            if ($commandName === 'pdftotext' || $commandName === 'pdftoppm' || $commandName === 'pdftohtml') {
                $matches = glob($wingetRoot . DIRECTORY_SEPARATOR . 'oschwartz10612.Poppler_*' . DIRECTORY_SEPARATOR . 'poppler-*' . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . $commandName . '.exe');
                if ($matches && count($matches) > 0) {
                    $candidates[] = $matches[0];
                }
            }

            if ($commandName === 'tesseract') {
                $matches = glob($wingetRoot . DIRECTORY_SEPARATOR . '*Tesseract*' . DIRECTORY_SEPARATOR . '**' . DIRECTORY_SEPARATOR . 'tesseract.exe', GLOB_BRACE);
                if ($matches && count($matches) > 0) {
                    $candidates[] = $matches[0];
                }
            }
        }
    }

    $programFiles = getenv('ProgramFiles');
    if ($programFiles) {
        if ($commandName === 'pdftotext' || $commandName === 'pdftoppm' || $commandName === 'pdftohtml') {
            $candidates[] = $programFiles . DIRECTORY_SEPARATOR . 'poppler' . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . $commandName . '.exe';
        }
        if ($commandName === 'tesseract') {
            $candidates[] = $programFiles . DIRECTORY_SEPARATOR . 'Tesseract-OCR' . DIRECTORY_SEPARATOR . 'tesseract.exe';
        }
    }

    $programFilesX86 = getenv('ProgramFiles(x86)');
    if ($programFilesX86) {
        if ($commandName === 'pdftotext' || $commandName === 'pdftoppm' || $commandName === 'pdftohtml') {
            $candidates[] = $programFilesX86 . DIRECTORY_SEPARATOR . 'poppler' . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . $commandName . '.exe';
        }
        if ($commandName === 'tesseract') {
            $candidates[] = $programFilesX86 . DIRECTORY_SEPARATOR . 'Tesseract-OCR' . DIRECTORY_SEPARATOR . 'tesseract.exe';
        }
    }

    return array_values(array_unique(array_filter($candidates)));
}

function extractTextWithPdftotext($pdfPath) {
    foreach (commandCandidates('pdftotext') as $bin) {
        $binCmd = strpos($bin, ' ') !== false ? '"' . $bin . '"' : $bin;
        $cmd = $binCmd . ' -layout ' . escapeshellarg($pdfPath) . ' -';
        $output = @shell_exec($cmd);
        if ($output && trim($output) !== '') {
            return $output;
        }
    }

    return null;
}

function isRedColor($hexColor) {
    $c = strtolower(trim((string)$hexColor));
    if ($c === '') {
        return false;
    }
    if ($c[0] !== '#') {
        return false;
    }
    if (strlen($c) === 4) {
        $r = hexdec(str_repeat($c[1], 2));
        $g = hexdec(str_repeat($c[2], 2));
        $b = hexdec(str_repeat($c[3], 2));
    } elseif (strlen($c) === 7) {
        $r = hexdec(substr($c, 1, 2));
        $g = hexdec(substr($c, 3, 2));
        $b = hexdec(substr($c, 5, 2));
    } else {
        return false;
    }

    return ($r >= 170) && ($g <= 110) && ($b <= 110);
}

function extractColoredLinesFromPdf($pdfPath) {
    $bins = commandCandidates('pdftohtml');
    if (count($bins) === 0) {
        return [];
    }

    $tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'pdfxml_' . uniqid();
    if (!@mkdir($tmpDir, 0777, true) && !is_dir($tmpDir)) {
        return [];
    }

    $xmlFile = $tmpDir . DIRECTORY_SEPARATOR . 'doc.xml';
    $gotXml = false;

    foreach ($bins as $bin) {
        $binCmd = strpos($bin, ' ') !== false ? '"' . $bin . '"' : $bin;
        $cmd = $binCmd . ' -xml -i ' . escapeshellarg($pdfPath) . ' ' . escapeshellarg($tmpDir . DIRECTORY_SEPARATOR . 'doc');
        @shell_exec($cmd);
        if (is_file($xmlFile)) {
            $gotXml = true;
            break;
        }
    }

    if (!$gotXml) {
        removeTree($tmpDir);
        return [];
    }

    $xml = @simplexml_load_file($xmlFile);
    if (!$xml) {
        removeTree($tmpDir);
        return [];
    }

    $fontColor = [];
    foreach ($xml->fontspec as $fontspec) {
        $id = (string)$fontspec['id'];
        $color = (string)$fontspec['color'];
        if ($id !== '') {
            $fontColor[$id] = $color;
        }
    }

    $lines = [];
    foreach ($xml->page as $page) {
        foreach ($page->text as $textNode) {
            $raw = sanitizeLine((string)$textNode);
            if ($raw === '') {
                continue;
            }
            $fontId = (string)$textNode['font'];
            $color = isset($fontColor[$fontId]) ? $fontColor[$fontId] : '';
            $lines[] = [
                'text' => $raw,
                'color' => $color,
                'is_red' => isRedColor($color)
            ];
        }
    }

    removeTree($tmpDir);
    return $lines;
}

function buildPairsFromColoredLines($coloredLines, $maxPairs = 20) {
    $pairs = [];
    $pendingQuestion = null;
    $pendingAnswerLines = [];
    $pendingRed = null;

    $flushPair = function () use (&$pairs, &$pendingQuestion, &$pendingAnswerLines, &$pendingRed, $maxPairs) {
        if ($pendingQuestion === null || count($pairs) >= $maxPairs) {
            return;
        }

        $answer = $pendingRed;
        if (!$answer) {
            $joined = trim(implode(' ', $pendingAnswerLines));
            $answer = $joined !== '' ? $joined : 'See source context for details.';
        }

        $pairs[] = [
            'question' => safeSubstr($pendingQuestion, 0, 220),
            'answer' => safeSubstr($answer, 0, 260)
        ];

        $pendingQuestion = null;
        $pendingAnswerLines = [];
        $pendingRed = null;
    };

    foreach ($coloredLines as $line) {
        if (count($pairs) >= $maxPairs) {
            break;
        }
        $text = stripQaPrefix($line['text']);
        if ($text === '') {
            continue;
        }

        if (isQuestionHeader($text)) {
            $flushPair();
            $pendingQuestion = $text;
            continue;
        }

        if ($line['is_red']) {
            if ($pendingQuestion) {
                $pendingRed = $text;
            }
            continue;
        }

        if ($pendingQuestion) {
            $pendingAnswerLines[] = $text;
        }
    }

    $flushPair();
    return $pairs;
}

function removeTree($path) {
    if (!is_dir($path)) {
        return;
    }
    $files = glob($path . DIRECTORY_SEPARATOR . '*');
    if ($files) {
        foreach ($files as $file) {
            if (is_dir($file)) {
                removeTree($file);
            } else {
                @unlink($file);
            }
        }
    }
    @rmdir($path);
}

function extractTextWithOcr($pdfPath) {
    $pdftoppmBins = commandCandidates('pdftoppm');
    $tesseractBins = commandCandidates('tesseract');
    if (count($pdftoppmBins) === 0 || count($tesseractBins) === 0) {
        return null;
    }

    $tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ocr_' . uniqid();
    if (!@mkdir($tmpDir, 0777, true) && !is_dir($tmpDir)) {
        return null;
    }

    $prefix = $tmpDir . DIRECTORY_SEPARATOR . 'page';
    $pagesGenerated = false;

    foreach ($pdftoppmBins as $pdftoppm) {
        $binCmd = strpos($pdftoppm, ' ') !== false ? '"' . $pdftoppm . '"' : $pdftoppm;
        // OCR first 8 pages for speed; can be tuned higher if needed.
        $cmd = $binCmd . ' -f 1 -l 8 -png ' . escapeshellarg($pdfPath) . ' ' . escapeshellarg($prefix);
        @shell_exec($cmd);
        $images = glob($tmpDir . DIRECTORY_SEPARATOR . 'page-*.png');
        if ($images && count($images) > 0) {
            $pagesGenerated = true;
            break;
        }
    }

    if (!$pagesGenerated) {
        removeTree($tmpDir);
        return null;
    }

    $images = glob($tmpDir . DIRECTORY_SEPARATOR . 'page-*.png');
    sort($images);
    $allText = '';

    foreach ($images as $imagePath) {
        $pageText = '';
        foreach ($tesseractBins as $tesseract) {
            $binCmd = strpos($tesseract, ' ') !== false ? '"' . $tesseract . '"' : $tesseract;
            $langOrder = ['vie+eng', 'eng'];
            foreach ($langOrder as $lang) {
                $cmd = $binCmd . ' ' . escapeshellarg($imagePath) . ' stdout -l ' . $lang;
                $out = @shell_exec($cmd);
                if ($out && trim($out) !== '') {
                    $pageText = $out;
                    break;
                }
            }
            if ($pageText !== '') {
                break;
            }
        }
        if ($pageText !== '') {
            $allText .= "\n" . $pageText;
        }
    }

    removeTree($tmpDir);

    if (trim($allText) === '') {
        return null;
    }

    return $allText;
}

function buildPairsFromText($text, $maxPairs = 20) {
    // Chatbot-style parser first: detect Q/A markers and question semantics.
    $chatbotPairs = buildPairsWithChatbotParser($text, $maxPairs);
    if (count($chatbotPairs) >= 2) {
        return $chatbotPairs;
    }

    // Extra pass for numbered exam-style documents.
    $blockPairs = buildPairsFromQuestionBlocks($text, $maxPairs);
    if (count($blockPairs) >= 2) {
        return $blockPairs;
    }

    $lines = preg_split('/\r\n|\r|\n/', $text);
    $clean = [];
    foreach ($lines as $line) {
        $line = sanitizeLine($line);
        // Keep shorter lines too because slides/notes often have brief bullets.
        if (strlen($line) >= 8 && !preg_match('/^\d+$/', $line)) {
            $clean[] = $line;
        }
    }

    $pairs = [];
    // Strategy 1: pair neighboring lines (works well for Q/A-style notes).
    for ($i = 0; $i < count($clean) - 1 && count($pairs) < $maxPairs; $i += 2) {
        $question = safeSubstr($clean[$i], 0, 220);
        $answer = safeSubstr($clean[$i + 1], 0, 260);
        $pairs[] = [
            'question' => 'Explain: ' . $question,
            'answer' => $answer
        ];
    }

    if (count($pairs) >= 2) {
        return $pairs;
    }

    // Strategy 2: split by paragraphs and generate concept/explanation cards.
    $paragraphs = preg_split('/\n\s*\n/', $text);
    foreach ($paragraphs as $paragraph) {
        if (count($pairs) >= $maxPairs) {
            break;
        }
        $p = sanitizeLine($paragraph);
        if (strlen($p) < 40) {
            continue;
        }

        $question = safeSubstr($p, 0, 120);
        $answer = safeSubstr($p, 0, 260);
        $pairs[] = [
            'question' => 'What does this section mean: ' . $question . '?',
            'answer' => $answer
        ];
    }

    if (count($pairs) >= 2) {
        return $pairs;
    }

    // Strategy 3: sentence-based fallback for dense text blocks.
    $flat = sanitizeLine($text);
    $sentences = preg_split('/(?<=[\.!?])\s+/', $flat);
    $sentences = array_values(array_filter($sentences, function ($s) {
        return strlen(trim($s)) >= 25;
    }));

    for ($i = 0; $i < count($sentences) && count($pairs) < $maxPairs; $i++) {
        $question = safeSubstr($sentences[$i], 0, 160);
        $answerParts = [$sentences[$i]];
        if (isset($sentences[$i + 1])) {
            $answerParts[] = $sentences[$i + 1];
        }
        $answer = safeSubstr(implode(' ', $answerParts), 0, 260);
        $pairs[] = [
            'question' => 'Key idea: ' . $question,
            'answer' => $answer
        ];
    }

    return $pairs;
}

try {
    $db = new Database();
    $conn = $db->connect();

    $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int)$_SERVER['CONTENT_LENGTH'] : 0;
    $postMaxSize = bytesFromIniSize((string)ini_get('post_max_size'));
    if ($postMaxSize > 0 && $contentLength > $postMaxSize) {
        failJson(413, "Uploaded file is too large for current server setting (post_max_size). Try a smaller PDF or increase post_max_size.");
    }

    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $deckId = isset($_POST['deck_id']) ? (int)$_POST['deck_id'] : 0;
    $deckTitle = isset($_POST['deck_title']) ? trim($_POST['deck_title']) : '';
    $maxPairs = isset($_POST['max_pairs']) ? (int)$_POST['max_pairs'] : 80;
    if ($maxPairs < 5) {
        $maxPairs = 5;
    }
    if ($maxPairs > 300) {
        $maxPairs = 300;
    }

    if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        $uploadErr = isset($_FILES['pdf_file']) ? (int)$_FILES['pdf_file']['error'] : -1;
        if ($uploadErr === UPLOAD_ERR_INI_SIZE || $uploadErr === UPLOAD_ERR_FORM_SIZE) {
            failJson(413, "Uploaded PDF exceeds upload_max_filesize limit.");
        }
        failJson(400, "Please upload a valid PDF file");
    }

    $tmpPath = $_FILES['pdf_file']['tmp_name'];
    $mimeType = null;
    if (function_exists('mime_content_type')) {
        $mimeType = mime_content_type($tmpPath);
    }
    if (!$mimeType && function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mimeType = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);
        }
    }

    if ($mimeType && strpos($mimeType, 'pdf') === false) {
        failJson(400, "Uploaded file must be a PDF");
    }

    $coloredLines = extractColoredLinesFromPdf($tmpPath);
    $pairs = [];
    if (count($coloredLines) > 0) {
        $pairs = buildPairsFromColoredLines($coloredLines, $maxPairs);
    }

    $text = extractTextWithPdftotext($tmpPath);
    if (!$text || trim($text) === '') {
        $text = extractTextWithOcr($tmpPath);
    }

    if (!$text || trim($text) === '') {
        failJson(500, "Cannot extract text from PDF. Ensure Poppler (pdftotext/pdftoppm) and Tesseract OCR are installed.");
    }

    if (count($pairs) < 2) {
        $pairs = buildPairsFromText($text, $maxPairs);
    }
    if (count($pairs) < 2) {
        $text = extractTextWithOcr($tmpPath);
        $pairs = $text ? buildPairsFromText($text, $maxPairs) : [];
    }

    if (count($pairs) < 2) {
        failJson(422, "Not enough readable text to build flashcards, even after OCR.");
    }

    if ($deckId <= 0) {
        if ($deckTitle === '') {
            $deckTitle = 'AI Deck ' . date('Y-m-d H:i');
        }
        $ownerUserId = null;
        if ($userId > 0) {
            $checkUserStmt = $conn->prepare("SELECT id FROM users WHERE id = :user_id LIMIT 1");
            $checkUserStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $checkUserStmt->execute();
            $userExists = $checkUserStmt->fetch(PDO::FETCH_ASSOC);
            if ($userExists) {
                $ownerUserId = $userId;
            }
        }

        $hasAiGenerated = tableHasColumn($conn, 'decks', 'is_ai_generated');
        if ($hasAiGenerated) {
            $deckInsert = $conn->prepare("INSERT INTO decks (title, user_id, is_ai_generated) VALUES (:title, :user_id, 1)");
        } else {
            $deckInsert = $conn->prepare("INSERT INTO decks (title, user_id) VALUES (:title, :user_id)");
        }

        $deckInsert->bindParam(':title', $deckTitle);

        if ($ownerUserId === null) {
            $deckInsert->bindValue(':user_id', null, PDO::PARAM_NULL);
        } else {
            $deckInsert->bindValue(':user_id', $ownerUserId, PDO::PARAM_INT);
        }

        $deckInsert->execute();
        $deckId = (int)$conn->lastInsertId();
    }

    $insertCard = $conn->prepare("INSERT INTO cards (deck_id, question, answer) VALUES (:deck_id, :question, :answer)");
    $inserted = 0;
    foreach ($pairs as $pair) {
        $question = $pair['question'];
        $answer = $pair['answer'];
        $insertCard->bindParam(':deck_id', $deckId, PDO::PARAM_INT);
        $insertCard->bindParam(':question', $question);
        $insertCard->bindParam(':answer', $answer);
        $insertCard->execute();
        $inserted++;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Created flashcards from PDF",
        "deck_id" => $deckId,
        "inserted_cards" => $inserted
    ]);
} catch (Throwable $e) {
    failJson(500, "Server error while generating cards: " . $e->getMessage());
}
