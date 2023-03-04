(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( ["jquery", "../jquery.validate"], factory );
	} else if (typeof module === "object" && module.exports) {
		module.exports = factory( require( "jquery" ) );
	} else {
		factory( jQuery );
	}
}(function( $ ) {

/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: ID (Indonesia; Indonesian)
 */
$.extend( $.validator.messages, {
	required: "Kolom ini diperlukan.",
	remote: "Harap benarkan kolom ini.",
	email: "Silakan masukkan format email yang benar.",
	url: "Silakan masukkan format URL yang benar.",
	date: "Silakan masukkan format tanggal yang benar.",
	dateISO: "Silakan masukkan format tanggal(ISO) yang benar.",
	number: "Silakan masukkan angka yang benar.",
	digits: "Harap masukan angka saja.",
	creditcard: "Harap masukkan format kartu kredit yang benar.",
	equalTo: "Konfirmasi password belum sama dengan password baru.",
	alphanum: "Kolom harus mengandung setidaknya 1 huruf dan 1 digit.",
	maxlength: $.validator.format( "Input dibatasi hanya {0} karakter." ),
	minlength: $.validator.format( "Input tidak kurang dari {0} karakter." ),
	rangelength: $.validator.format( "Panjang karakter yg diizinkan antara {0} dan {1} karakter." ),
	range: $.validator.format( "Harap masukkan nilai antara {0} dan {1}." ),
	max: $.validator.format( "Harap masukkan nilai lebih kecil atau sama dengan {0}." ),
	max_amount: $.validator.format( "Harap masukkan nilai lebih kecil atau sama dengan {0}." ),
	max_amount_request: $.validator.format( "Harap masukkan nilai lebih kecil atau sama dengan {0}." ),
	max_amount_approve: $.validator.format( "Harap masukkan nilai lebih kecil atau sama dengan {0}." ),
	min: $.validator.format( "Harap masukkan nilai lebih besar atau sama dengan {0}." ),
	extension: $.validator.format( "Ekstensi file yang bisa di upload hanya .{0}, .{1} dan .{2}." ),
} );
return $;
}));