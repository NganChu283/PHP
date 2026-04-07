let decks = [];
let cards = [];
let currentDeckId = 0;
const currentUserId = Number(localStorage.getItem("study_user_id") || 1);
const SYNC_KEY = "study_sync_signal";

init();

async function init() {
  await loadDecks();
}

function setStatus(message) {
  document.getElementById("statusBox").innerText = message;
}

function emitHomeSync(action) {
  localStorage.setItem(
    SYNC_KEY,
    JSON.stringify({ action, deck_id: currentDeckId, at: Date.now() })
  );
}

async function loadDecks() {
  try {
    const res = await fetch("../api/get_decks.php");
    const payload = await res.json();
    decks = payload.decks || [];

    const deckSelect = document.getElementById("deckSelect");
    deckSelect.innerHTML = "";

    if (decks.length === 0) {
      currentDeckId = 0;
      const option = document.createElement("option");
      option.value = "0";
      option.innerText = "No deck";
      deckSelect.appendChild(option);
      document.getElementById("cardsList").innerHTML = "";
      setStatus("No decks yet. Create one.");
      return;
    }

    decks.forEach((deck) => {
      const option = document.createElement("option");
      option.value = deck.id;
      option.innerText = `${deck.title} (${deck.card_count} cards)`;
      deckSelect.appendChild(option);
    });

    if (!decks.find((d) => Number(d.id) === Number(currentDeckId))) {
      currentDeckId = Number(decks[0].id);
    }

    deckSelect.value = String(currentDeckId);
    syncDeckEditInput();
    await loadCards();
  } catch (error) {
    console.error(error);
    setStatus("Cannot load decks.");
  }
}

function onDeckChange() {
  currentDeckId = Number(document.getElementById("deckSelect").value);
  syncDeckEditInput();
  loadCards();
}

function syncDeckEditInput() {
  const deck = decks.find((d) => Number(d.id) === Number(currentDeckId));
  document.getElementById("editDeckTitle").value = deck ? deck.title : "";
}

async function createDeck(event) {
  event.preventDefault();
  const titleInput = document.getElementById("deckTitle");
  const title = titleInput.value.trim();

  if (!title) {
    setStatus("Deck title is required.");
    return;
  }

  try {
    const res = await fetch("../api/create_deck.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ title, user_id: currentUserId })
    });
    const payload = await res.json();

    if (payload.status !== "success") {
      setStatus(payload.message || "Cannot create deck.");
      return;
    }

    titleInput.value = "";
    currentDeckId = Number(payload.deck_id);
    await loadDecks();
    emitHomeSync("create_deck");
    setStatus("Deck created.");
  } catch (error) {
    console.error(error);
    setStatus("Cannot create deck.");
  }
}

async function updateDeck() {
  if (!currentDeckId) {
    setStatus("Select a deck first.");
    return;
  }

  const title = document.getElementById("editDeckTitle").value.trim();
  if (!title) {
    setStatus("Deck title is required.");
    return;
  }

  try {
    const res = await fetch("../api/update_deck.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ deck_id: currentDeckId, title })
    });
    const payload = await res.json();

    if (payload.status !== "success") {
      setStatus(payload.message || "Cannot update deck.");
      return;
    }

    await loadDecks();
    emitHomeSync("update_deck");
    setStatus("Deck updated.");
  } catch (error) {
    console.error(error);
    setStatus("Cannot update deck.");
  }
}

async function deleteDeck() {
  if (!currentDeckId) {
    setStatus("Select a deck first.");
    return;
  }

  const ok = window.confirm("Delete this deck and all cards inside it?");
  if (!ok) {
    return;
  }

  try {
    const res = await fetch("../api/delete_deck.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ deck_id: currentDeckId })
    });
    const payload = await res.json();

    if (payload.status !== "success") {
      setStatus(payload.message || "Cannot delete deck.");
      return;
    }

    currentDeckId = 0;
    await loadDecks();
    emitHomeSync("delete_deck");
    setStatus("Deck deleted.");
  } catch (error) {
    console.error(error);
    setStatus("Cannot delete deck.");
  }
}

async function loadCards() {
  if (!currentDeckId) {
    cards = [];
    renderCards();
    return;
  }

  try {
    const res = await fetch(`../api/get_cards.php?deck_id=${currentDeckId}`);
    cards = await res.json();
    if (!Array.isArray(cards)) {
      cards = [];
    }
    renderCards();
  } catch (error) {
    console.error(error);
    setStatus("Cannot load cards.");
  }
}

async function createCard(event) {
  event.preventDefault();

  if (!currentDeckId) {
    setStatus("Create or select a deck first.");
    return;
  }

  const questionInput = document.getElementById("cardQuestion");
  const answerInput = document.getElementById("cardAnswer");
  const question = questionInput.value.trim();
  const answer = answerInput.value.trim();

  if (!question || !answer) {
    setStatus("Question and answer are required.");
    return;
  }

  try {
    const res = await fetch("../api/create_card.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ deck_id: currentDeckId, question, answer })
    });
    const payload = await res.json();

    if (payload.status !== "success") {
      setStatus(payload.message || "Cannot add card.");
      return;
    }

    questionInput.value = "";
    answerInput.value = "";
    await loadDecks();
    await loadCards();
    emitHomeSync("create_card");
    setStatus("Card added.");
  } catch (error) {
    console.error(error);
    setStatus("Cannot add card.");
  }
}

function renderCards() {
  const list = document.getElementById("cardsList");

  if (cards.length === 0) {
    list.innerHTML = '<div class="card-item">No cards in this deck.</div>';
    return;
  }

  list.innerHTML = cards
    .map((card) => {
      const q = escapeHtml(card.question || "");
      const a = escapeHtml(card.answer || "");
      return `
        <article class="card-item" data-id="${card.id}">
          <div class="q">Q: ${q}</div>
          <div class="a">A: ${a}</div>
          <div class="card-actions">
            <button onclick="editCard(${card.id})">Edit</button>
            <button class="danger" onclick="deleteCard(${card.id})">Delete</button>
          </div>
        </article>
      `;
    })
    .join("");
}

async function editCard(cardId) {
  const card = cards.find((c) => Number(c.id) === Number(cardId));
  if (!card) {
    return;
  }

  const newQuestion = window.prompt("Edit question:", card.question || "");
  if (newQuestion === null) {
    return;
  }

  const newAnswer = window.prompt("Edit answer:", card.answer || "");
  if (newAnswer === null) {
    return;
  }

  if (!newQuestion.trim() || !newAnswer.trim()) {
    setStatus("Question and answer cannot be empty.");
    return;
  }

  try {
    const res = await fetch("../api/update_card.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ card_id: cardId, question: newQuestion.trim(), answer: newAnswer.trim() })
    });
    const payload = await res.json();

    if (payload.status !== "success") {
      setStatus(payload.message || "Cannot update card.");
      return;
    }

    await loadDecks();
    await loadCards();
    emitHomeSync("update_card");
    setStatus("Card updated.");
  } catch (error) {
    console.error(error);
    setStatus("Cannot update card.");
  }
}

async function deleteCard(cardId) {
  const ok = window.confirm("Delete this card?");
  if (!ok) {
    return;
  }

  try {
    const res = await fetch("../api/delete_card.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ card_id: cardId })
    });
    const payload = await res.json();

    if (payload.status !== "success") {
      setStatus(payload.message || "Cannot delete card.");
      return;
    }

    await loadDecks();
    await loadCards();
    emitHomeSync("delete_card");
    setStatus("Card deleted.");
  } catch (error) {
    console.error(error);
    setStatus("Cannot delete card.");
  }
}

function escapeHtml(text) {
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/\"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
