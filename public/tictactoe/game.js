console.log("Welcome to Tic Tac Toe!");

var slots = [];

var current_player = 0;
var is_hovering_valid_slot = false;

function get_current_player_token_html() {
  if (current_player == 0) {
    return "<p class='colour-x'>X</p>";
  } else {
    return "<p class='colour-o'>O</p>";
  }
}

function next_player() {
  current_player = (current_player + 1) % 2;
}

window.addEventListener("load", function () {
  slots = document.querySelectorAll("[data-slot]");

  slots.forEach((slot) => {
    slot.addEventListener("mouseenter", (event) => {
      console.log(event.target.getAttribute("data-slot"));

      if (event.target.innerHTML == "") {
        event.target.innerHTML = get_current_player_token_html();
        is_hovering_valid_slot = true;
      }
    });

    slot.addEventListener("mouseout", (event) => {
      if (is_hovering_valid_slot) {
        event.target.innerHTML = "";
      }

      is_hovering_valid_slot = false;
    });

    slot.addEventListener("click", (event) => {
      console.log("Click");
      if (is_hovering_valid_slot) {
        event.target.innerHTML = get_current_player_token_html();
        is_hovering_valid_slot = false;
        next_player();
      }
    });
  });
});
