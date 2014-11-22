(function($) {
	$.fn.validationEngineLanguage = function() {};
	$.validationEngineLanguage = {
		newLang: function() {
			$.validationEngineLanguage.allRules = 	{
				"required": { // Add your regex rules here, you can take telephone as an example
                    "regex": "none",
                    "alertText": "* Câmp obligatoriu",
                    "alertTextCheckboxMultiple": "* Selectaţi o opţiune",
                    "alertTextCheckboxe": "* Câmp obligatoriu"
                },				
				 "minSize": {
                    "regex": "none",
                    "alertText": "* Minim ",
                    "alertText2": " caractere permise"
                },
                "maxSize": {
                    "regex": "none",
                    "alertText": "* Maxim ",
                    "alertText2": " caractere permise"
                },
				"groupRequired": {
                    "regex": "none",
                    "alertText": "* Trebuie să completaţi unul din următoarele câmpuri"
                },
                "min": {
					"regex": "none",
                    "alertText": "* Valoarea minimă este "
                },
                "max": {
                    "regex": "none",
                    "alertText": "* Valoarea maximă este "
                },
                "past": {
                    "regex": "none",
                    "alertText": "* Data inainte de "
                },
                "future": {
                    "regex": "none",
                    "alertText": "* Data dupa "
                },
				"maxCheckbox": {
                    "regex": "none",
                    "alertText": "* Limita maxima de optiuni a fost depasita"
                },
                "minCheckbox": {
                    "regex": "none",
                    "alertText": "* Selectati cel putin ",
                    "alertText2": " optiuni"
                },
                "equals": {
                    "regex": "none",
                    "alertText": "* Campurile nu coincid"
                },
                "phone": {
                    // credit: jquery.h5validate.js / orefalo
                    "regex": /^([\+][0-9]{1,3}[ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9 \.\-\/]{3,20})((x|ext|extension)[ ]?[0-9]{1,4})?$/,
                    "alertText": "* Numar de telefon eronat"
                },
                "email": {
                    // Shamelessly lifted from Scott Gonzalez via the Bassistance Validation plugin http://projects.scottsplayground.com/email_address_validation/
                    "regex": /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
                    "alertText": "* Adresa de email eronata"
                },
                "integer": {
                    "regex": /^[\-\+]?\d+$/,
                    "alertText": "* Numar intreg eronat"
                },
                "number": {
                    // Number, including positive, negative, and floating decimal. credit: orefalo
                    "regex": /^[\-\+]?(([0-9]+)([\.,]([0-9]+))?|([\.,]([0-9]+))?)$/,
                    "alertText": "* Numar zecimal eronat"
                },
                "date": {
                    "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/,
                    "alertText": "* Data eronata, formatul de introducere este: YYYY-MM-DD"
                },
                "ipv4": {
                    "regex": /^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,
                    "alertText": "* Adresa IP eronata"
                },
                "url": {
                    "regex": /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,
                    "alertText": "* URL eronat"
                },
                "onlyNumberSp": {
                    "regex": /^[0-9\ ]+$/,
                    "alertText": "* Doar numere"
                },
                "onlyLetterSp": {
                    "regex": /^[a-zA-Z\ \']+$/,
                    "alertText": "* Doar litere"
                },
                "onlyLetterNumber": {
                    "regex": /^[0-9a-zA-Z]+$/,
                    "alertText": "* Caracterele speciale (',', '.', '-', etc) nu sunt permise"
                },
				//custom
				"ajaxUser":{
					"url":"/ajax/check-username",
					"extraData":"",
					"alertText": "* Acest nume de utilizator este deja folosit",
                    "alertTextLoad": "* Se valideaza, va rugam asteptati"
				},
				 "ajaxAdminUser": {
					// remote json service location
					"url": "/admin/ajax/check-username",
					"alertTextLoad":"* Se incarca, va rugam asteptati",
					"alertText":"* Acest nume de utilizator este deja folosit"
				},
				"ajaxAdminSku":{
					"url":"/admin/ajax/check-sku",
					"extraData":"",
					"alertTextLoad":"* Se incarca, va rugam asteptati",
					"alertText":"* Acest cod este deja folosit"
				},
				"ajaxAdminEmail":{
					"url":"/admin/ajax/check-email",
					"extraData":"",
					"alertText":"* Acest email este deja folosit ",
					"alertTextLoad":"* Se incarca, va rugam asteptati"
				},
				"ajaxEmail":{
					"url":"/ajax/check-email",
					"extraData":"",
					"alertText":"* Acest email este deja folosit ",
					"alertTextLoad":"* Se incarca, va rugam asteptati"
				},
				"ajaxEmails":{
					"url":"/ajax/check-emailaccount",
					"extraData":"",
					"alertText":"* Acest email este deja folosit ",
					"alertTextLoad":"* Se incarca, va rugam asteptati"
				},	
				"ajaxPassword":{
					"extraData":"",
					"url":"/ajax/check-password",
					"alertText":"* Parola veche incorecta!",
					"alertTextLoad":"* Se incarca, va rugam asteptati"
				},			
				"ajaxAdminCoupon":{
					"url":"/admin/ajax/check-coupon",
					"extraData":"",
					"alertText":"* Acest cupon a fost folosit",
					"alertTextOk":"",
					"alertTextLoad":"* Se incarca, va rugam asteptati"
				}
			}
		}
	}
})(jQuery);

$(document).ready(function() {
	$.validationEngineLanguage.newLang()
});