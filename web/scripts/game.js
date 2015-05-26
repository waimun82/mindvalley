function game() {
	
	var gameObject = new Object();
	
	// @access public
	var defaultRows = 4; // Default row of grid
	var defaultColumns = 4; // Default column of grid
	var defaultStartingBlocks = 2; // Default number of starting blocks
	var defaultAddOnBlocks = 1; // Default number of blocks to add on each move
	var defaultEndState = 2048; // Default end state of the game

	/*
	* Create 2-dimensional arrays
	* @access private
	*/
	function create2DArray() {
		var array = new Array(defaultRows);
		for (counter = 0; counter < defaultRows; counter++) {
			array[counter] = new Array(defaultColumns);
		}
		return array;
	}
	
	/*
	* Create HTML grid and populate
	* @access private
	* @param array $cell - Cell array to create HTML
	*/
	function createGrid(cell) {
		var table = '<table border="0" width="100%" cellpadding="0" cellspacing="0" class="game-grid">';
		for(y = 0; y < cell.length; y++) {
			table += '<tr>'
			for(x = 0; x < cell[y].length; x++) {
				table += '<td id="'+y+'-'+x+'"></td>';
			}
			table += '</tr>';
		}
		table += '</table>';
		document.getElementById('game-control').style.visibility = 'visible';
		document.getElementById('game-grid').innerHTML = table;
	}

	/*
	* Generate random number from 0 onwards
	* @access private
	* @param integer $limit - Limit of the random number
	*/
	function generateRandom(limit) {
		return Math.floor((Math.random() * limit));
	}

	/*
	* Add new block to grid
	* @access private
	* @param array $cell - Cell array to populate
	* @param integer $quantity - Number of blocks to add
	*/
	function addBlock(cell, quantity) {
		y = new Array();
		x = new Array();
		num = new Array();
		for(key = 0; key < quantity; key++) {
			y[key] = generateRandom(defaultRows);
			x[key] = generateRandom(defaultColumns);
			num[key] = (generateRandom(2) + 1) * 2; // generateRandom(2) + 1 * 2 due to number must be 2 or 4
			while(cell[y[key]][x[key]] != undefined) {
				y[key] = generateRandom(defaultRows);
				x[key] = generateRandom(defaultColumns);
			}
			cell[y[key]][x[key]] = num[key];
		}
	}

	/*
	* Check grid array and populate grid cell value
	* @access private
	* @param array $cell - Cell array to populate
	*/
	function populateGrid(cell) {
		for(y = 0; y < cell.length; y++) {
			for(x = 0; x < cell[y].length; x++) {
				var color = '';	
				document.getElementById(y+'-'+x).style.backgroundColor = color;
				document.getElementById(y+'-'+x).innerHTML = '';
				if(cell[y][x] != undefined) {		
					switch(cell[y][x]) {
						case 2: color = '#FFFFFF'; break;
						case 4: color = '#FFFCCF'; break;
						case 8: color = '#FFE8CF'; break;
						case 16: color = '#FCFFCF'; break;
						case 32: color = '#DDFFCF'; break;
						case 64: color = '#CFFFE8'; break;
						case 128: color = '#CFEDFF'; break;
						case 256: color = '#D2CFFF'; break;
						case 512: color = '#FFCFFF'; break;
						case 1024: color = '#FFCFE2'; break;
						case 2048: color = '#FC8C8C'; break;
					}
					document.getElementById(y+'-'+x).style.backgroundColor = color;
					document.getElementById(y+'-'+x).innerHTML = cell[y][x];	
				}
			}
		}
	}
	
	/*
	* Check grid array if the victory condition has been met or grid array has been fully populated
	* @access private
	* @param array $cell - Cell array to check
	* @param integer $condition - End state condition
	* @param integer $score - End state score
	*/
	function checkEndState(cell, condition, score) {
		var win = false;
		var cont = false;
		for(y = 0; y < cell.length; y++) {
			for(x = 0; x < cell[y].length; x++) {
				if(cell[y][x] == 2048) {
					win = true;
				} else if(cell[y][x] == undefined) {
					cont = true;
				}
			}
		}
		if(win == true) {
			alert('COMPLETED!\nYOUR SCORE: '+score);
		} else if(cont != true) {
			alert('GAME OVER!\nYOUR SCORE: '+score);
		}
	}
	
	/*
	* Update score in HTML
	* @access private
	* @param integer $score - Score to update in HTML
	*/
	function updateScore(score) {
		document.getElementById('score').innerHTML = score;
	}

	/*
	* Create new game by populating two cells with 2 or 4
	* @access public
	* @param integer $blocks - Starting blocks to generate
	*/
	function newGame() {
		
		// Starting score
		var score = 0;

		// Create new grid matrix array
		var cell = create2DArray();

		// Create grid HTML
		createGrid(cell);

		// Create starting blocks
		addBlock(cell, defaultStartingBlocks);

		/*
		* Controller to navigate block
		* @access public
		* @param integer $direction - Direction to navigate blocks (1 = Move top, 2 = Move right, 3 = Move bottom, 4 = Move left)
		*/
		function navigateBlock(direction) {
			
			var rows = cell.length;
			var columns = cell[0].length;
			
			switch(direction) {
				
				// Move top
				case 1:
					for(x = 0; x < columns; x++) {
						for(y = 0; y < rows; y++) {
							if(cell[y][x] != undefined) {
								for(key = y; key < rows; key++) {
									if((y != key) && (cell[key][x] != undefined) && (cell[y][x] == cell[key][x])) {
										cell[y][x] += cell[key][x];
										cell[key][x] = undefined;
										score += cell[y][x];
									}
								}
							}
						}
						for(y = 0; y < rows; y++) {
							if(cell[y][x] == undefined) {
								for(key = y; key < rows; key++) {
									if((cell[y][x] == undefined) && (cell[key][x] != undefined)) {
										cell[y][x] = cell[key][x];
										cell[key][x] = undefined;
									}
								}
							}
						}
					}
					break;
				
				// Move right
				case 2:
					for(y = 0; y < rows; y++) {
						for(x = columns -1; x >= 0; x--) {
							if(cell[y][x] != undefined) {
								for(key = x; key >= 0; key--) {
									if((x != key) && (cell[y][key] != undefined) && (cell[y][x] == cell[y][key])) {
										cell[y][x] += cell[y][key];
										cell[y][key] = undefined;
										score += cell[y][x];
									}
								}
							}
						}
						for(x = columns -1; x >= 0; x--) {
							if(cell[y][x] == undefined) {
								for(key = x; key >= 0; key--) {
									if((cell[y][x] == undefined) &&(cell[y][key] != undefined)) {
										cell[y][x] = cell[y][key];
										cell[y][key] = undefined;
									}
								}
							}
						}
					}
					break;
				
				// Move bottom
				case 3: 
					for(x = 0; x < columns; x++) {
						for(y = rows - 1; y >= 0; y--) {
							if(cell[y][x] != undefined) {
								for(key = y; key >= 0; key--) {
									if((y != key) && (cell[key][x] != undefined) && (cell[y][x] == cell[key][x])) {
										cell[y][x] += cell[key][x];
										cell[key][x] = undefined;
										score += cell[y][x];
									}
								}
							}
						}
						for(y = rows - 1; y >= 0; y--) {
							if(cell[y][x] == undefined) {
								for(key = y; key >= 0; key--) {
									if((cell[y][x] == undefined) &&(cell[key][x] != undefined)) {
										cell[y][x] = cell[key][x];
										cell[key][x] = undefined;
									}
								}
							}
						}
					}
					break;

				// Move left
				case 4:
					for(y = 0; y < rows; y++) {
						for(x = 0; x < columns; x++) {
							if(cell[y][x] != undefined) {
								for(key = x; key < columns; key++) {
									if((x != key) && (cell[y][key] != undefined) && (cell[y][x] == cell[y][key])) {
										cell[y][x] += cell[y][key];
										cell[y][key] = undefined;
										score += cell[y][x];
									}
								}
							}
						}
						for(x = 0; x < columns; x++) {
							if(cell[y][x] == undefined) {
								for(key = x; key < columns; key++) {
									if((cell[y][x] == undefined) && (cell[y][key] != undefined)) {
										cell[y][x] = cell[y][key];
										cell[y][key] = undefined;
									}
								}
							}
						}
					}
					break;
			}
			
			// Add-on new block
			addBlock(cell, defaultAddOnBlocks);
			
			// Repopulate grid cell
			populateGrid(cell);
			
			// Update score
			updateScore(score);

			// Check end state
			checkEndState(cell, defaultEndState, score);

			// Debug console
			//console.debug(JSON.stringify(cell));
		}
		
		// Repopulate grid cell
		populateGrid(cell);
		
		// Debug console
		//console.debug(JSON.stringify(cell));

		gameObject.newGame.navigateBlock = navigateBlock;
	}
	
	gameObject.newGame = newGame;
	return gameObject;
}

/*
* ----- *
* NOTES
* ----- *
*
* FUTURE ENHANCEMENTS:
* -------------------
* Dynamically control defaultRows, defaultColumns, defaultStartingBlocks, defaultAddOnBlocks and defaultEndState in UI.
* Able to save score in member record and display in leaderboard.
* Able to use keyboard arrow keys and mouse slide to navigate block.
*
* LIMITATIONS:
* -----------
* For loops in navigateBlock may not be efficient (and may be buggy).
*/