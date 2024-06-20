console.log("Welcome to Tic Tac Toe!");

var slots = [];

var restart_button;

var current_player = 0;
var counter = 0;
var score;
var scoreElement;

var is_hovering_valid_slot = false;

var game_over = false;

function get_current_player_token_html() {
  if (current_player == 0) {
    return "<p class='colour-x'>X</p>";
  } else {
    return "<p class='colour-o'>O</p>";
  }
}

function is_game_over() {
  for (var i = 0; i < 3; i++) {
    if (
      slots[i].innerHTML != "" &&
      slots[i].innerHTML == slots[i + 3].innerHTML &&
      slots[i].innerHTML == slots[i + 6].innerHTML
    ) {
      if (current_player == 0){
        scoreElement = document.getElementById("x_score");      
        score = parseInt(scoreElement.textContent);
        score++;
        scoreElement.textContent = score.toString();
      }
      else{
        scoreElement = document.getElementById("o_score");
        score = parseInt(scoreElement.textContent);
        score++;
        scoreElement.textContent = score.toString();
      }
      
      counter = 0;
      return [i, i + 3, i + 6];
    }
  }

  for(var i = 0; i < 9; i+=3){
    if (
      slots[i].innerHTML != "" &&
      slots[i].innerHTML == slots[i + 1].innerHTML &&
      slots[i].innerHTML == slots[i + 2].innerHTML
    ) {
      if (current_player == 0){
        scoreElement = document.getElementById("x_score");
        score = parseInt(scoreElement.textContent);
        score++;
        scoreElement.textContent = score.toString();
      }
      else{
        scoreElement = document.getElementById("o_score");
        score = parseInt(scoreElement.textContent);
        score++;
        scoreElement.textContent = score.toString();
      }

      counter = 0;
      return [i, i + 1, i + 2];
    }
  }

  if (
    slots[0].innerHTML != "" &&
    slots[0].innerHTML == slots[4].innerHTML &&
    slots[0].innerHTML == slots[8].innerHTML
  ) {
    if (current_player == 0){
      scoreElement = document.getElementById("x_score");
      score = parseInt(scoreElement.textContent);
      score++;
      scoreElement.textContent = score.toString();
    }
    else{
      scoreElement = document.getElementById("o_score"); 
      score = parseInt(scoreElement.textContent);
      score++;
      scoreElement.textContent = score.toString();
    }

    counter = 0;
    return [0, 4, 8];
  } else if (
    slots[2].innerHTML != "" &&
    slots[2].innerHTML == slots[4].innerHTML &&
    slots[2].innerHTML == slots[6].innerHTML
  ) {
    if (current_player == 0){
      scorescoreElementElement = document.getElementById("x_score");      
      score = parseInt(scoreElement.textContent);
      score++;
      scoreElement.textContent = score.toString();
    }
    else{
      scoreElement = document.getElementById("o_score");      
      score = parseInt(scoreElement.textContent);
      score++;
      scoreElement.textContent = score.toString();
    }

    counter = 0;
    return [2, 4, 6];
  }

  return [];
}

function next_player() {
  current_player = (current_player + 1) % 2;
  counter++;
  if(counter == 9){
    counter = 0;
    game_over = true;
    restart_button.style.visibility = "visible";
  }
  return
}

function play_turn(slot) {
  if (game_over) {
    return;
  }

  slot.innerHTML = get_current_player_token_html();

  var is_over = is_game_over();

  if (is_over.length != 0) {
    console.log(is_over);
    game_over = true;

    restart_button.style.visibility = "visible";

    for (var i = 0; i < 3; i++) {
      var index = is_over[i];
      slots[index].classList.add("background-winner");
    }

    return;
  }

  next_player();
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
      if (!is_hovering_valid_slot) {
        return;
      }

      is_hovering_valid_slot = false;

      play_turn(event.target);

      var open = [];

      for (var i = 0; i < 9; i++) {
        if (slots[i].innerHTML == "") {
          open.push(i);
        }
      }

      var i = Math.floor(Math.random() * open.length);
      var index = open[i];
      play_turn(slots[index]);
    });
  });

  restart_button = document.getElementById("restart-button");
  restart_button.addEventListener("click", (event) => {
    for (var i = 0; i < 9; i++) {
      slots[i].innerHTML = "";
      slots[i].classList.remove("background-winner");
    }

    game_over = false;
    current_player = 0;

    restart_button.style.visibility = "hidden";
  });

  restart_button.style.visibility = "hidden";
});
