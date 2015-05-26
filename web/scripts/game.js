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
				if(cell[y][x] != null) {
					document.getElementById(y+'-'+x).innerHTML = cell[y][x];
					document.getElementById(y+'-'+x).style.backgroundColor = '#FFFFFF';
				} else {
					document.getElementById(y+'-'+x).innerHTML = '';
					document.getElementById(y+'-'+x).style.backgroundColor = '';
				}
			}
		}
	}

	/*
	* Create new game by populating two cells with 2 or 4
	* @access public
	* @param integer $blocks - Starting blocks to generate
	*/
	function newGame() {

		// Create new grid matrix array
		var cell = create2DArray();

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
* Dynamically generate grid table based on number of rows and columns in UI.
* Dynamically control defaultRows, defaultColumns, defaultStartingBlocks, defaultAddOnBlocks and defaultEndState in UI.
* Able to save score in member record and display best score.
* Able to use keyboard arrow keys to navigate block.
* Cell color changes according to number.
*
* LIMITATIONS:
* -----------
* Error when executing navigateBlock() without executing newGame().
* For loops in navigateBlock may not be efficient.
*/