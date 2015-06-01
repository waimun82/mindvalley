function game() {
	
	var gameObject = new Object();

	// @access public
	var defaultRows = 4; // Default row of grid
	var defaultColumns = 4; // Default column of grid
	var defaultStartingBlocks = 2; // Default number of starting blocks
	var defaultAddOnBlocks = 1; // Default number of blocks to add on each move
	var defaultEndState = 2048; // Default end state of the game

	/*
	* Generate random number from 0 onwards
	* @access private
	* @param integer $limit - Limit of the random number
	*/
	function generateRandom(limit) {
		return Math.floor((Math.random() * limit));
	}

	/*
	* Create 2-dimensional arrays
	* @access private
	*/
	function create2DArray(rows, columns) {
		var array = new Array(rows);
		for (counter = 0; counter < rows; counter++) {
			array[counter] = new Array(columns);
		}
		//var array = [[,,,,],[,,,,],[,,,,],[,,,,]]; // For debugging
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
		var targetOffset = $('#game-control').offset();
		$(window).scrollTop(targetOffset.top);
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
		rows = cell.length;
		columns = cell[0].length;
		for(key = 0; key < quantity; key++) {
			y[key] = generateRandom(rows);
			x[key] = generateRandom(columns);
			num[key] = (generateRandom(2) + 1) * 2;
			while(cell[y[key]][x[key]] != undefined) {
				y[key] = generateRandom(rows);
				x[key] = generateRandom(columns);
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
						case 4: color = '#FFE8CF'; break;
						case 8: color = '#FFAB84'; break;
						case 16: color = '#FCFF89'; break;
						case 32: color = '#4EFFD9'; break;
						case 64: color = '#4ECEFF'; break;
						case 128: color = '#5242FF'; break;
						case 256: color = '#F742FF'; break;
						case 512: color = '#FF20D7'; break;
						case 1024: color = '#FF2072'; break;
						default:
						case 2048: color = '#FF0000'; break;
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
	* @param float $score - End state score
	*/
	function checkEndState(cell, condition, score, member_id) {
		var win = false;
		var cont = false;
		for(y = 0; y < cell.length; y++) {
			for(x = 0; x < cell[y].length; x++) {
				if(cell[y][x] == condition) {
					postScore(member_id, score);
				}
			}
		}
	}

	/*
	* Insert points record via AJAX call
	* @access private
	* @param integer $member_id - 
	* @param float $points - Points obtained
	* @alert HTTP response text if successful
	*/	
	function postScore(member_id, score) {
		document.getElementById("button_top").disabled = true;
		document.getElementById("button_right").disabled = true; 
		document.getElementById("button_bottom").disabled = true; 
		document.getElementById("button_left").disabled = true;

		var url = 'ajax.updateMemberPoints.php';
		var parameters = "id="+member_id+"&points="+score;
		var http = new XMLHttpRequest();
		
		http.open("POST", url, true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(parameters);
		http.onload = function() {
			alert(http.responseText);
		}
	}	

	/*
	* Update score in HTML
	* @access private
	* @param float $score - Score to update in HTML
	*/
	function updateScore(score) {
		document.getElementById('score').innerHTML = score;
	}

	/*
	* Create new game by populating two cells with 2 or 4
	* @access public
	* @param integer $blocks - Starting blocks to generate
	*/
	function newGame(member_id) {

		var member_id = member_id;

		// Get default variables
		var selectRows = document.getElementById('rows');
		var selectedRows = selectRows[selectRows.selectedIndex].value ? parseInt(selectRows[selectRows.selectedIndex].value) : defaultRows;

		var selectColumns = document.getElementById('columns');
		var selectedColumns = selectColumns[selectColumns.selectedIndex].value ? parseInt(selectColumns[selectColumns.selectedIndex].value) : defaultColumns;

		var selectStartingBlocks = document.getElementById('starting_blocks');
		var selectedStartingBlocks = selectStartingBlocks[selectStartingBlocks.selectedIndex].value ? parseInt(selectStartingBlocks[selectStartingBlocks.selectedIndex].value) : defaultStartingBlocks;

		var selectAddOnBlocks = document.getElementById('addon_blocks');
		var selectedAddOnBlocks = selectAddOnBlocks[selectAddOnBlocks.selectedIndex].value ? parseInt(selectAddOnBlocks[selectAddOnBlocks.selectedIndex].value) : defaultAddOnBlocks;

		var selectEndState = document.getElementById('end_state');
		var selectedEndState = selectEndState[selectEndState.selectedIndex].value ? parseInt(selectEndState[selectEndState.selectedIndex].value) : defaultEndState;

		// Starting score
		var score = 0;

		// Create new grid matrix array
		var cell = create2DArray(selectedRows, selectedColumns);

		// Create grid HTML
		createGrid(cell);

		// Create starting blocks
		addBlock(cell, selectedStartingBlocks);

		/*
		* Controller to navigate block
		* @access public
		* @param integer $direction - Direction to navigate blocks (1 = Move top, 2 = Move right, 3 = Move bottom, 4 = Move left)
		*/
		function navigateBlock(direction) {
			
			var rows = cell.length;
			var columns = cell[0].length;
			var move = false;
			
			switch(direction) {
				
				// Move top
				case 1:
					for(x = 0; x < columns; x++) {
						for(y = 0; y < rows; y++) {
							if(cell[y][x] != undefined) {
								for(key = y; key < rows; key++) {
									if((y != key) && (cell[key][x] != undefined)) {
										if(cell[y][x] == cell[key][x]) {
											cell[y][x] += cell[key][x];
											cell[key][x] = undefined;
											score += cell[y][x];
											move = true;
										}
										key = rows;
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
										move = true;
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
									if((x != key) && (cell[y][key] != undefined)) {
										if(cell[y][x] == cell[y][key]) {
											cell[y][x] += cell[y][key];
											cell[y][key] = undefined;
											score += cell[y][x];
											move = true;
										}
										key = 0;
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
										move = true;
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
									if((y != key) && (cell[key][x] != undefined)) {
										if(cell[y][x] == cell[key][x]) {
											cell[y][x] += cell[key][x];
											cell[key][x] = undefined;
											score += cell[y][x];
											move = true;
										}
										key = 0;
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
										move = true;
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
									if((x != key) && (cell[y][key] != undefined)) {
										if(cell[y][x] == cell[y][key]) {
											cell[y][x] += cell[y][key];
											cell[y][key] = undefined;
											score += cell[y][x];
											move = true;
										}
										key = columns;
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
										move = true;
									}
								}
							}
						}
					}
					break;
			}
			
			// Add-on new block
			if(move == true) {
				addBlock(cell, selectedAddOnBlocks);
			}
			
			// Repopulate grid cell
			populateGrid(cell);
			
			// Update score
			updateScore(score);

			// Check end state
			checkEndState(cell, selectedEndState, score, member_id);

			// Debug console
			console.debug(JSON.stringify(cell));
		}
		
		// Repopulate grid cell
		populateGrid(cell);
		
		// Debug console
		console.debug(JSON.stringify(cell));

		gameObject.newGame.navigateBlock = navigateBlock;
	}
	
	gameObject.newGame = newGame;
	return gameObject;
}

// Window event to register keyboard input
document.onkeydown = function(evt) {
    event = evt || window.event;
    switch (event.keyCode) {
        case 87: Game.newGame.navigateBlock(1); break;
        case 68: Game.newGame.navigateBlock(2); break;
		case 83: Game.newGame.navigateBlock(3); break;
		case 65: Game.newGame.navigateBlock(4); break;
    }
};

/*
* ----- *
* NOTES
* ----- *
*
* LIMITATIONS:
* -----------
* For loops in navigateBlock() function may not be efficient.
*/