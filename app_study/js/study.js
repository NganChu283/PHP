
let cards = [];
let decks = [];
let currentDeckId = 1;
let index = 0;
let currentMode = "flashcard";

let quizOrder = [];
let quizCursor = 0;
let quizScore = 0;
let quizAnswered = 0;
let currentCorrectAnswer = "";
let quizStreak = 0;
let quizTimer = 15;
let quizTimerId = null;
let quizLocked = false;

const currentUserId = Number(localStorage.getItem("study_user_id") || 1);
const SYNC_KEY = "study_sync_signal";

document.getElementById("userIdText").innerText = String(currentUserId);

init();

async function init() {
	await loadDecks();
	await loadCards(currentDeckId);
	await loadLeaderboard();
	setupControlledSync();
}

async function refreshHomeData(force = false) {
	const previousDeckId = currentDeckId;
	const previousDeck = decks.find((deck) => Number(deck.id) === Number(previousDeckId));
	const previousCardCount = previousDeck ? Number(previousDeck.card_count || 0) : null;

	await loadDecks();

	// Keep selected deck when possible, otherwise fall back to first available deck.
	if (decks.find((deck) => Number(deck.id) === Number(previousDeckId))) {
		currentDeckId = previousDeckId;
		document.getElementById("deckSelect").value = String(currentDeckId);
	}

	const latestDeck = decks.find((deck) => Number(deck.id) === Number(currentDeckId));
	const latestCardCount = latestDeck ? Number(latestDeck.card_count || 0) : null;

	// Avoid interrupting flashcard session unless data actually changed or user explicitly forces refresh.
	if (force || latestCardCount !== previousCardCount) {
		await loadCards(currentDeckId, { preserveCard: true });
	}
}

function setupControlledSync() {
	// Only sync when another page (manage.php) emits a CRUD signal.
	window.addEventListener("storage", (event) => {
		if (event.key !== SYNC_KEY || !event.newValue) {
			return;
		}

		refreshHomeData(false);
	});
}

async function loadDecks() {
	try {
		const res = await fetch("../api/get_decks.php");
		const payload = await res.json();
		decks = payload.decks || [];

		const deckSelect = document.getElementById("deckSelect");
		deckSelect.innerHTML = "";

		if (decks.length === 0) {
			const option = document.createElement("option");
			option.value = "0";
			option.innerText = "No deck";
			deckSelect.appendChild(option);
			currentDeckId = 0;
			return;
		}

		decks.forEach((deck) => {
			const option = document.createElement("option");
			option.value = deck.id;
			option.innerText = `${deck.title} (${deck.card_count} cards)`;
			deckSelect.appendChild(option);
		});

		if (!decks.find((deck) => Number(deck.id) === Number(currentDeckId))) {
			currentDeckId = Number(decks[0].id);
		}

		deckSelect.value = String(currentDeckId);
	} catch (error) {
		console.error(error);
	}
}

async function loadCards(deckId, options = {}) {
	const { preserveCard = false } = options;
	const previousCardId = preserveCard && cards[index] ? Number(cards[index].id) : null;
	const previousQuestion = preserveCard && cards[index] ? String(cards[index].question || "") : "";

	if (!deckId) {
		showEmptyState();
		return;
	}

	try {
		const res = await fetch(`../api/get_cards.php?deck_id=${deckId}`);
		const data = await res.json();
		cards = Array.isArray(data) ? data : [];

		if (preserveCard && previousCardId !== null) {
			const matchedIndex = cards.findIndex((card) => Number(card.id) === previousCardId);
			if (matchedIndex >= 0) {
				index = matchedIndex;
			} else {
				const fallbackIndex = cards.findIndex((card) => String(card.question || "") === previousQuestion);
				index = fallbackIndex >= 0 ? fallbackIndex : 0;
			}
		} else {
			index = 0;
		}

		if (cards.length === 0) {
			showEmptyState();
			return;
		}

		showCard();
		if (currentMode === "quiz") {
			startQuizRound();
		}
	} catch (error) {
		console.error(error);
		document.getElementById("question").innerText = "Cannot load cards.";
		document.getElementById("answer").innerText = "Please check API / DB.";
	}
}

function changeDeck() {
	const value = Number(document.getElementById("deckSelect").value);
	currentDeckId = value;
	loadCards(currentDeckId, { preserveCard: false });
}

function pickRandomDeck() {
	if (decks.length === 0) {
		return;
	}

	const randomDeck = decks[Math.floor(Math.random() * decks.length)];
	currentDeckId = Number(randomDeck.id);
	document.getElementById("deckSelect").value = String(currentDeckId);
	loadCards(currentDeckId, { preserveCard: false });
}

function setMode(mode) {
	currentMode = mode;

	const panelMap = {
		flashcard: "flashcardPanel",
		quiz: "quizPanel",
		create: "createPanel",
		leaderboard: "leaderboardPanel",
		ai: "aiPanel",
		chat: "chatPanel"
	};

	const buttonMap = {
		flashcard: "flashcardModeBtn",
		quiz: "quizModeBtn",
		create: "createModeBtn",
		leaderboard: "leaderboardModeBtn",
		ai: "aiModeBtn",
		chat: "chatModeBtn"
	};

	Object.values(panelMap).forEach((panelId) => {
		document.getElementById(panelId).classList.remove("active");
	});

	Object.values(buttonMap).forEach((buttonId) => {
		document.getElementById(buttonId).classList.remove("active");
	});

	document.getElementById(panelMap[mode]).classList.add("active");
	document.getElementById(buttonMap[mode]).classList.add("active");

	if (mode === "leaderboard") {
		loadLeaderboard();
	}
}

async function createDeck(event) {
	event.preventDefault();

	const titleInput = document.getElementById("newDeckTitle");
	const result = document.getElementById("createResult");
	const title = titleInput.value.trim();

	if (!title) {
		result.innerText = "Please enter a deck title.";
		return;
	}

	result.innerText = "Creating deck...";

	try {
		const res = await fetch("../api/create_deck.php", {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({ title, user_id: currentUserId })
		});
		const payload = await res.json();

		if (payload.status !== "success") {
			result.innerText = payload.message || "Cannot create deck.";
			return;
		}

		titleInput.value = "";
		await loadDecks();
		currentDeckId = Number(payload.deck_id);
		document.getElementById("deckSelect").value = String(currentDeckId);
		await loadCards(currentDeckId);
		result.innerText = `Deck created (#${payload.deck_id}). Now add cards.`;
	} catch (error) {
		console.error(error);
		result.innerText = "Cannot create deck.";
	}
}

async function createCard(event) {
	event.preventDefault();

	const questionInput = document.getElementById("newCardQuestion");
	const answerInput = document.getElementById("newCardAnswer");
	const result = document.getElementById("createResult");

	const question = questionInput.value.trim();
	const answer = answerInput.value.trim();

	if (!currentDeckId) {
		result.innerText = "Please create or select a deck first.";
		return;
	}

	if (!question || !answer) {
		result.innerText = "Question and answer are required.";
		return;
	}

	result.innerText = "Adding card...";

	try {
		const res = await fetch("../api/create_card.php", {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({
				deck_id: currentDeckId,
				question,
				answer
			})
		});
		const payload = await res.json();

		if (payload.status !== "success") {
			result.innerText = payload.message || "Cannot add card.";
			return;
		}

		questionInput.value = "";
		answerInput.value = "";
		await loadDecks();
		document.getElementById("deckSelect").value = String(currentDeckId);
		await loadCards(currentDeckId);
		result.innerText = "Card added successfully.";
	} catch (error) {
		console.error(error);
		result.innerText = "Cannot add card.";
	}
}

function showEmptyState() {
	document.getElementById("question").innerText = "No cards found.";
	document.getElementById("answer").innerText = "Create cards to start studying.";
	document.getElementById("quizQuestion").innerText = "No quiz available.";
	document.getElementById("quizOptions").innerHTML = "";
	stopQuizTimer();
	updateQuizMeta();
}

function showCard() {
	if (cards.length === 0) {
		showEmptyState();
		return;
	}

	const flipInner = document.getElementById("flipInner");
	flipInner.classList.remove("flipped");
	document.getElementById("question").innerText = cards[index].question;
	document.getElementById("answer").innerText = cards[index].answer;
}

function flipCard() {
	if (currentMode !== "flashcard") {
		return;
	}

	const flipInner = document.getElementById("flipInner");
	flipInner.classList.toggle("flipped");
}

function nextCard() {
	if (cards.length === 0) {
		return;
	}

	index++;
	if (index >= cards.length) {
		index = 0;
	}
	showCard();
}

function startQuizRound() {
	if (cards.length === 0) {
		showEmptyState();
		return;
	}

	quizOrder = cards.map((_, i) => i);
	shuffle(quizOrder);
	quizCursor = 0;
	quizScore = 0;
	quizAnswered = 0;
	quizStreak = 0;
	quizLocked = false;
	renderQuizQuestion();
}

function nextQuizQuestion() {
	if (cards.length === 0) {
		return;
	}

	if (quizCursor >= quizOrder.length - 1) {
		startQuizRound();
		return;
	}

	quizCursor++;
	quizLocked = false;
	renderQuizQuestion();
}

function renderQuizQuestion() {
	if (cards.length === 0 || quizOrder.length === 0) {
		return;
	}

	const card = cards[quizOrder[quizCursor]];
	currentCorrectAnswer = card.answer;
	document.getElementById("quizQuestion").innerText = card.question;

	const options = buildOptions(card.answer);
	const quizOptions = document.getElementById("quizOptions");
	quizOptions.innerHTML = "";

	options.forEach((option) => {
		const button = document.createElement("button");
		button.innerText = option;
		button.onclick = () => submitAnswer(button, option);
		quizOptions.appendChild(button);
	});

	startQuizTimer();
	updateQuizMeta();
}

function buildOptions(correctAnswer) {
	const pool = cards
		.map((card) => card.answer)
		.filter((answer) => answer !== correctAnswer);

	shuffle(pool);
	const choices = Array.from(new Set(pool.slice(0, 3)));
	choices.push(correctAnswer);
	return shuffle(Array.from(new Set(choices))).slice(0, 4);
}

function startQuizTimer() {
	stopQuizTimer();
	quizTimer = 15;
	document.getElementById("quizTimer").innerText = String(quizTimer);

	quizTimerId = setInterval(() => {
		quizTimer -= 1;
		document.getElementById("quizTimer").innerText = String(Math.max(quizTimer, 0));

		if (quizTimer <= 0) {
			stopQuizTimer();
			submitAnswer(null, null, true);
		}
	}, 1000);
}

function stopQuizTimer() {
	if (quizTimerId) {
		clearInterval(quizTimerId);
		quizTimerId = null;
	}
}

function submitAnswer(button, selectedAnswer, timedOut = false) {
	if (quizLocked) {
		return;
	}

	quizLocked = true;
	stopQuizTimer();

	const optionButtons = document.querySelectorAll("#quizOptions button");
	optionButtons.forEach((btn) => {
		btn.disabled = true;
		if (btn.innerText === currentCorrectAnswer) {
			btn.classList.add("correct");
		}
	});

	let isCorrect = false;
	if (!timedOut && selectedAnswer === currentCorrectAnswer) {
		isCorrect = true;
	}

	if (button && isCorrect) {
		button.classList.add("correct");
	}
	if (button && !isCorrect) {
		button.classList.add("wrong");
	}

	if (isCorrect) {
		quizScore++;
		quizStreak++;
	} else {
		quizStreak = 0;
	}

	quizAnswered++;
	updateQuizMeta();

	const xpGain = isCorrect ? 15 + Math.min(quizStreak * 2, 20) : 2;
	const isLastQuestion = quizAnswered >= quizOrder.length;

	syncProgress({
		xp_gain: xpGain,
		answered_gain: 1,
		correct_gain: isCorrect ? 1 : 0,
		current_streak: quizStreak,
		quiz_played_gain: isLastQuestion ? 1 : 0
	});

	if (isLastQuestion) {
		document.getElementById("quizQuestion").innerText = `Round complete. Score ${quizScore}/${quizOrder.length}`;
		return;
	}

	setTimeout(() => {
		quizCursor++;
		quizLocked = false;
		renderQuizQuestion();
	}, 700);
}

function updateQuizMeta() {
	const total = quizOrder.length > 0 ? quizOrder.length : cards.length;
	document.getElementById("quizMeta").innerText = `Score: ${quizScore} | Answered: ${quizAnswered}/${total}`;
	document.getElementById("quizStreak").innerText = String(quizStreak);
}

async function syncProgress(progress) {
	try {
		const res = await fetch("../api/update_progress.php", {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({ user_id: currentUserId, ...progress })
		});

		const payload = await res.json();
		if (payload.status === "success") {
			applyStats(payload.stats);
		}
	} catch (error) {
		console.error(error);
	}
}

function applyStats(stats) {
	if (!stats) {
		return;
	}
	document.getElementById("xpText").innerText = String(stats.xp || 0);
	document.getElementById("levelText").innerText = String(stats.level || 1);
	document.getElementById("bestStreakText").innerText = String(stats.best_streak || 0);
}

async function loadLeaderboard() {
	try {
		const res = await fetch("../api/get_leaderboard.php?limit=10");
		const payload = await res.json();
		const rows = payload.leaderboard || [];
		const body = document.getElementById("leaderboardBody");

		if (rows.length === 0) {
			body.innerText = "No data yet.";
			return;
		}

		body.innerHTML = rows
			.map((row) => {
				const me = Number(row.id) === currentUserId ? " me" : "";
				return `<div class="leader-row${me}"><span>#${row.rank} ${row.username}</span><span>Lv.${row.level} | XP ${row.xp} | ${row.accuracy}%</span></div>`;
			})
			.join("");

		const current = rows.find((row) => Number(row.id) === currentUserId);
		if (current) {
			applyStats(current);
		}
	} catch (error) {
		console.error(error);
	}
}

async function generateFromPdf(event) {
	event.preventDefault();

	const fileInput = document.getElementById("pdfFileInput");
	const deckTitle = document.getElementById("deckTitleInput").value.trim();
	const result = document.getElementById("pdfResult");

	if (!fileInput.files || fileInput.files.length === 0) {
		result.innerText = "Please choose a PDF file.";
		return;
	}

	const formData = new FormData();
	formData.append("pdf_file", fileInput.files[0]);
	formData.append("user_id", String(currentUserId));
	formData.append("deck_title", deckTitle);

	result.innerText = "Generating cards from PDF...";

	try {
		const res = await fetch("../api/generate_cards_from_pdf.php", {
			method: "POST",
			body: formData
		});

		const raw = await res.text();
		let payload = null;
		try {
			payload = JSON.parse(raw);
		} catch (jsonErr) {
			const jsonStart = raw.indexOf("{");
			const jsonEnd = raw.lastIndexOf("}");
			if (jsonStart >= 0 && jsonEnd > jsonStart) {
				const possibleJson = raw.slice(jsonStart, jsonEnd + 1);
				try {
					payload = JSON.parse(possibleJson);
				} catch (innerErr) {
					throw new Error(raw || "Invalid server response");
				}
			} else {
				throw new Error(raw || "Invalid server response");
			}
		}

		if (payload.status !== "success") {
			result.innerText = payload.message || "Generation failed.";
			return;
		}

		result.innerText = `Done. Created ${payload.inserted_cards} cards in deck #${payload.deck_id}.`;
		await loadDecks();
		currentDeckId = Number(payload.deck_id);
		document.getElementById("deckSelect").value = String(currentDeckId);
		await loadCards(currentDeckId);
		fileInput.value = "";
	} catch (error) {
		console.error(error);
		result.innerText = `Cannot generate cards from PDF: ${error.message || "Unknown error"}`;
	}
}

async function askChatbot(event) {
	event.preventDefault();

	const message = document.getElementById("chatInput").value.trim();
	const output = document.getElementById("chatOutput");

	if (!message) {
		output.innerText = "Please type a question.";
		return;
	}

	output.innerText = "Thinking...";

	try {
		const res = await fetch("../api/chat_explain.php", {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({ deck_id: currentDeckId, message })
		});

		const payload = await res.json();
		output.innerText = payload.reply || "Bot has no response.";
	} catch (error) {
		console.error(error);
		output.innerText = "Cannot connect to chatbot.";
	}
}

function shuffle(arr) {
	for (let i = arr.length - 1; i > 0; i--) {
		const j = Math.floor(Math.random() * (i + 1));
		[arr[i], arr[j]] = [arr[j], arr[i]];
	}
	return arr;
}
