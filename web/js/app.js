console.log("app.js bien chargé");

$("#voteBtn").on("click", function(e){
	e.preventDefault();

	var url = $(this).attr("href");

	$.ajax({
		"url": url,
		"success": function(response){
			console.log(response);
			$(".container").append(response);
		}
	});
});