$(document).ready(function()	{
	$("#mxlxml").change(function()	{
		//Load file info
		var file = $(this).prop("files")[0];
		$("#progress").attr("value", "0");
		if(file)	{
			$("#name").html(file.name);
			$("#size").html(file.size * 0.0009765625 + "kB");
			$("#mime").html(file.type);
		}
		else	{
			$("#name").html("unknown");
			$("#size").html("unknown");
			$("#mime").html("unknown");
		}
	});
});
