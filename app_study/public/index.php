
<!DOCTYPE html>
<html>
<head>
<title>Study App Upgrade</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="bg-shape bg-shape-1"></div>
<div class="bg-shape bg-shape-2"></div>

<main class="container">
<header>
<h1>Study Arena</h1>
<p>Flashcard, Quizizz-style challenge, AI PDF card generator, chatbot, and leaderboard.</p>
<p><a class="header-link" href="./manage.php">Open Full CRUD Manager</a></p>
</header>

<section class="status-bar">
<div class="chip">User ID: <span id="userIdText">1</span></div>
<div class="chip">XP: <span id="xpText">0</span></div>
<div class="chip">Level: <span id="levelText">1</span></div>
<div class="chip">Best Streak: <span id="bestStreakText">0</span></div>
</section>

<section class="deck-controls">
<label for="deckSelect">Deck:</label>
<select id="deckSelect" onchange="changeDeck()"></select>
<button onclick="pickRandomDeck()">Random Deck</button>
<button onclick="refreshHomeData(true)">Refresh</button>
</section>

<div class="mode-switch">
<button id="flashcardModeBtn" class="active" onclick="setMode('flashcard')">Flashcard</button>
<button id="quizModeBtn" onclick="setMode('quiz')">Quiz</button>
<button id="createModeBtn" onclick="setMode('create')">Create</button>
<button id="leaderboardModeBtn" onclick="setMode('leaderboard')">Leaderboard</button>
<button id="aiModeBtn" onclick="setMode('ai')">AI PDF</button>
<button id="chatModeBtn" onclick="setMode('chat')">Chatbot</button>
</div>

<section id="flashcardPanel" class="panel active">
<div class="flip-card" onclick="flipCard()">
<div class="flip-card-inner" id="flipInner">
<div class="flip-face flip-front">
<h2>Question</h2>
<p id="question">Loading...</p>
</div>
<div class="flip-face flip-back">
<h2>Answer</h2>
<p id="answer">Click to reveal</p>
</div>
</div>
</div>
<div class="flashcard-actions">
<button onclick="flipCard()">Flip</button>
<button onclick="nextCard()">Next</button>
</div>
</section>

<section id="quizPanel" class="panel">
<div class="quiz-head">
<h2 id="quizQuestion">Question</h2>
<div id="quizMeta">Score: 0 | Answered: 0/0</div>
<div id="quizLive">Timer: <span id="quizTimer">15</span>s | Streak: <span id="quizStreak">0</span></div>
</div>
<div id="quizOptions" class="quiz-options"></div>
<div class="quiz-actions">
<button onclick="startQuizRound()">Start Round</button>
<button onclick="nextQuizQuestion()">Next Question</button>
</div>
</section>

<section id="createPanel" class="panel">
<h2>Create Flashcards</h2>

<form class="form-grid" onsubmit="createDeck(event)">
<input id="newDeckTitle" type="text" placeholder="Deck title" required>
<button type="submit">Create Deck</button>
</form>

<form class="form-grid" onsubmit="createCard(event)">
<input id="newCardQuestion" type="text" placeholder="Question" required>
<textarea id="newCardAnswer" rows="4" placeholder="Answer" required></textarea>
<button type="submit">Add Card To Current Deck</button>
</form>

<div id="createResult" class="result-box">Create a deck, then add cards to the selected deck.</div>
</section>

<section id="leaderboardPanel" class="panel">
<h2>Leaderboard</h2>
<div id="leaderboardBody" class="leaderboard-list">No data yet.</div>
<div class="quiz-actions">
<button onclick="loadLeaderboard()">Refresh Leaderboard</button>
</div>
</section>

<section id="aiPanel" class="panel">
<h2>Generate Flashcards from PDF</h2>
<form id="pdfForm" class="form-grid" onsubmit="generateFromPdf(event)">
<input id="deckTitleInput" type="text" placeholder="New deck title (optional)">
<input id="pdfFileInput" type="file" accept="application/pdf" required>
<button type="submit">Upload + Generate</button>
</form>
<div id="pdfResult" class="result-box">Upload a PDF to auto-create cards.</div>
</section>

<section id="chatPanel" class="panel">
<h2>Deck Chatbot</h2>
<div id="chatOutput" class="chat-output">Ask something about the selected deck.</div>
<form class="form-grid" onsubmit="askChatbot(event)">
<textarea id="chatInput" rows="4" placeholder="Example: Explain the main concept in this deck"></textarea>
<button type="submit">Ask Bot</button>
</form>
</section>

</main>

<script src="../js/study.js?v=20260407-2"></script>

</body>
</html>
