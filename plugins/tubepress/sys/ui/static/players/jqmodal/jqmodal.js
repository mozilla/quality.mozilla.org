var TubePressJqModalPlayer=(function(){var c=TubePressEvents,b="jqmodal",f=jQuery(document),e=getTubePressBaseUrl()+"/sys/ui/static/players/jqmodal/lib/jqModal.",a=function(m,l,j,i,g){var h=jQuery('<div id="jqmodal'+j+l+'" style="visibility: none; height: '+g+"px; width: "+i+'px;"></div>').appendTo("body"),k=function(n){n.o.remove();n.w.remove()};h.addClass("jqmWindow");h.jqm({onHide:k}).jqmShow()},d=function(l,m,h,g,i,k,j){jQuery("#jqmodal"+j+k).html(h)};jQuery.getScript(e+"js",function(){},true);TubePressCss.load(e+"css");f.bind(c.PLAYER_INVOKE+b,a);f.bind(c.PLAYER_POPULATE+b,d)}());