function game() {
	
	var gameObject = new Object();
	
	var defaultRows = 4; // Default row of grid
	var defaultColumns = 4; // Default column of grid
	var defaultStartingBlocks = 2; // Default number of blocks when starting new game
	var defaultAddOnBlocks = 1; // Default number of blocks to add on each move

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
	* Check grid array and populate grid cell value
	* @access private
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

		y = new Array();
		x = new Array();
		num = new Array();
		
		var cell = create2DArray();

		for(counter = 0; counter < defaultStartingBlocks; counter++) {
			y[counter] = generateRandom(defaultRows);
			x[counter] = generateRandom(defaultColumns);
			num[counter] = (generateRandom(2) + 1) * 2; // generateRandom(2) + 1 * 2 due to number must be 2 or 4
			while((y[counter] == y[counter-1]) && (x[counter] == x[counter-1])) {
				y[counter] = generateRandom(defaultRows);
				x[counter] = generateRandom(defaultColumns);
			}
			cell[y[counter]][x[counter]] = num[counter];
		}

		/*
		* Controller to navigate block
		* @access public
		* @param integer $direction - Direction to navigate blocks (1 = Move top, 2 = Move right, 3 = Move bottom, 4 = Move left)
		*/
		function navigateBlock(direction) {
			var rows = cell.length;
			var columns = cell[0].length;
			switch(direction) {
				case 1:
					for(x = 0; x < columns; x++) {
						for(y = 0; y < rows; y++) {
							
						}
					}
					break;
				case 2:
					for(y = 0; y < rows; y++) {
						for(x = columns -1; x >= 0; x--) {
							
						}
					}
					break;
				case 3: 
					for(x = 0; x < columns; x++) {
						for(y = rows - 1; y >= 0; y--) {
							
						}
					}
					break;
				case 4: 
					for(y = 0; y < rows; y++) {
						for(x = 0; x < columns; x++) {
							
						}
					}
					break;
			}
			console.debug(JSON.stringify(cell));
		}
		populateGrid(cell);
		gameObject.newGame.navigateBlock = navigateBlock;
		//console.debug(JSON.stringify(cell));
	}

	gameObject.newGame = newGame;
	return gameObject;
}

/*
* Notes:
* -----
* Dynamically generate grid table based on number of rows and columns.
* Dynamically control defaultRows, defaultColumns, defaultStartingBlocks and defaultAddOnBlocks.
* While loop when generating cell row and column will not work if defaultStartingBlocks is more than 2.
* For loops in navigateBlock may not be efficient.
*/