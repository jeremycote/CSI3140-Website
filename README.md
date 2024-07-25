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
# Install php (Mac instruction)
brew install php

# Start postgress instance
docker compose up -d

# Execute psql script to initialize database.
# Alternativly, execute contents of init.psql in pgadmin.
psql -h localhost -U admin -d tictactoe -f init.psql

# Start the php server
cd public
php -S localhost:8000
```

# Accounts

We have not implemented account creation as it was outside the scope of the project. You may use the following accounts.

<table>
  <thead>
    <tr>
      <th>Username</th>
      <th>Password</th>
      <th>Is Admin</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>jeremy</td>
      <td>ABC</td>
      <td>false</td>
    </tr>
    <tr>
      <td>jasmin</td>
      <td>DEF</td>
      <td>false</td>
    </tr>
    <tr>
      <td>TA</td>
      <td>XYZ</td>
      <td>true</td>
    </tr>
  </tbody>
</table>

# In case of issues

We've attached a Demo.mov file for demonstration purposes in case of difficulty. It should cover all necessary live demonstration features.
