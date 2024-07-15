# Website for CSI3140 Final Project

Jasmin Cartier: 300 160 723
Jeremy Côté: 300 171 029

Project Repo: https://github.com/jeremycote/CSI3140-Website

# Git Commands

```bash
# Pull changes from TicTacToe repo
git subtree pull --prefix public/tictactoe git@github.com:jeremycote/CSI3140-TicTacToe.git main --squash

# Push changes back to TicTacToeRepo
git subtree push --prefix public/tictactoe/ git@github.com:jeremycote/CSI3140-TicTacToe.git main
```

# Using the project

```bash
cd public
php -S localhost:8000
```
