function preventAlpha(event) {
    var regex = new RegExp("^[a-zA-Z]+$");
    var str = String.fromCharCode(
        !event.charCode ? event.which : event.charCode
    );
    if (!regex.test(str)) {
        return true;
    }

    event.preventDefault();
    return false;
}

function numberFormat(element, currency = false, decimal = false) {
    $(element).val(function (index, value) {
        value = value.replace(/[^0-9\.]/g, "");
        if (decimal) {
            if (value.length > 0) {
                var split = value.split(".");
                if (split.length > 1) {
                    value = parseFloat(value).toFixed(2);
                } else {
                    value += ".00";
                }
            } else {
                value = "0.00";
            }
        }
        if (currency) {
            var split = value.split(".");
            value = split[0].replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
            if (split.length > 1) {
                value += "." + split[1];
            }
        }
        return value;
    });

    return element;
}

function formatCurrency(value) {
    value += "";
    return value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

function amountText(value, target = null) {
    value = value.replace(/[^0-9\.]/g, "");
    bilangan = String(value);
    let angka = new Array(
        "0",
        "0",
        "0",
        "0",
        "0",
        "0",
        "0",
        "0",
        "0",
        "0",
        "0",
        "0",
        "0",
        "0",
        "0",
        "0"
    );
    let kata = new Array(
        "",
        "Satu",
        "Dua",
        "Tiga",
        "Empat",
        "Lima",
        "Enam",
        "Tujuh",
        "Delapan",
        "Sembilan"
    );
    let tingkat = new Array("", "Ribu", "Juta", "Milyar", "Triliun");

    let panjang_bilangan = bilangan.length;
    let kalimat = (subkalimat = kata1 = kata2 = kata3 = "");
    let i = (j = 0);

    /* pengujian panjang bilangan */
    if (panjang_bilangan > 15) {
        kalimat = "Diluar Batas";
        return kalimat;
    }

    /* mengambil angka-angka yang ada dalam bilangan, dimasukkan ke dalam array */
    for (i = 1; i <= panjang_bilangan; i++) {
        angka[i] = bilangan.substr(-i, 1);
    }

    i = 1;
    j = 0;
    kalimat = "";

    /* mulai proses iterasi terhadap array angka */
    while (i <= panjang_bilangan) {
        subkalimat = "";
        kata1 = "";
        kata2 = "";
        kata3 = "";

        /* untuk Ratusan */
        if (angka[i + 2] != "0") {
            if (angka[i + 2] == "1") {
                kata1 = "Seratus";
            } else {
                kata1 = kata[angka[i + 2]] + " Ratus";
            }
        }

        /* untuk Puluhan atau Belasan */
        if (angka[i + 1] != "0") {
            if (angka[i + 1] == "1") {
                if (angka[i] == "0") {
                    kata2 = "Sepuluh";
                } else if (angka[i] == "1") {
                    kata2 = "Sebelas";
                } else {
                    kata2 = kata[angka[i]] + " Belas";
                }
            } else {
                kata2 = kata[angka[i + 1]] + " Puluh";
            }
        }

        /* untuk Satuan */
        if (angka[i] != "0") {
            if (angka[i + 1] != "1") {
                kata3 = kata[angka[i]];
            }
        }

        /* pengujian angka apakah tidak nol semua, lalu ditambahkan tingkat */
        if (angka[i] != "0" || angka[i + 1] != "0" || angka[i + 2] != "0") {
            subkalimat =
                kata1 + " " + kata2 + " " + kata3 + " " + tingkat[j] + " ";
        }

        /* gabungkan variabe sub kalimat (untuk Satu blok 3 angka) ke variabel kalimat */
        kalimat = subkalimat + kalimat;
        i = i + 3;
        j = j + 1;
    }

    /* mengganti Satu Ribu jadi Seribu jika diperlukan */
    if (angka[5] == "0" && angka[6] == "0") {
        kalimat = kalimat.replace("Satu Ribu", "Seribu");
    }

    var result = kalimat.trim().replace(/\s{2,}/g, " ") + " Rupiah";
    if(target != null) {
        $(target).val(result);
        return;
    } else {
        return result;
    }
}

function multidata(element) {
    var checkboxes = $(element).attr('name')
    var id = $(element).attr('id')
    var count = 0;
    var available = 0;
    var checked = [];
    var data_id = [];
    $('input[name="'+checkboxes+'"]').each(function(){
        if($(this).is(':checked')) {
            count += 1;
            checked.push($(this))
        }
    })

    if(count > 1) {
        $.each(checked, function(index,value){
            if(value.attr('id') != id) {
                $('input[name="'+checkboxes+'"]:not(#' + id + ')').each(function(){
                    $(this).prop('checked', false)
                })
                count = 1;
                available = $(element).data('available')
                data_id.push($(element).val());
            } else {
                available += $(this).data('available')
                data_id.push($(this).val());
            }
        })
    } else {
        available = $(element).data('available')
        data_id.push($(element).val());
    }

    if(count > 1) {
        $('input[name=is_multiple]').val(1)
    } else {
        $('input[name=is_multiple]').val(0)
    }

    return {
        data_id :data_id.join('|'),
        amount : $(element).data('amount'),
        available : available,
        ma : $(element).data('ma')
    }
}
