<?php

	function playGame()
	{
		$board = resetBoard();
		$players = ['X', 'O'];
		$currentPlayer = 1;
		$playerScores = ['X' => 0, 'O' => 0];

		while (1) {
			showBoard($board);
			$currentPlayer = abs($currentPlayer - 1);

			$result['valid'] = false;
			while ($result['valid'] === false) {
				$result = requestCell($board, $currentPlayer, $players);
			}

			$board = $result['board'];

			if ($win = checkWinner($board)) {
				$winner = $win['player'];
				echo "\nPlayer {$winner} has won this match!";
				$playerScores[$winner]++;

				drawWin($board, $win['cells']);

				printScore($players, $playerScores);
				$board = resetBoard();
				continue;
			}

			if (boardFull($board)) {
				echo "\nNeither player won this match.\n";
				echo "\nStarting next match.\n";
				printScore($players, $playerScores);
				$board = resetBoard();
			}
		}
	}

	function resetBoard()
	{
		$row = ['', '', ''];
		return [$row,$row,$row];
	}

	function drawWin($board, $cells)
	{
		$iidx = 0;

		echo "\n\n";
		foreach ($board as $idx => $row) {
			echo "\n-------------\n|";
			foreach ($row as $iidx => $cell) {
				if (in_array([$idx, $iidx], $cells))
					echo " ${cell} |";
				else
					echo "   |";
			}
		}
		echo "\n-------------\n";
	}

	function showBoard($board)
	{
		$iidx = 0;

		foreach ($board as $row) {
			echo "\n-------------\n|";
			foreach ($row as $cell) {
				$iidx++;
				echo ($cell === '') 
					? " $iidx |" 
					: " ${cell} |";
			}
		}
		echo "\n-------------\n";
		echo "\n";
	}

	function requestCell($board, $currentPlayer, $players)
	{
		echo "Player {$players[$currentPlayer]}, please choose a square: ";
		$response = readline();
		$result = addResponse($board, $players[$currentPlayer], $response);
		echo "\n";
		return $result;
	}

	function addResponse($board, $player, $response)
	{
		$response = (int)$response;

		if ($response < 1 || $response > 9) {
			echo "Please enter a valid square\n";
			return ['valid' => false];
		}

		$row = 0;
		$cell = 0;
		while ($response > 3) {
			$response -= 3;
			$row++;
		}

		while ($response > 1) {
			$response--;
			$cell++;
		}

		if ($board[$row][$cell] !== '') {
			echo "Your choice has already been taken\n";
			return ['valid' => false];
		}

		$board[$row][$cell] = $player;

		return ['valid' => true, 'board' => $board];
	}

	function boardFull($board)
	{
		foreach ($board as $row) {
			foreach ($row as $cell) {
				if ($cell === '')
					return false;
			}
		}
		return true;
	}

	function checkWinner($board)
	{
		foreach ($board as $key => $row)
			if ($test = checkMatch($row[0], $row[1], $row[2]))
			{
				return ['player' => $test, 'cells' => [[$key, 0], [$key, 1], [$key, 2]]];
			}

		for ($idx = 0; $idx < 3; $idx++)
			if ($test = checkMatch($board[0][$idx], $board[1][$idx], $board[2][$idx]))
				return ['player' => $test, 'cells' => [[0, $idx], [1, $idx], [2, $idx]]];

		if ($test = checkMatch($board[0][0], $board[1][1], $board[2][2]))
			return ['player' => $board[2][2], 'cells' => [[0, 0], [1, 1], [2, 2]]];

		if ($test = checkMatch($board[0][2], $board[1][1], $board[2][0]))
			return ['player' => $board[2][0], 'cells' => [[0, 2], [1, 1], [2, 0]]];

		return false;
	}

	function checkMatch($cell1, $cell2, $cell3)
	{
		if ($cell1 === $cell2 && $cell2 === $cell3 && $cell3 !== '')
			return $cell3;

		return false;
	}

	function printScore($players, $playerScores)
	{
		echo "\n\nCurrent Score:\n";
		foreach ($players as $player)
			echo "Player {$player} has won ${playerScores[$player]} games.\n";
		echo "\n------------------------\n\nStarting next match\n\n";	
	}

	playGame();