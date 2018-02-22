/**
 * 
 */
$(function() {
	var id = 0;

	$('.divMain').css('height',
			$(window).height() - $('#navbar').height() + 'px');
	$('#contenu').css('height',
			$(window).height() - $('#navbar').height() + 'px');

	// à chaque redimensionnement de la fenêtre du navigateur
	$(window).resize(function() {
		$('.divMain').css('height', $(window).height() + 'px');
		$('#contenu').css('height',
		$(window).height() - $('#navbar').height() + 'px');
	});

	// $('#test').css("width", "50%").css("height",
	// "100px").css("background-image",
	// "url('uploads/img/donnees_perso_250x250.jpg')")
	// .css("background-repeat", "no-repeat").css("background-size",
	// "contain").css("background-position", "center")
	// .css("position", "absolute").css("border", "1px solid");
	// $('#test').draggable({
	// containment: "#contenu"
	// });
	// $('#test').resizable({
	// containment: "#contenu"
	// });

	$("#drag").on('mouseover', function() {
		$(this).css("border", "1px solid");
	});
	$("#drag").on('mouseout', function() {
		$(this).css("border", "");
	});

	$('#draggable').draggable({
		revert : "invalid", // when not dropped, the item will revert back to its initial position
		helper : "clone",
		cursor : "move"
	});

	$('#drag').draggable({
		containment : "#contenu"
	});
	$('#drag').resizable({
		containment : "#contenu"
	});

	$('#addDiv').on('click', function() {
		$('#contenu').append('<div id="drag1" style="position: absolute; width:150px; height:150px; border: 1px solid;">');
		$('#drag1').draggable({
			containment : "#contenu"
		});
		$('#drag1').resizable({
			containment : "#contenu"
		});
	});

	$('#addTextarea').on('click', function() {
		$('#contenu').append('<div id="divTextarea" style="position: absolute; width:150px; height:150px; border: 1px solid;"><textarea id="textarea" rows="2" cols="10"></div>');
		$('#divTextarea').resizable({
			containment : "parent"
		});
		$('#divTextarea').draggable({
			containment : "parent"
		});
		$("#textarea").css("color", "red");
	});
	
	$('#addTexte').on('click', function() {
		id = id + 1;
		$('#contenu').append('<div id="divTexte' + id +'" style="position: absolute; width:150px; height:150px; border: 1px solid;"></div>');
		$('#divTexte'+ id).html($('#editeur').html());
		$('#divTexte'+ id).resizable({
			containment : "parent"
		});
		$('#divTexte'+ id).draggable({
			containment : "parent"
		});
	});
	
	$('#btnHTML').on('click', function(){
		alert($('#contenu').html());
	});

	// $('#addTexte').on('click', function(){
	// $('#drag').append('<textarea id="textarea1" rows="2" cols="20">');
	// $('#textarea1').resizable({
	// containment: "parent"
	// });
	// $('#textarea1').draggable({
	// containment: "parent"
	// });
	// });

	$("#addImage").on('click', function() {
		$('#file').trigger('click');
	});

	// Function to preview image after validation
	$("#file").change(function() {
		var file = this.files[0];
		var imagefile = file.type;
		var match = [ "image/jpeg", "image/png", "image/jpg" ];
		if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2]))) {
			$('#droite').append('<div id="alertMain"></div>');
			$("#alertMain").html('<div class="alert alert-danger">Please Select A valid Image File. Only jpeg, jpg and png Images type allowed</div>');
			$("#alertMain").hide().fadeIn("slow").delay(10000).fadeOut('400', function() {
				$('#alertMain').remove();
			});
			return false;
		} else {
			var reader = new FileReader();
			reader.onload = imageIsLoaded;
			reader.readAsDataURL(this.files[0]);
		}
	});

	function imageIsLoaded(e) {
		id = id + 1;
		$('#contenu').append('<div id="image_preview' + id	+ '" style="position:absolute;"></div>');
		// $('#previewing').attr('src', e.target.result)';
		$('#image_preview' + id).css("width", "50%").css("height", "100px")
				.css("background-image", "url('" + e.target.result + "')").css("background-repeat", "no-repeat")
				.css("background-position", "center").css("background-size", "contain")
				.css("position", "absolute").css("border", "1px solid");
		$('#image_preview' + id).draggable({
			containment : "#contenu"
		});
		$('#image_preview' + id).resizable({
			containment : "#contenu"
		});

		$("*").on('click', clickSurUnElement);

		function clickSurUnElement(e) {
			e.stopPropagation();
			$('#info').text("Balise = " + this.tagName + " id = " + this.id);
		};

	};

	// $('#contenu').droppable({
	// drop: function(){
	// id = id+1;
	// $(this).find('p').remove();
	// $(this).append(
	// '<div id="modeleImageTexte">'
	// +'<div id="divImage">'
	// +'<p><form id="formUpload" action="upload.php" method="get"
	// enctype="multipart/form-data">'
	// +'Insérer une image : <input type="file" id="file" name="file">'
	// +'<input type="submit" value="valider" name="submit">'
	// +'</form></p>'
	// +'</div>'
	// +'<div id="divTexte">'
	// +'<p>Taper du texte</p>'
	// +'</div>'
	// +'</div>'
	// );

	// //upload image
	// $('#formUpload').on('submit', function(e){
	// e.preventDefault(); // On empêche le navigateur de soumettre le formulaire
	// $('#divImage').html('<p><img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif"></p>');

	// var formdata = (window.FormData) ? new FormData($(this)[0]) : null;
	// var data = (formdata !== null) ? formdata : $(this).serialize();
	// alert(data);

	// // ajax
	// $.ajax({
		// type: 'POST', // Type of request to be send, called as method
		// url: 'upload.php', // Url to which the request is send
		// contentType: false, // obligatoire pour de l'upload
		// processData: false, // obligatoire pour de l'upload
		// data: data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
		// timeout: 3000,
		// success: function(data) {
			// $('#droite').append('<div id="alertMain"></div>');
			// $('#alertMain').append(data).show("slow").delay(10000).hide('400', function(){
				// $('#alertMain').remove();
			// });
		// },
		// error: function() {
			// $('#droite').append('<div id="alertMain"></div>');
			// $('#alertMain').append('la requête n\'a pas abouti').show("slow").delay(10000).hide('400', function(){
				// $('#alertMain').remove();
			// });
		// }
	// });
	// });

	// // Function to preview image after validation
	// $("#file").change(function() {
	// $("#message").empty(); // To remove the previous error message
	// var file = this.files[0];
	// var imagefile = file.type;
	// var match= ["image/jpeg","image/png","image/jpg"];
	// if(!((imagefile==match[0]) || (imagefile==match[1]) ||
	// (imagefile==match[2]))) {
	// $('#droite').append('<div id="alertMain"></div>');
	// $("#alertMain").html('<div class="alert alert-danger">Please Select A
	// valid Image File. Only jpeg, jpg and png Images type allowed</div>')
	// .show("slow").delay(10000).hide('400', function(){
	// $('#alertMain').remove();
	// });
	// return false;
	// } else {
	// var reader = new FileReader();
	// reader.onload = imageIsLoaded;
	// reader.readAsDataURL(this.files[0]);
	// }
	// });

	// function imageIsLoaded(e) {
	// $('#divImage').html('<div id="image_preview"></div>');
	// // $('#image_preview').css("margin", "10%").css("width", "80%");
	// // $('#previewing').attr('src', e.target.result);
	// $('#image_preview').css("width", "100%").css("height",
	// "auto").css("background-image", "url('"+e.target.result+"')")
	// .css("background-repeat", "no-repeat").css("background-size",
	// "contain").css("background-position", "center");

	// };

	// }
	// });

	// exemple issu du 3.3-TP_Chat
	$('#envoyer').click(function() {
		var nom = $('#nom').val();
		var message = $('#message').val();
		$.post('3.3-chat.php', {
			'nom' : nom,
			'message' : message
		}, afficheConversation);
	});
	
	// éditeur
	$("#btnResultat").on('click', function() {
		$("#resultat").val($("#editeur").html());
		$("#info").html($('#editeur').html());
	});
	
	$(".wysiwyg").on('click change', function(){
		if(this.id == "police"){
			commande('fontName', $(this).val());
		}
		else {
			commande($(this).val());
		}
	});
	
	
	$("#wColorPicker").wColorPicker({
	    color: '#FF00FF',
	    mode:'hover',
	    effect:'none',
	    onSelect: function(color){ commande('foreColor', color); },
	    onMouseover: function(color){ commande('foreColor', color); },
	    onMouseout: function(color){ commande('foreColor', color); }
	});
	
});

function commande(nom, argument) {
	if (typeof argument === 'undefined') {
		argument = '';
	}
	switch (nom) {
	case "createLink":
		argument = prompt("Quelle est l'adresse du lien ?");
		break;
	case "insertImage":
		argument = prompt("Quelle est l'adresse de l'image ?");
		break;
	}
	// Exécuter la commande
	document.execCommand(nom, false, argument);
}

