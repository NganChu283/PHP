<!DOCTYPE html>
<html>
<head>
<title>Flashcard Manager</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/manage.css">
</head>
<body>
<main class="layout">
<header class="topbar">
<h1>Flashcard Manager</h1>
<a class="back-link" href="./index.php">Back To Study</a>
</header>

<section class="panel">
<h2>Deck CRUD</h2>
<form class="row" onsubmit="createDeck(event)">
<input id="deckTitle" type="text" placeholder="New deck title" required>
<button type="submit">Add Deck</button>
</form>

<div class="row">
<select id="deckSelect" onchange="onDeckChange()"></select>
<input id="editDeckTitle" type="text" placeholder="Rename selected deck">
<button onclick="updateDeck()">Update Deck</button>
<button class="danger" onclick="deleteDeck()">Delete Deck</button>
</div>
</section>

<section class="panel">
<h2>Card CRUD</h2>
<form class="grid" onsubmit="createCard(event)">
<input id="cardQuestion" type="text" placeholder="Question" required>
<textarea id="cardAnswer" rows="4" placeholder="Answer" required></textarea>
<button type="submit">Add Card</button>
</form>

<div id="cardsList" class="cards-list"></div>
</section>

<div id="statusBox" class="status-box">Ready.</div>
</main>

<script src="../js/manage.js?v=20260407-2"></script>
</body>
</html>
