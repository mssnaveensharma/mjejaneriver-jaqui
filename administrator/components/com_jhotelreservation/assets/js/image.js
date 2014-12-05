/*
  *  George Bara (www.cmsjunkie.com)
  *
 */
 
this.previewImage = function(){	
		
	var xOffset = 31;
	var yOffset = 65;
	
	
	if(window.navigator.userAgent.indexOf ( "MSIE " ) > 0 ){
		//yOffset = 20;
	}
    

	$current_body=jQuery("body");
	$current_body.append("<p id='preview'><img src='"+ this.href +"' alt='Image preview' /></p>");								 
	$preview = jQuery("#preview");

	$container = jQuery("a.preview");
	var height =0;
	var pos;
	jQuery("a.preview").hover(function(e){
		/*
		console.log("This offset:" + this.offsetLeft +" " + this.offsetTop);
		console.log("Height:"+e.target.naturalHeight);
		console.log("Height:"+e.target);
		console.log("Container top:"+ $container.position().top +" left:" + $container.position().left);*/
		pos = getElementAbsolutePos(this);
		//console.log("Element's left: " + pos.x + " and top: " + pos.y);
		
		var img = new Image( );
		img.src = this.href;
		height = img.height ;
		
	    $preview.html("<img src='"+ this.href +"' alt='Image preview' />");								 

		$preview
			.css("top",(pos.y - height - yOffset) + "px")
			.css("left",(pos.x - xOffset) + "px")
			.fadeIn("fast");						
    },
	function(){
		//this.title = this.t;	
		$preview.hide();
    });	
	
	
	jQuery("a.preview").mousemove(function(e){
		pos = getElementAbsolutePos(this);
		$preview
			.css("top",(pos.y - height - yOffset) + "px")
			.css("left",(pos.x - xOffset) + "px");
		$preview.css("display","block");
		$preview.css("z-index","10");

	});			
	
	//returns the absolute position of some element within document
	function getElementAbsolutePos(element) {
		var res = new Object();
		res.x = 0; res.y = 0;
		if (element !== null) { 
			if (element.getBoundingClientRect) {
				var viewportElement = document.documentElement;  
				var box = element.getBoundingClientRect();
				var scrollLeft = viewportElement.scrollLeft;
				var scrollTop = viewportElement.scrollTop+ document.body.scrollTop; 

				res.x = box.left + scrollLeft;
				res.y = box.top + scrollTop;

			}
			else { //for old browsers
				res.x = element.offsetLeft;
				res.y = element.offsetTop;

				var parentNode = element.parentNode;
				var borderWidth = null;

				while (offsetParent != null) {
					res.x += offsetParent.offsetLeft;
					res.y += offsetParent.offsetTop;
					
					var parentTagName = 
						offsetParent.tagName.toLowerCase();	

					if ((__isIEOld && parentTagName != "table") || 
						((__isFireFoxNew || __isChrome) && 
							parentTagName == "td")) {		    
						borderWidth = kGetBorderWidth
								(offsetParent);
						res.x += borderWidth.left;
						res.y += borderWidth.top;
					}
					
					if (offsetParent != document.body && 
					offsetParent != document.documentElement) {
						res.x -= offsetParent.scrollLeft;
						res.y -= offsetParent.scrollTop;
					}


					//next lines are necessary to fix the problem 
					//with offsetParent
					if (!__isIE && !__isOperaOld || __isIENew) {
						while (offsetParent != parentNode && 
							parentNode !== null) {
							res.x -= parentNode.scrollLeft;
							res.y -= parentNode.scrollTop;
							if (__isFireFoxOld || __isWebKit) 
							{
								borderWidth = 
								 kGetBorderWidth(parentNode);
								res.x += borderWidth.left;
								res.y += borderWidth.top;
							}
							parentNode = parentNode.parentNode;
						}    
					}

					parentNode = offsetParent.parentNode;
					offsetParent = offsetParent.offsetParent;
				}
			}
		}
		return res;
	}
};


// starting the script on page load
jQuery(document).ready(function(){
	previewImage();
});

