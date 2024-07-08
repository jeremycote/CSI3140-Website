console.log("Welcome to Tic Tac Toe!");

var slots = [];
var restart_button;
var game_over = false;
var current_player = 0;
var is_hovering_valid_slot = false;

function get_current_player_token_html() {
  console.log("Current Player: ", current_player);
  return current_player === 0
    ? "<p class='colour-x'>X</p>"
    : "<p class='colour-o'>O</p>";
}

function updateBoard(board) {
  for (var i = 0; i < board.length; i++) {
    if (board[i] !== null) {
      slots[i].innerHTML = `<p class='${
        board[i] === 0 ? "colour-x" : "colour-o"
      }'>${board[i] === 0 ? "X" : "O"}</p>`;
    } else {
      slots[i].innerHTML = "";
    }
  }
}

function handleStatus(data) {
  if (data.board !== null) {
    updateBoard(data.board);
  }

  if (data.currentPlayer !== null) {
    console.log("Received player: ", data.currentPlayer);
    current_player = data.currentPlayer;
  }

  if (data.status !== "continue" && data.status !== "reset") {
    game_over = true;
    restart_button.style.visibility = "visible";
  } else {
    game_over = false;
    restart_button.style.visibility = "hidden";
  }

  // TODO: Present pretty UI elements for game events such as draw, win or restart
}

/**
 * Attempt to play at position
 * @param position integer id of slot
 */
function playTurn(position) {
  fetch("game.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `position=${position}`,
  })
    .then((response) => response.json())
    .then((data) => {
      handleStatus(data);
    })
    .catch((error) => console.error("Error:", error));
}

window.addEventListener("load", function () {
  slots = document.querySelectorAll("[data-slot]");

  slots.forEach((slot) => {
    slot.addEventListener("mouseenter", (event) => {
      if (game_over) {
        return;
      }

      if (event.target.innerHTML == "") {
        event.target.innerHTML = get_current_player_token_html();
        is_hovering_valid_slot = true;
      }
    });

    slot.addEventListener("mouseout", (event) => {
      if (game_over) {
        return;
      }

      if (is_hovering_valid_slot) {
        event.target.innerHTML = "";
      }

      is_hovering_valid_slot = false;
    });

    slot.addEventListener("click", (event) => {
      if (!is_hovering_valid_slot || game_over) {
        return;
      }

      is_hovering_valid_slot = false;
      playTurn(parseInt(event.target.getAttribute("data-slot")));
    });
  });

  restart_button = document.getElementById("restart-button");
  restart_button.addEventListener("click", (event) => {
    fetch("game.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `reset=true`,
    })
      .then((response) => response.json())
      .then((data) => {
        handleStatus(data);
      });
  });

  restart_button.style.visibility = "hidden";

  // Fetch game state on page load
  fetch("game.php", {
    method: "GET",
  })
    .then((response) => response.json())
    .then((data) => {
      handleStatus(data);
    })
    .catch((error) => console.error("Error:", error));
});
