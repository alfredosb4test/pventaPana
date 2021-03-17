var $drop_activo=0;
$(document).ready(function(e)
{
	altura=$(window).height();
	$("#cont_productos").css("height",altura-105);
	$(".draggable").draggable({
		helper:"clone",
		cursor:"move",
		revert:!0,
		opacity:.9,
		revertDuration:200,
		drag:function(){}
  	});
	$drop_activo=0;
	$(".droppable").droppable({
		greedy:false,
		activeClass:"",
		hoverClass:"f_verde_degradado",
		cursor:"crosshair",
		drop:function(event, ui){
			$drop_activo=1,
			$codigo=$.trim(ui.draggable.html()),
			$(this).html($codigo),
			$(this).attr("code",$codigo),
			$(this).addClass("f_azul_degradado2 t_blanco hand"),
			$(this).draggable({
				helper:"clone",
				cursor:"move",
				revert:true,
				opacity:.9,
				revertDuration:200
			})
		}
	})
	$("#pdf_code_bar").click(function(){
		day=new Date;
		id=day.getTime();
		var $array_cod=[];
		
		if($drop_activo){
			
			$(".droppable").each(function(){
				$id=$(this).attr("id");
				$code=$(this).attr("code");
				prod1=new Codigo($code);
				$array_cod.push(prod1)
			})
			var $codeJSON=JSON.stringify($array_cod);
			day=new Date;
			id=day.getTime();
			URL="bar_code_prod_varios.php?codigos="+$codeJSON;
			eval("page"+id+" = window.open(URL, '"+id+"','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=1,resizable=1,width=700,height=600');")
		}
	});
});
function Codigo(code){this.code=code}