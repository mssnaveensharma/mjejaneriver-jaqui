/*------------------------------------------------------------------------
# plg_thumbgallery - Thumb Gallery Plugin
# ------------------------------------------------------------------------
# author    Jesús Vargas Garita
# copyright Copyright (C) 2010 joomlahill.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlahill.com
# Technical Support:  Forum - http://www.joomlahill.com/forum
-------------------------------------------------------------------------*/

(function () {
	var oldonload = window.onload;
	window.onload = function(){
		var holders = document.querySelectorAll('.tg_holder');
		for (var i=0; i<holders.length; i++) {
			var main = holders[i].querySelector('.tg_main');
			var thumbs = holders[i].querySelector('.tg_thumbs');
			var cWidth = holders[i].parentNode.clientWidth;
			var gWidth = holders[i].getAttribute('data-width');
			if(gWidth<cWidth) {
				holders[i].style.width = gWidth*100/cWidth + '%';
			}
			var mWidth = main.getAttribute('data-width'); 
			var tWidth = thumbs.getAttribute('data-width'); 
			main.style.width = mWidth*100/gWidth + '%';
			thumbs.style.width = tWidth*100/gWidth + '%';
			
			if(window.getComputedStyle(thumbs,null).getPropertyValue("margin-left")) {
				thumbs.style.marginLeft = parseInt(window.getComputedStyle(thumbs,null).getPropertyValue("margin-left"))*100/gWidth + '%';
			}
			
			var imgs = thumbs.querySelectorAll('img');
			var iWidth = imgs[0].getAttribute('width');
			for (var i=0; i<imgs.length; i++) {
				imgs[i].style.width = iWidth*100/tWidth + '%';
				if(window.getComputedStyle(imgs[i],null).getPropertyValue("margin-left")) {
					imgs[i].style.marginLeft = parseInt(window.getComputedStyle(imgs[i],null).getPropertyValue("margin-left"))*100/tWidth + '%';
				}
			}		
		}
		var slidepages = document.querySelectorAll('.slidepage');
		for (var i=0; i<slidepages.length; i++) {
			var pages = slidepages[i].querySelector('.pages');
			var ul = pages.querySelector('ul');
			var li = ul.querySelector('li');
			var width = li.offsetWidth + parseInt(window.getComputedStyle(li,null).getPropertyValue("margin-left")) + parseInt(window.getComputedStyle(li,null).getPropertyValue("margin-right"));
			var data = pages.getAttribute('data-pages').split(",");
			pages.style.width = width*data[1]+'px';
			ul.style.width = width*data[0]+'px';
		}
		var thumbslides = document.querySelectorAll('.thumbslide');
		for (var i=0; i<thumbslides.length; i++) {
			var images = thumbslides[i].querySelectorAll('img');
			var urls = new Array();
			for(var j=0;j<images.length-1;j++) {
				urls[j] = images[j].getAttribute('data-fullimage');
			}
			tg_loadImages(urls);
		}
		if(oldonload){oldonload()}};
	}()
);

function tg_thumbJump(el,to,nav,visible) {
	var container = el.parentNode;
	var id = container.getAttribute('id').substring(3,6);
	var ul = document.getElementById('ul_'+id); 
	var lis  = ul.querySelectorAll('li');
	var width = lis[0].offsetWidth + parseInt(window.getComputedStyle(lis[0],null).getPropertyValue("margin-left")) + parseInt(window.getComputedStyle(lis[0],null).getPropertyValue("margin-right"));
	var active = tg_getElementData(container.querySelector('li.activepagenumber'));
	var leftEdge = tg_getElementData(container.querySelector('li.edge-l'));
	var rightEdge = tg_getElementData(container.querySelector('li.edge-r'));
	if(to==0) to+=active['num']+nav;
	if (to!=0&&to<=lis.length&&to!=active['num']) {
		var dest = tg_getElementData(document.getElementById('pn_'+id+'_'+to));
		var left = parseInt(window.getComputedStyle(ul,null).getPropertyValue("left"));
		var newLeft = left-(width*nav)+'px';
		//ul.set('tween', {duration: 100});
		if(nav==+1 && active['el']==rightEdge['el']) {
			ul.style.left = newLeft;	
			tg_removeEdges(leftEdge['el'],active['el']);
			tg_setEdges(document.getElementById('pn_'+id+'_'+(leftEdge['num']+1)),dest['el']);
		} else if (nav==-1 && active['el']==leftEdge['el']) {
			ul.style.left = newLeft;
			tg_removeEdges(active['el'],rightEdge['el']);
			tg_setEdges(dest['el'],document.getElementById('pn_'+id+'_'+(rightEdge['num']-1)));
		} else if (nav==0) {
		    if (dest['el'].classList.contains('first')) {
				tg_removeEdges(leftEdge['el'],rightEdge['el']);
				ul.style.left = 0;
				tg_setEdges(dest['el'],document.getElementById('pn_'+id+'_'+visible));
			} else if (dest['el'].classList.contains('last')) {
				tg_removeEdges(leftEdge['el'],rightEdge['el']);
				ul.style.left = -((lis.length-visible)*width)+'px';
				tg_setEdges(document.getElementById('pn_'+id+'_'+(dest['num']-visible+1)),dest['el']);	
			}
		}
		for(var k=0;k<lis.length; k++) {
			lis[k].classList.remove('activepagenumber');	
		}
		dest['el'].classList.add('activepagenumber');
		var thumbslides = document.querySelectorAll('.ts_'+id);
		for (var t=0; t<thumbslides.length; t++) {
			thumbslides[t].style.display = 'none';
			thumbslides[t].style.opacity = 0;
		}		
		document.getElementById('sl_'+id+'_'+to).style.display = 'table';
		document.getElementById('sl_'+id+'_'+to).style.opacity = 100;
	}
}
function tg_showImage(img) {
	var id = img.getAttribute('class').substring(0,3);
	var oldimg = document.getElementById('tg_main_'+id).querySelector('img');
	oldimg.style.opacity = 0;
	setTimeout(function () {
		oldimg.src = img.getAttribute('data-fullimage');
		oldimg.style.opacity = 100;
	}, 200);
}
function tg_getElementData (el){
	if (typeof el != 'undefined') {
		data = new Array();
		data['el'] = el;
		data['id'] = el.getAttribute('id');	
		data['num'] = parseInt(data['id'].substring(7,8));	
		return data;
	}
}
function tg_removeEdges(l,r) {
		l.classList.remove('edge-l');
		r.classList.remove('edge-r');		
}
function tg_setEdges(l,r) {
		l.classList.add('edge-l');
		r.classList.add('edge-r');		
}
function tg_loadImages(arr){
    var newimages=[]
    var arr=(typeof arr!="object")? [arr] : arr //force arr parameter to always be an array
    for (var i=0; i<arr.length; i++){
        newimages[i]=new Image()
        newimages[i].src=arr[i]
    }
}