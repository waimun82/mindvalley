function game() {
	
	var gameObject = new Object();
	var defaultRows = 4;
	var defaultColumns = 4;
	var defaultBlocks = 2;
	var cell = create2DArray();

	/*
	* Generate random number from 1 onwards
	* @access private
	* @param integer $limit - Limit of the random number
	*/
	function generateRandom(limit) {
		return Math.floor((Math.random() * limit));
	}
	
	/*
	* Clear all grid cells
	* @access private
	*/
	function clearGrid() {
		for(y = 0; y < defaultRows; y++) {
			for(x = 0; x < defaultColumns; x++) {
				document.getElementById(y+'-'+x).innerHTML = '';
				document.getElementById(y+'-'+x).style.backgroundColor = '';
			}
		}
	}
	
	/*
	* Populate grid cell with a value
	* @access private
	* @param integer $y - Row number
	* @param integer $x - Column number
	* @param integer $value - Value to populate
	*/
	function populateGrid(y, x, value) {
		document.getElementById(y+'-'+x).innerHTML = value;
		document.getElementById(y+'-'+x).style.backgroundColor = '#FFFFFF';
	}

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
	* Assign value to variable
	* @access private
	* @param integer $y - Row key
	* @param integer $x - Column key
	* @param integer $value - Variable value
	*/
	function assignVariable(y, x, value) {
		cell[y][x] = value;
	}

	/*
	* Create new game by populating two cells with 2 or 4
	* @access public
	* @param integer $blocks - Starting blocks to generate
	*/
	function newGame(blocks) {
		clearGrid();
		
		blocks = typeof blocks !== 'undefined' ? blocks : defaultBlocks;
		y = new Array();
		x = new Array();
		num = new Array();

		for(counter = 0; counter < blocks; counter ++) {
			y[counter] = generateRandom(4);
			x[counter] = generateRandom(4);
			num[counter] = (generateRandom(2) + 1) * 2;
			while((y[counter] == y[counter-1]) && (x[counter] == x[counter-1])) {
				y[counter] = generateRandom(4);
				x[counter] = generateRandom(4);
			}
			populateGrid(y[counter], x[counter], num[counter]);
			assignVariable(y[counter], x[counter], num[counter]);
		}
		console.debug(cell);
	}

	/*
	* Controller to navigate block
	* @access public
	* @param integer $direction - Direction to navigate blocks (1 = Move top, 2 = Move right, 3 = Move bottom, 4 = Move left)
	*/
	function navigateBlock(direction) {
		
	}

	gameObject.newGame = newGame;
	gameObject.navigateBlock = navigateBlock;
	return gameObject;
}