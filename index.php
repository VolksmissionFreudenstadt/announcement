<html>
	<head>
		<title>Bekanntmachungen erstellen</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.4/themes/humanity/jquery-ui.css" />
		 <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		 <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	</head>
	<body>
		<form id="setup" action="build.php" method="post" class="ui-widget">
			<h1>Bekanntmachungen erstellen</h1>
			<fieldset class="ui-widget-content">
				<legend>Abschnitte</legend>
				<input name="pre" type="checkbox" checked value="1" /> Einleitende Folien<br /> 
				<input name="events" type="checkbox" checked value="1" /> Veranstaltungen aus dem kOOL-Kalender<br /> 
				<input name="post" type="checkbox" checked value="1" /> Abschließende Folien<br />
			</fieldset>
			<input type="submit" name="submit" value="Erstellen" />
			<?php  if ($_GET['debug']) { ?><input type="hidden" name="debug" value="1" /><?php } ?>  
		</form>
	</body>
	
<script>

$.widget("ui.form",{
 _init:function(){
	 var object = this;
	 var form = this.element;
	 var inputs = form.find("input , select ,textarea");

         form.find("fieldset").addClass("ui-widget-content");
	 form.find("legend").addClass("ui-widget-header ui-corner-all");
	 form.addClass("ui-widget");

	 $.each(inputs,function(){
		$(this).addClass('ui-state-default ui-corner-all');
		$(this).wrap(<label />");

		if($(this).is(":reset ,:submit"))
		object.buttons(this);
		else if($(this).is(":checkbox"))
		object.checkboxes(this);
		else if($(this).is("input[type='text']")||$(this).is("textarea")||$(this).is("input[type='password']"))
		object.textelements(this);
		else if($(this).is(":radio"))
		object.radio(this);
		else if($(this).is("select"))
		object.selector(this);

		if($(this).hasClass("date"))
        		$(this).datepicker();
	});
	  var div = jQuery("&lt;div />",{
	 	   css:{
		   width:20,height:20,
		   margin:10,textAlign:"center"
			   }
		   }).addClass("ui-state-default drag");
	  var no = Math.ceil(Math.random() * 4);
	  var holder = jQuery("&lt;div />",{
				  id:'droppable',
				  text:"Drop the box with "+no+" here",
	                	   css:{
					 width:100,height:100,float:'right',fontWeight:'bold'}
					  }).addClass('ui-state-default');
	  $(form).find("fieldset").append(holder);
	  for(var i=1;i&lt;5;i++)
		{
		$(form).find("fieldset").append(div.clone().html(i).attr("id",i));
		}

	$(".drag").draggable({containment: 'parent'});
	 $("#droppable").droppable({
			accept:'#'+no,
			drop: function(event, ui) {
				$(this).addClass('ui-state-highlight').html("Right One");
				form.find(":submit").removeClass('ui-state-disabled').unbind('click');
				}
	});
	 $(".hover").hover(function(){
				  $(this).addClass("ui-state-hover");
				   },function(){
				  $(this).removeClass("ui-state-hover");
				   });

	 },
	 textelements:function(element){

			$(element).bind({

 			  focusin: function() {
 			   $(this).toggleClass('ui-state-focus');
 				 },
			   focusout: function() {
 			    $(this).toggleClass('ui-state-focus');
 				 }
			  });

			 },
	 buttons:function(element)
		 {
			if($(element).is(":submit"))
			{
			$(element).addClass("ui-priority-primary ui-corner-all ui-state-disabled hover");
			 $(element).bind("click",function(event)
			   {
				   event.preventDefault();
			   });
			}
			else if($(element).is(":reset"))
			$(element).addClass("ui-priority-secondary ui-corner-all hover");
			$(element).bind('mousedown mouseup', function() {
 			   $(this).toggleClass('ui-state-active');
 			 }

			  );
		 },
	 checkboxes:function(element){
		 $(element).parent("label").after("&lt;span />");
		 var parent =  $(element).parent("label").next();
		$(element).addClass("ui-helper-hidden");
		parent.css({width:16,height:16,display:"block"});
		parent.wrap("&lt;span class='ui-state-default ui-corner-all' style='display:inline-block;width:16px;height:16px;margin-right:5px;'/>");
		 parent.parent().addClass('hover');
		 parent.parent("span").click(function(event){
		 $(this).toggleClass("ui-state-active");
		 parent.toggleClass("ui-icon ui-icon-check");
		$(element).click();

		});

	 },
	 radio:function(element){
	        $(element).parent("label").after("<span />");
		var parent =  $(element).parent("label").next();
		$(element).addClass("ui-helper-hidden");
		parent.addClass("ui-icon ui-icon-radio-off");
		parent.wrap("&lt;span class='ui-state-default ui-corner-all' style='display:inline-block;width:16px;height:16px;margin-right:5px;'/>");
		parent.parent().addClass('hover');
		parent.parent("span").click(function(event){
				 $(this).toggleClass("ui-state-active");
				 parent.toggleClass("ui-icon-radio-off ui-icon-bullet");
				$(element).click();
				});
			 },
	  selector:function(element){
		var parent = $(element).parent();
		parent.css({"display":"block",width:140,height:21}).addClass("ui-state-default ui-corner-all");
		$(element).addClass("ui-helper-hidden");
		parent.append("&lt;span id='labeltext' style='float:left;'>&lt;/span>&lt;span style='float:right;display:inline-block' class='ui-icon ui-icon-triangle-1-s' >&lt;/span>");
		parent.after("&lt;ul class=' ui-helper-reset ui-widget-content ui-helper-hidden' style='position:absolute;z-index:50;width:140px;' >&lt;/ul>");
		 $.each($(element).find("option"),function(){
			  $(parent).next("ul").append("&lt;li class='hover'>"+$(this).html()+"&lt;/li>");
								   });
		 $(parent).next("ul").find("li").click(function(){ $("#labeltext").html($(this).html());
					$(element).val($(this).html());
										 });
		 $(parent).click(function(event){ $(this).next().slideToggle('fast');
									 event.preventDefault();
												});

				}

		 });



  $(function(){

$("#setup").form();

});
</script>

	
</html><?php

function monthSelect($name) {
	$currentMonth = strftime('%m');
	$selected = ($currentMonth % 2) ? $currentMonth+2 : $currentMonth+1;
	if ($selected>12) $selected -= 12; 
	$m = array('', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember', 'Januar');
	for ($no=2;$no<13;$no+=2) {
		if ($m[$no]) $o[] = '<option value="'.$no.'"'.($selected==$no ? ' selected' : '').'>'.$m[$no].'/'.$m[$no+1].'</option>';	
	}
	return '<select name="'.$name.'">'.join('', $o).'</select>';
}

