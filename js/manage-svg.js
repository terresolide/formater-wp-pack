/**
 * 
 * @author epointal
 */
function formater_switch_svg( node, value ){
    if( value ){
        var classname = "fm-right";
        var float = "right";
    }else{
        var classname = "fm-left";
        var float = "left";
    }
  
    node.parentNode.style.float = float;
	if(node.parentNode.className.indexOf( classname )>=0){
		node.parentNode.className = node.parentNode.className.replace(classname, "");
	}else{
		node.parentNode.className = node.parentNode.className + " " + classname;
	}
}