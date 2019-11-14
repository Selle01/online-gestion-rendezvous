

export default function appendError(data) {
    for (const key in data) {
        if (data.hasOwnProperty(key)) {
            const errors = data[key];
            for (const nameChamp in errors) {
                if (errors.hasOwnProperty(nameChamp)) {
                    const element = errors[nameChamp];
                    var errorsChamps = "";
                    $('#' + nameChamp).addClass(' is-invalid').next().html('');
                    element.forEach(function (value) {
                        errorsChamps += "<li>" + value + "</li>";
                    });
                }
                $("<ul style='color:red;'>" + errorsChamps + "</ul>").insertAfter('#' + nameChamp);
            }
        }
    }
}




