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
    value += ""
    return value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}
