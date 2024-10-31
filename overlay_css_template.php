/* the overlayed element */
.apple_overlay {
	
	/* initially overlay is hidden */
	display:none;
	
	/* growing background image or use background-color */    
	##THEME##
	
	/* 
		width after the growing animation finishes
		height is automatically calculated
	*/
	width:##WIDTH##px;		
	
	/* some padding to layout nested elements nicely  */
	padding:35px;

	/* a little styling */	
	font-size:11px;
	text-align:center;
}

/* default close button positioned on upper right corner */
.apple_overlay .close {
	background-image:url(../images/apple-close.png);
	position:absolute; right:5px; top:5px;
	cursor:pointer;
	height:28px;
	width:28px;
}