/**
 * 
 */
$(function() {
	var id = 0;
	var laSelection = '';
	var modifierTexte = '';
	var uneSelection = '';
	var ajouterImage = '';
	var utilisateurLogin = '';
	var utilisateurId = '';
		
	// affiche progressivment le titre principal au chargement de la page
	$('header div h1').html('Envoyez vos newsletters gratuitement et facilement').fadeIn(1000);
	
	//hauteur de my_navbar en integer
	var hauteurMyNavbar = parseInt($('#my_navbar').css('height').replace("px", ""));
	
	$('.divMain').css('height',	$(window).height()-hauteurMyNavbar + 'px');
	$('#contenu').css('height',	$(window).height()-hauteurMyNavbar + 'px');
	$('header').css('width', $(window).width() + 'px').css('height', $(window).height()-hauteurMyNavbar + 'px');

	// à chaque redimensionnement de la fenêtre du navigateur
	$(window).resize(function() {
		$('.divMain').css('height', $(window).height()-hauteurMyNavbar + 'px');
		$('#contenu').css('height',	$(window).height()-hauteurMyNavbar + 'px');
		$('header').css('width', $(window).width() + 'px').css('height', $(window).height()-hauteurMyNavbar + 'px');
	});
	
	/*** Partie LOGIN ***/
	$('#impossible').on('click', function(){
		$('#impossibleContent').toggle(500);
	});
	$('#inscription').on('click', function(){
		$('#inscriptionContent').toggle(500);
	});
	$('#btnDeconnexion').on('click', function(){
		$('.newsletter').hide(500);
		$('.authentification').show(500);
		$.post('login.php', {
			'controller' : "deconnexion"
		}, function(data){
		});
	});
	//ajax ajout nouveau Utilisateur
	$("form#formInscription").submit('click', function(event){

		event.preventDefault(); //disable the default form submission
		
		var login = $('#userInscription').val();
		var password = $('#inscriptionMdp').val();
		var passwordConfirm = $('#inscriptionMdpConfirm').val();
		
		if(password != passwordConfirm){
			$('#inscriptionMdpConfirm-control')
			.html('Veuillez saisir deux mots de passe identiques.')
			.show("slow").delay(10000).hide("slow");
		} else {
			$('#resultatInscription').html('<div align=center><img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif"></div>').show("slow");
			$.post('login.php', {
				'controller' : "inscription",
				'login' : login,
				'password' : password
			}, function(data){
				$('#resultatInscription').html(data).show("slow").delay(10000).hide("slow");
			});
		}
	});
	//ajax mot de passe oublié ?
	$("form#formImpossible").submit('click', function(event){

		event.preventDefault(); //disable the default form submission
		
		var login = $('#userImpossible').val();
		
		$('#resultatImpossible').html('<div align=center><img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif"></div>').show("slow");
		$.post('login.php', {
			'controller' : "impossible",
			'login' : login
		}, function(data){
			$('#resultatImpossible').html(data).show("slow").delay(10000).hide("slow");
		});
	});
	//ajax Authentification Utilisateur
	$("form#formLogin").submit('click', function(event){

		event.preventDefault(); //disable the default form submission
		
		var login = $('#user').val();
		var password = $('#loginMdp').val();
		
		$('#resultatLogin').html('<div align=center><img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif"></div>').show("slow");
		$.post('login.php', {
			'controller' : "authentification",
			'login' : login,
			'password' : password
		}, function(data){
			if(data.message != ''){
				$('#resultatLogin').html(data.message).show("slow").delay(10000).hide("slow");
			} else {
				$('#resultatLogin').hide();
				$('#btnModalLoginClose').trigger('click'); // ferme le modal du login
				utilisateurLogin = data.login;
				utilisateurId = data.id;
				$('.authentification').hide(500);
				$('.newsletter').show(500);
				// récupération initiale de la liste des serveurs SMTP en bdd
				$.post('pdo.php', {
					'controller' : "init_smtp",
					'utilisateurId' : utilisateurId
				}, function(data){
					$('#fromContent').html(data);		
				});
				// récupération initiale des listes de diffusion en bdd
				$.post('pdo.php', {
					'controller' : "init_listediffusion",
					'utilisateurId' : utilisateurId
				}, function(data){
					$('#toContent').html(data);		
				});
			}						
		}, "json");
	});
	
	//ajoute un modele à la div contenu
	$('#contenu').on('click', '#addModele', function(){
		insertModel();
	});
	
	
	//Quand on clique sur la div #contenu
	$('#contenu').on('click', function(){
		laSelection = $(this);
		info(this);
	});
	//#info modification de l'alignement
	$('body').on('change', '#alignSelectInfo', function(){
		$(laSelection).attr('align', $(this).val());
		//alert($(uneSelection).attr('align'));
	});
	//#info modification de la couleur du background
	$('body').on('change', '#backgroundColor', function(){
		$(laSelection).css('background', $(this).val());
		//alert($(uneSelection).attr('align'));
	});
	//#info modification de la largeur
	$('body').on('change', '#widthInfo', function(){
		$(laSelection).attr('width', $(this).val());
	});
	//#info supprime la selection sauf la div contenu
	$('body').on('click', '#btnSupprimerInfo', function(){
		if($(laSelection).attr('id') != 'contenu'){
			$(laSelection).remove();
		}
	});
	//#info ajoute un saut de ligne avant la séléction
	$('body').on('click', '#btnAddLigneInfo', function(){
		$(laSelection).before('<br/>');
	});
	//#info ajout d'une ligne de texte à une table
	$('body').on('click', '#btnAddLigneTexteTable', function(){
		$(laSelection).append(
			'<tr>'+
				'<td align=center id="divTexte" width=600>'+
					'<p class="texte_insert">Ajouter du texte</p>'+
				'</td>'+
			'</tr>'
		);
	});
	//#info ajout d'une ligne d'image à une table
	$('body').on('click', '#btnAddLigneImageTable', function(){
		$(laSelection).append(
			'<tr>'+
				'<td align=center>'+
					'<button class="btn btn-warning btnAddImageModele" type="button"><span class="glyphicon glyphicon-picture"></span>Ajouter une image</button>'+
				'</td>'+
			'</tr>'
		);
	});
	
	
	// Quand on passe la souris sur une ligne du menu
	$('.divHover').on('mouseover', function(){
		var couleur = $(this).prev().css('background-color');
        $(this).animate({backgroundColor: couleur, color: 'white'}, "fast", "linear");
        //$('.option, .ui-icon', this).show();
        //alert($('#menuCss td:first-child', this).html());
        //alert($(this).prev().html());    
	});
	// Quand on sort la souris d'une ligne du menu
	$('.divHover').on('mouseleave', function(){
        $(this).animate({backgroundColor: 'transparent', color: $(this).prev().css('background-color')}, "fast", "linear");
	});
	// Quand on clique sur une ligne du menu
	$('.divHover').on('click', function(){
		//$(this).next().slideToggle("slow");
		var slideToggle = '#toggle' + $(this).attr('id');
		//$('.slideToggle', this).slideToggle("slow");
		$(slideToggle).css('background-color', $(this).css('background-color'));
		$(slideToggle).slideToggle("slow");
//		$(slideToggle).toggle(500);
	});
	
	$('#width').on('change', function(){
		//alert($(laSelection).get(0).tagName);
		if ($(laSelection).get(0).tagName == 'IMG'){
			$(laSelection).attr('width', $(this).val());
		} else {
			//$(laSelection).css('width', $(this).val()+'pt');
			$(laSelection).attr('width', $(this).val());
		}
	});
	
//	$('#btnFrom').on('click', function(){
//		//$('.menuDown-content').css('display', 'block');
//		$('#fromContent').toggle(500);
//	});
	
	$("#btnResultat").on('click', function() {
		$("#resultat").val($("#editeur").html());
		$("#info").html($('#editeur').html());
	});
	
	$(".wysiwyg").on('click change', function(){
		if(this.id == "police"){
			commande('fontName', $(this).val());
		}
		else if(this.id == "fontsize"){
			commande('fontSize', $(this).val());
		}
		else {
			commande($(this).val());
		}
	});
	
//	$("#wColorPicker").wColorPicker({mode:'hover', effect:'none'});
	$("#wColorPicker").wColorPicker({
	    color: '#FF00FF',
	    mode:'hover',
	    effect:'none',
	    onSelect: function(color){
//	        $("#editeur").css('color', color);	
	        commande('foreColor', color);
	    },
	    onMouseover: function(color){
//	    	$("#editeur").css('color', color);
	        commande('foreColor', color);
	    },
	    onMouseout: function(color){
//	    	$("#editeur").css('color', color);
	        commande('foreColor', color);
	    }
	});
	
	//Drag & Drop
	$('#dragRemove').on('click', function(){
		laSelection.draggable( "destroy" );
	});
	$('#dragAdd').on('click', function(){
		laSelection.draggable({
			containment : "parent"
		});
	});
	
	//position
	$('#position').on('change', function(){
		laSelection.css('position', $(this).val());
	});
	
	
	$('#addTexte').on('click', function() {
		id = id + 1;
		
//		$('#contenu').append('<div class="texte_insert" id="divTexte' + id + '" style="border: 1px solid; width:150pt; height:150pt;"></div>');
//		$('#divTexte'+ id).append('<p id="p' + id + '"></p>');
//		$('#p'+ id).html($('#editeur').html());
		
		$('#contenu').append('<table id="tableTexte' + id + '" style="position: relative;" border=0 cellspacing=0 cellpadding=0 align=center class="texte_insert" width=600><tr><td id="divTexte' + id + '" width=600></td></tr></table>');
		$('#divTexte'+ id).append('<p id="p' + id + '"></p>');
		$('#p'+ id).html($('#editeur').html());
		
		// ajouter bouton supprimer à l'angle haut droit du texte inséré
		$('#tableTexte'+ id).append('<div class="option" id="remove' + id + '"><a class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove" style="color:red;"></span></a></div>');
		
//		$('#contenu').append('<div class="texte_insert" id="divTexte' + id + '" style="position: absolute; width:150px; height:150px; border: 1px solid;"></div>');
//		$('#divTexte'+ id).append('<p id="p' + id + '"></p>');
//		$('#p'+ id).html($('#editeur').html());
//		$('#divTexte'+ id).resizable({
//			containment : "parent"
//		});
//		$('#divTexte'+ id).draggable({
//			containment : "parent"
//		});
//   	  	// ajouter bouton supprimer à l'angle haut droit du texte inséré
//   	  	$('#divTexte' + id).append('<div class="option" id="remove' + id + '"><a class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove" style="color:red;"></span></a></div>');   	  	
	});
	
	// au passage de la souris sur la div contenu affiche les options
	$('#contenu')
		.mouseover(function(){
			$('.option, .ui-icon', this).show();
		})
		.mouseout(function(){
			$('.option, .ui-icon', this).hide();
		});
	
	// supprime le texte ou l'image
	$('#contenu').on('click', '.option', function(){
	  		$(this).parent().remove(); // supprime le parent
	});
	// au passage de la souris sur le texte ou l'image affiche les options
	$('#contenu').on('mouseover', '.texte_insert, .image_upload, .table_insert', function(){
		//$(this).children().show();
		$('.option, .ui-icon', this).show();
	});
	// quand le curseur sort du texte ou de l'image les options disparaissent
	$('#contenu').on('mouseleave', '.texte_insert, .image_upload, .table_insert', function(){
	  	//$(this).children().hide();
	  	$('.option, .ui-icon', this).hide();
	});
	  	
	// click sur un élément sauf si il est de la classe 'option'
	//$("*").not(".option, .btn btn-xs btn-default").on('click', clickSurUnElement);
	
	$('#contenu').on('click', '.table_insert', function(e){
		$('.image_upload, .texte_insert, .table_insert').removeClass('selection');
		$(this).addClass("selection"); // ajoute la classe 'selection' à l'élément cliqué	
		e.stopPropagation();
		info(this);
		laSelection = $(this);
	});
	  	
	$('#contenu').on('click', '.texte_insert', function(e){
	  	$('.imageCreation, .imageModification, .texteCreation').hide();
	  	$('.texte, .texteModification, .modification').show();
 		$('.image_upload, .texte_insert, .table_insert').removeClass('selection'); // supprime la classe 'selection' à toutes les div créés
   		$(this).addClass("selection"); // ajoute la classe 'selection' à la div cliquée		
	  	e.stopPropagation();
	  	//$('#info').text("Balise = " + this.tagName + " | id = " + this.id + " | class = " + this.className );
	  	info(this);
		$('#updateTexte').show();
	  		//$("#resultat").val($("#editeur").html());
	  		//$("#info").html($('#editeur').html());
	  		//alert($(':first-child', this).html());
	  		//alert($(this).html());
	  		
	  		//$("#editeur").html($(':first-child', this).html());
		
	  	//$("#editeur").html($('>p', this).html());
	  	//$("#editeur").html($('tr td p', this).html());
		$("#editeur").html($(this).html());
	  		
	  		//laSelection = $(':first-child', this); // affecte à la variable globale 'laSelection' le premier enfant de l'élément cliqué (soit p de la div classe 'texte_insert' selectionnée)
	  	laSelection = $(this); 
		//modifierTexte = $('>p', this); // affecte à la variable globale 'modifierTexte' l'élément 'p' enfant de l'élément sélectionné.
		//modifierTexte = $('tr td p', this);
	  	modifierTexte = $(this);
	  		
	  	//alert($(':first-child', this).attr('id'));
	});
	
 	$('#contenu').on('click', '.image_upload', function(e){
 		$('.texte, .texteCreation, .texteModification, .imageCreation').hide();
 	  	$('.imageModification, .modification').show();
   	  		
   		$('.image_upload, .texte_insert, .table_insert').removeClass('selection'); // supprime la classe 'selection' à toutes les div créés
   		$(this).addClass("selection"); // ajoute la classe 'selection' à la div cliquée
   		e.stopPropagation();
   		//$('#info').text("Balise = " + this.tagName + " | id = " + this.id + " | class = " + this.className );
   		info(this);
   	  	$('#updateTexte').hide();
   	  	laSelection = $(this);
   	});
 	
	
	$('#btnHTML').on('click', function(){
		alert($('#contenu').html());
	});
	
	$('#updateTexte').on('click', function(){
   	  	//alert(laSelection.attr('id'));
		//alert(laSelection.html());
   	  	$("#resultat").val($('#editeur').html());
   	  	//$("#info").html($('#editeur').html());
   	  	$(modifierTexte).html($('#editeur').html());
   	});
	$('#contenu').on('click', function(){
		$('.texte, .imageCreation, .texteCreation').show();
		$('.imageModification, .texteModification, .modification').hide();
		$('.image_upload, .texte_insert, .table_insert').removeClass('selection'); // supprime la classe 'selection' à toutes les div créés
	});
	
	//effet, bordures, CSS Modification
	$('#borderWidth').on('change', function(){
		$(laSelection).css('border-width', $(this).val() + 'px');
	});
	$('#borderStyle').on('change', function(){
		$(laSelection).css('border-style', $(this).val() );
	});
	$('#borderColor').on('change', function(){
		$(laSelection).css('border-color', $(this).val() );
	});
	$('#borderRadius').on('change', function(){
		$(laSelection).css('border-radius', $(this).val() + 'px');
	});
	
	//gradient
	$('#gradientDirection').on('change', function(){
		$(laSelection).css('background', '-moz-linear-gradient(' + $(this).val() + ',  ' + $("#gradientColor1").val() + ',  ' + $("#gradientColor2").val() + ')');
	});
	$('#gradientColor1').on('change', function(){
		$(laSelection).css('background', '-moz-linear-gradient(' + $("#gradientDirection").val() + ',  ' + $(this).val() + ',  ' + $("#gradientColor2").val() + ')');
	});
	$('#gradientColor2').on('change', function(){
		$(laSelection).css('background', '-moz-linear-gradient(' + $("#gradientDirection").val() + ',  ' + $("#gradientColor1").val() + ',  ' + $(this).val() + ')');
	});
	
	$('#border').on('click', function(){
 		$('ul.topnav li a').removeClass('active'); // supprime la classe 'active' à tous les 'a' de 'ul.topnav li'
   		$(this).addClass("active"); // ajoute la classe 'active' sur l'élément cliqué
   		$('#updateEffect').hide();
   		$('#updateBorder').show();
	});
	$('#effect').on('click', function(){
 		$('ul.topnav li a').removeClass('active'); // supprime la classe 'active' à tous les 'a' de 'ul.topnav li'
   		$(this).addClass("active"); // ajoute la classe 'active' sur l'élément cliqué
   		$('#updateBorder').hide();
   		$('#updateEffect').show();
	});
	
	$("#apercu").on('click', function(){
		//window.open($(this).attr("href"), "_blank", "width=600, height=600").opener.location.reload();
		//window.open($(this).attr("href"), "_blank").document.load('test.html');
		//window.open($(this).attr("href"), "_blank").load('test.html');
		//var myWindow = window.open("", "MsgWindow", "width=200,height=100");
		var childWindow = window.open($(this).attr("href"));
		//childWindow.location.reload(true);
		//myWindow.document.write("<p>This is 'MsgWindow'. I am 200px wide and 100px tall!</p>");
		//myWindow.document.write('<p></p><script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script><script>$(function(){ $("p").load("http://localhost/newsletter/test.html"); });</script>'); 
		return false; // sinon affiche sur la même page
	});
	
	// envoyer la newsletter
	$("#btnEnvoyerMail").on('click', function(event){
		
		//disable the default form submission
		event.preventDefault();
		
		// si le champ du formulaire 'from' est vide
		if($('#from').val() == ''){
		    // affiche la div id  pendant 10 sec puis disparaît
		    $("#from-control").show("slow").delay(10000).hide("slow");
		}
		// si le champ du formulaire 'to' est vide
		if($('#to').val() == ''){
		    // affiche la div id  pendant 10 sec puis disparaît
		    $("#to-control").show("slow").delay(10000).hide("slow");
		}
		// si le champ du formulaire 'to' est vide
		if($('#subject').val() == ''){
		    // affiche la div id  pendant 10 sec puis disparaît
		    $("#subject-control").show("slow").delay(10000).hide("slow");
		}

		if($('#from').val() != '' && $('#to').val() != '' && $('#subject').val() != ''){
			var contenu = $('#contenu').html();
			
			//window.open("test_load_ajax.html");
			//affiche le contenu du fichier email_transmission.htm toutes les 4 secondes
//			var majResultat = setInterval(function(){ 
//				alert( "majResultat() start" );
//				$('#resultatEmail').show();
//				$('#resultatEmail').load('email_transmission.htm');
//			}, 4000);
						
			$.post('email.php', {
				'contenu' : contenu,
				'idSmtp' : $('#fromIdSmtp').html(),
				'idListediffusion' : $('#toIdListediffusion').html(),
				'subject' : $('#subject').val()
			}, function(data){
//				clearInterval(majResultat); // supprime le timer de maj de l'affichage du contenu du fichier email_transmission.htm
				//$('#info').html(data);
			    // affiche les div de class .alert pendant 10 sec puis disparaît
			    //$("#alertMain").show("slow").delay(10000).hide("slow");
				$('#resultatEmail').html(data).show("slow").delay(10000).hide("slow");
			});
		}
		
	});
	
	
	//ajax ajout nouveau SMTP ou modification d'un SMTP
	$("form#addSmtp").submit('click', function(event){

		event.preventDefault(); //disable the default form submission
		
		var controller = $('form#addSmtp').attr('action');
		
		
		//alert($('form#addSmtp').attr('action'));
		var smtpId = $('#smtpId').val();
		var smtpLibelle = $('#smtpLibelle').val();
		var smtpHost = $('#smtpHost').val();
		var smtpPort = $('#smtpPort').val();
		var smtpSecure = $('#smtpSecure').val();
		var smtpUser = $('#smtpUser').val();
		var smtpPassword = $('#smtpPassword').val();
		
		//alert(smtpId + " " + smtpLibelle + " " + smtpHost + " " + smtpPort + " " + smtpSecure + " " + smtpUser + " " + smtpPassword);
		$('#fromContent').html('<img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">');
		$.post('pdo.php', {
			'controller' : controller,
			'smtpId' : smtpId,
			'smtpLibelle' : smtpLibelle,
			'smtpHost' : smtpHost,
			'smtpPort' : smtpPort,
			'smtpSecure' : smtpSecure,
			'smtpUser' : smtpUser,
			'smtpPassword' : smtpPassword,
			'utilisateurId' : utilisateurId,
			'libelle_societe' : $('#smtpLibelle_societe').val(),
			'adresse_societe' : $('#smtpAdresse_societe').val(),
			'url_ou_email_societe' : $('#smtpUrl_ou_email_societe').val()
		}, function(data){
			$('#fromContent').html(data);
		});
	});
	
	// click sur le lien du libellé smtp
	$('#fromMenuDown').on('click', '#fromContent a.libelleSmtp', function(){
		//alert('click sur = ' + $(this).html());
		if($(this).html() == 'Paramétrer un nouveau serveur SMTP'){
			$('#from').val("");
			$('#addSmtp #annuler').trigger('click'); // reset le formulaire
			$('#addSmtp #modifier').hide(500);
			$('#addSmtp #ajouter').show(500);
			$('#panelTitleSmtpModifer').hide(500);
			$('#panelTitleSmtpAjouter').show(500);
			$('#panelSmtp').show(500).removeClass('panel-success').addClass('panel-primary');
			$('form#addSmtp').attr('action', 'ajouter_smtp');
		} else {
			$('#panelSmtp').hide(500);
			$('#from').val($(this).html());
			$('#fromIdSmtp').html($(this).attr('id'));
			// test recupération de l'id qui est l'id du smtp
			//alert($('#fromIdSmtp').html());
		}		
	});
	
	//click sur modifier
	$('#fromMenuDown').on('click', '#fromContent a.edit', function(){
		//alert($(this).attr('id'));
		$('#addSmtp #ajouter').hide(500);
		$('#addSmtp #modifier').show(500);
		$('#panelTitleSmtpAjouter').hide(500);
		$('#panelTitleSmtpModifer').show(500);
		$('#panelSmtp').show(500).removeClass('panel-primary').addClass('panel-success');
		$('form#addSmtp').attr('action', 'modifier_smtp');
//		$('form#addSmtp').attr('action', 'testpdo.php');
		
	
		// envoi de l'id et récupération des données via json format
		$.post('pdo.php', {
			'controller' : 'infos_smtp',
			'id' : $(this).attr('id')
		}, function(data){
			//alert(data.libelle + " " + data.host + " " + data.port + " " + data.smtpsecure + " " + data.username + " " + data.password);
			$("#smtpId").val(data.id);
			$('#smtpLibelle').val(data.libelle);
			$('#smtpHost').val(data.host);
			$('#smtpPort').val(data.port);
			$('#smtpSecure').val(data.smtpsecure);
			$('#smtpUser').val(data.username);
			$('#smtpPassword').val(data.password);
			$('#smtpLibelle_societe').val(data.libelle_societe),
			$('#smtpAdresse_societe').val(data.adresse_societe),
			$('#smtpUrl_ou_email_societe').val(data.url_ou_email_societe);
			
		}, "json");
		
	});
	
	//click sur supprimer
	$('#fromMenuDown').on('click', '#fromContent a.remove', function(){
		//alert($(this).attr('id'));
		var r = confirm("Êtes-vous sûr de vouloir supprimer le serveur SMTP n°"+$(this).attr('id')+"?");
		if (r == true) {
			$('#fromContent').html('<img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">');
			$.post('pdo.php', {
				'controller' : 'supprimer_smtp',
				'id' : $(this).attr('id'),
				'utilisateurId' : utilisateurId
			}, function(data){
				//$('#info').html(data);
				$('#fromContent').html(data);
			});
		} 
	});
	
	
	$('#modalEmail').on('show.bs.modal', function(e){
		//$('#divApercu').load('test.html');
		$('#divApercu').html($('#contenu').html());
	});
	
	$('#btnFrom').on('click', function(){
		//$('.menuDown-content').css('display', 'block');
		$('#fromContent').toggle(500);
	});
	$('#btnTo').on('click', function(){
		//$('.menuDown-content').css('display', 'block');
		$('#toContent').toggle(500);
	});
	
	$('#toMenuDown').on('click', '#toContent #btnAddListediffusion', function(){
		$('#input-group-add').toggle(500);
	});
	
	// click sur un lien libellé liste de diffusion
	$('#toMenuDown').on('click', 'a.libelleListediffusion', function(){
		$('#to').val($(this).html());
		$('#toIdListediffusion').html($(this).attr('id'));
	});
	
	// ajouter une liste de diffusion en bdd
	$('#toMenuDown').on('click', '#toContent #btnAddListediffusionValid', function(event){	
				  
		event.preventDefault(); //disable the default form submission
		  
		var controller = 'ajouter_listediffusion';

		var listediffusionLibelle = $('#listediffusionAdd').val();
			
		$('#toContent').html('<img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">');
		$.post('pdo.php', {
			'controller' : controller,
			'listediffusionLibelle' : listediffusionLibelle,
			'utilisateurId' : utilisateurId
		}, function(data){
				//$('#info').html(data);
				$('#toContent').html(data);
		});
	});
	
	// affiche les destinataires d'une liste de diffusion sur le clique du bouton edit
	$('#toMenuDown').on('click', '#toContent a.edit', function(){
		$('#panelDestinataire').show(500);
		
		$('#panelDestinataire').html('<div style="text-align:center;"><img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif"></div>');
		// envoi de l'id de la liste de diffusion sélectionnée
		$.post('pdo.php', {
			'controller' : 'modifier_listediffusion',
			'idListediffusion' : $(this).attr('id')
		}, function(data){
			//alert(data.libelle + " " + data.host + " " + data.port + " " + data.smtpsecure + " " + data.username + " " + data.password);
			$('#panelDestinataire').html(data);	
		});
		
	});
	
	//supprime la liste de diffusion sélectionnée ainsi que les destinataires associés
	$('#toMenuDown').on('click', '#toContent a.remove', function(){
		var r = confirm("Êtes-vous sûr de vouloir supprimer la liste de diffusion n°"+$(this).attr('id')+"?");
		if (r == true) {
			$('#toContent').html('<img style="text-align:center;" src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">');
			$.post('pdo.php', {
				'controller' : 'supprimer_listediffusion',
				'id' : $(this).attr('id'),
				'utilisateurId' : utilisateurId
			}, function(data){
				//$('#info').html(data);
				$('#toContent').html(data);
			});
		}
	});
	
	//supprime le destinataire sélectionné
	$('#panelDestinataire').on('click', '.remove', function(){
		var r = confirm("Êtes-vous sûr de vouloir supprimer le destinataire n°"+$(this).attr('id')+" ainsi que tout l'historique des transmissions associé à ce destinataire?");
		if (r == true) {
			
			var idListediffusion = $('#divA a').attr('id');
			
			$('#panelDestinataire').html('<img style="text-align:center;" src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">');
			
			$.post('pdo.php', {
				'controller' : 'supprimer_destinataire',
				'id' : $(this).attr('id'),
				'idListediffusion' : idListediffusion
			}, function(data){
				//$('#info').html(data);
				$('#panelDestinataire').html(data);
			});
		}
	});
	
	//TODO afficher l'historique d'un destinataire d'une liste de diffusion
	$('#panelDestinataire').on('click', '.historique_transmission', function(){
		var toggleid = '#toggleHistorique' + $(this).attr('id');
		$(toggleid).slideToggle("slow").html('<img style="text-align:center;" src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">');
		//alert('destinataire_id = ' + $(this).attr('id') + ' destinataire_listediffusion_id = ' + $('#divA a').attr('id') + "#toggleHistorique" + $(this).attr('id'));
		$.post('pdo.php', {
			'controller' : 'afficher_historique_transmission_destinataire',
			'id' : $(this).attr('id'),
			'idListediffusion' : $('#divA a').attr('id')
		}, function(data){
			//$('#info').html(data);
			//alert('success '+ '#toggleHistorique' + $(this).attr('id') );
			$(toggleid).html(data);
			//alert($('#toggleHistorique' + $(this).attr('id')).html() );
		});
		
	});
	
	//modifie le nom d'un destinataire
	$('#panelDestinataire').on('submit', '.destinataireNom', function(event){
		//alert($('input.destinataireNom', this).val());
		
		var controller = 'modifier_nom_destinataire';
		
		event.preventDefault(); //disable the default form submission

		var idListediffusion = $('#divA a').attr('id');
			
		$('.destinataireNom', this).html('<img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">');
		$.post('pdo.php', {
			'controller' : controller,
			'id' : $('input.destinataireId', this).val(),
			'nom' : $('input.destinataireNom', this).val(),
			'idListediffusion' : idListediffusion
		}, function(data){
			$('#panelDestinataire').html(data);
		});
	});
	
	//modifie l'email d'un destinataire
	$('#panelDestinataire').on('submit', '.destinataireEmail', function(event){
		//alert($('input.destinataireEmail', this).val());
		
		var controller = 'modifier_email_destinataire';
		
		event.preventDefault(); //disable the default form submission

		var idListediffusion = $('#divA a').attr('id');
			
		$('.destinataireEmail', this).html('<img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">');
		$.post('pdo.php', {
			'controller' : controller,
			'id' : $('input.destinataireId', this).val(),
			'email' : $('input.destinataireEmail', this).val(),
			'idListediffusion' : idListediffusion
		}, function(data){
			$('#panelDestinataire').html(data);
		});
	});
	
	// ferme le panel smtp en cliquant sur la petite croix située sur le coin supérieur droit.
	$('#panelHeadingSmtp .close').on('click', function(){
		$('#panelSmtp').hide(500);
	});
	
	// ferme le panel destinataire en cliquant sur la petite croix située sur le coin supérieur droit.
	$('#panelDestinataire').on('click', '#panelHeadingDestinataire .close', function(){
		$('#panelDestinataire').hide(500);
	});
	
	// affiche ou fait disparaitre le formulaire de saisi d'un nouveau destinataire en cliquant sur le bouton avec un +
	$('#panelDestinataire').on('click', '#btnAddDestinataireListediffusion', function(){
		$('#divAddDestinataireForm').toggle(500);
	});
	
	$('#panelDestinataire').on('click', '#btnAddDestinataireListediffusionValid', function(event){
		event.preventDefault(); //disable the default form submission
		
		var controller = 'ajouter_destinataire';

		var idListediffusion = $('#addDestinataireListediffusion a').attr('id');		
		var email = $('#destinataireEmailAdd').val();
		var nom = $('#destinataireNameAdd').val();
			
		$('#panelDestinataire').html('<img class="img-responsive" src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">');
		$.post('pdo.php', {
			'controller' : controller,
			'idListediffusion' : idListediffusion,
			'email' : email,
			'nom' : nom
		}, function(data){
			//$('#info').html(data);
			$('#panelDestinataire').html(data);
		});
	});

	$("#btnAddImage").on('click', function() {
		$('#file').trigger('click');
	});
	
	$("#file").change(function() {
		$('form#data').trigger('submit');
	});
	$('#contenu').on('click', 'table tr td .btnAddImageModele', function() {
		ajouterImage = $(this);
		//info(this);
		$('#file').trigger('click');
	});
	
	//Program a custom submit function for the form
	$("form#data").submit(function(event){
	 
	  //disable the default form submission
	  event.preventDefault();
	 
	  //grab all form data  
	  var formData = new FormData($(this)[0]);
	  //$('#contenu').html('<img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">');
	  $.ajax({
	    url: 'formprocessing.php',
	    type: 'POST',
	    data: formData,
	    async: false,
	    cache: false,
	    contentType: false,
	    processData: false,
	    success: function (returndata) {
//	      alert(returndata);
//	  	  id = id + 1;
//	  	  $('#contenu').append('<div class="image_upload" id="image_preview' + id	+ '" style="position:absolute;"></div>');
//     	  $('#image_preview' + id).css("width", "50%").css("height", "100px")
//				.css("background-image", "url('" + returndata + "')").css("background-repeat", "no-repeat")
//				.css("background-position", "center").css("background-size", "contain")
//				.css("position", "absolute").css("border", "1px solid");
//     	  $('#image_preview' + id).draggable({
//			containment : "#contenu"
//     	  });
//     	  $('#image_preview' + id).resizable({
//			containment : "#contenu"
//     	  });
//     	  // ajouter bouton supprimer à l'angle haut droit de l'image uploadé
//     	  $('#image_preview' + id).append('<div class="option" id="remove' + id + '"><a class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove" style="color:red;"></span></a></div>');

	      id = id+1;
//	      $('#contenu').append('<img src="' + returndata + '" class="image_upload" id="image_preview' + id	+ '">');
     	  
	      //$(this).parent().remove(); // supprime le parent
	      $(ajouterImage).parent().html(
	    	  '<img src="' + returndata + '" class="image_upload" id="image_preview' + id	+ '" style="max-width:600px;">'
	      );
	      
     		// click sur un élément
     		//$("*").on('click', clickSurUnElement);
	    }
	  });
	 
	  return false;
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

function resultat() {
	document.getElementById("resultat").value = document.getElementById("editeur").innerHTML;
}

function clickSurUnElement(e) {
	e.stopPropagation();
	$('#info').text("Balise = " + this.tagName + " | id = " + this.id + " | class = " + this.className );
//	if(this.className == "image_upload ui-draggable ui-draggable-handle ui-resizable" || this.className == "image_upload")  {
//		//$('#updateTexte').css("visibility", "hidden");
//		$('#updateTexte').hide();
//	} else if(this.className == "texte_insert ui-resizable ui-draggable ui-draggable-handle" || this.className == "texte_insert")  {
//		//$('#updateTexte').css("visibility", "visible");
//		$('#updateTexte').show();
//		//$("#resultat").val($("#editeur").html());
//		//$("#info").html($('#editeur').html());
//		//alert($(':first-child', this).html());
//	}
}

function info(thisone) {
	 
	//alert($(uneSelection).attr('align'));
	$('#info').html(
			'<table class="table table-hover alert alert-info">'+
				'<thead style="color:white; background-color:black;">'+
					'<tr>'+
						'<td>Type</td>'+
						'<td>Résultat</td>'+
						'<td>Modification</td>'+
					'</tr>'+
				'</thead>'+
				'<tbody>'+
					'<tr id="baliseInfo">'+
						'<td>Balise</td>'+
						'<td>' + thisone.tagName + '</td>'+
						'<td></td>'+
					'</tr>'+
					'<tr id="idInfo">'+
						'<td>Id</td>'+
						'<td>' + thisone.id + '</td>'+
						'<td></td>'+
					'</tr>'+
					'<tr id="classInfo">'+
						'<td>Classe</td>'+
						'<td>' + thisone.className + '</td>'+
						'<td></td>'+
					'</tr>'+
					'<tr id="attributAlignInfo">'+
						'<td>Attribut Align</td>'+
						'<td>' + $(thisone).attr('align') + '</td>'+
						'<td>'+
					       	'<select id="alignSelectInfo">'+
				          		'<option value="justify" selected="selected">Justifié</option>'+
				          		'<option value="center">Centré</option>'+
				          		'<option value="left">Gauche</option>'+
				          		'<option value="right">Droite</option>'+
					       	'</select>'+
						'</td>'+
					'</tr>'+
					'<tr id="styleBackgroundInfo">'+
						'<td>Background</td>'+
						'<td>' + $(thisone).css('background-color') + '</td>'+
						'<td><input type="color" value="#ffffff" id="backgroundColor">'+
						'</td>'+
					'</tr>'+
					'<tr id="attributTailleInfo">'+
						'<td>Largeur</td>'+
						'<td>' + $(thisone).attr('width') + '</td>'+
						'<td><input id="widthInfo" type="range" min=1 max=600 value=1 >'+
						'</td>'+
					'</tr>'+
				'</tbody>'+
			'</table>'+
			'<div align=center style="padding-bottom:16px;">'+
				'<button class="btn btn-danger" id="btnSupprimerInfo" type="button"><span class="glyphicon glyphicon-remove"></span> Supprimer</button>'+
			'</div>'+
			'<div align=center style="padding-bottom:16px;">'+
				'<button class="btn btn-default" id="btnAddLigneInfo" type="button" title="Ajout un saut de ligne"><span class="glyphicon glyphicon-rub"></span></button>'+
			'</div>'
				
				
			
//				'Balise = ' + this.tagName + '<br/>'+
//				'id = ' + this.id + '<br/>'+
//				'class = ' + this.className + '<br/>'+
//				'align = ' + $(this).attr('align') + '<br/>'
		);
		//$('#info').text("Balise = " + this.tagName + " | id = " + this.id + " | class = " + this.className );
	// Si une table est selectionnée
	if(thisone.tagName == 'TABLE'){
		// On ajoute le bouton 'ajouter une ligne au tableau'
		$('#info').append(
			'<div class="col-lg-12" align=center style="padding-bottom:16px;">'+
				'<div class="col-lg-6 col-md-12">'+
					'<button class="btn btn-primary" id="btnAddLigneTexteTable" type="button"><span class="glyphicon glyphicon-plus"></span> Ajouter un bloc de texte</button>'+
				'</div>'+
				'<div class="col-lg-6 col-md-12">'+
					'<button class="btn btn-primary" id="btnAddLigneImageTable" type="button"><span class="glyphicon glyphicon-plus"></span> Ajouter un bloc d\'Image</button>'+
				'</div>'+
			'</div>'
		);
	}
}


//insert un model à contenu
function insertModel(){
	//todo si chiffre en paramètre (choix du modele)
	//modele image + texte
	$('#contenu').html(
		'<div class="option" id="addModele" style="display:none;"><a class="btn btn-xs btn-default"><span class="glyphicon glyphicon-plus" style="color:blue;"></span></a></div>'+
		'<table class="table_insert" id="tableTexte" style="position: relative;" border=0 cellspacing=0 cellpadding=0 align=center width=600>'+
			'<tr>'+
				'<td align=center>'+
					'<button class="btn btn-warning btnAddImageModele" type="button"><span class="glyphicon glyphicon-picture"></span>Ajouter une image</button>'+
				'</td>'+
			'</tr>'+
			'<tr>'+
				'<td align=center id="divTexte" width=600>'+
					'<p class="texte_insert">Ajouter du texte</p>'+
				'</td>'+
			'</tr>'+
		'</table>'
	);
	
}



