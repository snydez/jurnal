$(function() {
	
	// Place ID's of all required fields here.
	required = ["txtNama", "txtemail", "txtKoment"];
	// If using an ID other than #email or #error then replace it here
	email = $("#txtemail");
	errornotice = $("#errorwarning");
	// The text to show up within a field when it is incorrect
	emptyerror = "Please fill out this field.";
	emailerror = "Please enter a valid e-mail.";

	$('#btnPreview').click(function(e) {
		e.preventDefault();
		
		for(i=0;i<required.length;i++) {
			var input = $('#' + required[i]);
			if ((input.val() == "") || (input.val() == emptyerror )) {
				input.addClass("needsfilled");
				input.val(emptyerror);
				errornotice.fadeIn(550);
			} else {
				input.removeClass("needsfilled");
			}
		} // end for
		
		// validate email
		if (!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(email.val())) {
			email.addClass("needsfilled");
			email.val(emailerror);
		}
		
		//kalo masih ada yang error gak bisa submit
		if ($(":input").hasClass("needsfilled")) {
			return false;
		} else {
			errornotice.hide();
			$("#frmKoment").submit();
/*			var x = $("#txtKoment").val();
			$("#btnPreview").hide()
			$("#frmKoment fieldset").hide();
			$("#frmKoment").append("<input type=\"hidden\" value=\"hiddenpreview\"");
			$("#frmKoment").append(x);
			$("#frmKoment").append("<input type=\"submit\" value=\"submit\"");
*/
			return true;
		}
	
	});
  
	// clears any fields when the user click on them.
	$(":input").click(function() {
		if ($(this).hasClass("needsfilled") ) {
			$(this).val("");
		}
	});
	
	$(":textarea").click(function() {
		if ($(this).hasClass("needsfilled") ) {
			$(this).val("");
			
		}
	});
  
  
  
  });
