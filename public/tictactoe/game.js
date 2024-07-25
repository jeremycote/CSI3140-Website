console.log("Welcome to Tic Tac Toe!");

var slots = [];
var leadSlots = [];
var restart_button;
var game_over = false;
var is_hovering_valid_slot = false;
var o_score_element;
var x_score_element;

function logout() {
  console.log("Logging out");
  window.location.href = "logout.php";
}

function get_current_player_token_html() {
  return "<p class='colour-x'>X</p>"; // Player is always X now
}

function updateBoard(board) {
  for (var i = 0; i < board.length; i++) {
    if (board[i] !== "-") {
      slots[i].innerHTML = `<p class='${
        board[i] === "X" ? "colour-x" : "colour-o"
      }'>${board[i]}</p>`;
    } else {
      slots[i].innerHTML = "";
    }
  }
}

function updateLeaderboard(leaderboard) {
  for (var i = 0; i < leaderboard.length; i++) {
    if (leaderboard[i].username && leaderboard[i].wins) {
      leadSlots[i].innerHTML = `<li><b>${leaderboard[i].username}</b>: ${
        leaderboard[i].wins
      } win${leaderboard[i].wins !== 1 ? "s" : ""}</li>`;
    } else {
      leadSlots[i].innerHTML = "";
    }
  }
}

function handleStatus(data) {
  if (data.board !== null) {
    updateBoard(data.board);
  }

  if (data.leaderboard !== null) {
    updateLeaderboard(data.leaderboard);
  }

  if (data.status !== "ongoing" && data.status !== "reset") {
    game_over = true;
    restart_button.style.visibility = "visible";
  } else {
    game_over = false;
    restart_button.style.visibility = "hidden";
  }

  if (data.status === "user_won" || data.status === "ai_won") {
    if (data.winner != null) {
      for (var i = 1; i < 4; i++) {
        var idx = Number.parseInt(data.winner[i]);
        slots[idx].classList.add("background-winner");
      }
    }
  } else {
    for (var i = 0; i < 9; i++) {
      slots[i].classList.remove("background-winner");
    }
  }

  if (data.xWins !== null) {
    x_score_element.innerHTML = data.xWins;
  }

  if (data.oWins != null) {
    o_score_element.innerHTML = data.oWins;
  }

  // TODO: Present pretty UI elements for game events such as draw, win or restart
}

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
  leadSlots = document.querySelectorAll("[data-leadSlot]");

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

  x_score_element = document.getElementById("x_score");
  o_score_element = document.getElementById("o_score");

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
        console.log(data);
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
