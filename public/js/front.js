// on startup


$('.clickable-row').on('click', clickableRowOnClick);

//   functions

function sendreadings(saveonly = false) {
    let readings = {};
    $(".table-readings input").each(function (index, el) {
        $(el).attr("disabled", true);
        readings[$(el).attr("name")] = $(el).val();
    });

    $(".btn.sendreadings").addClass("loading");
    $(".btn.sendreadings i").hide();
    $(".btn.sendreadings").attr("disabled", true);
    $(".btn.savereadings").attr("disabled", true);

    if (saveonly) {
        urlPost = '/savereadings'
    } else {
        urlPost = '/sendreadings'
    }
    $.ajax({
        method: "post",
        url: urlPost,
        data: readings,
    }).done(function (data) {
        if (saveonly) {
            showModal('Сохранение показаний', 'Данные были сохранены.');
            $(".table-readings input").removeAttr("disabled");
            $(".btn.sendreadings").removeAttr("disabled");
            $(".btn.savereadings").removeAttr("disabled");
        } else {
            showModal('Передача показаний', 'Данные были переданы на обработку. Копия данных отправлена на указанный в настройках электронный ящик.');
        }
    }).fail(function (jqXHR, textStatus) {
        alert("ошибка: " + textStatus);
        $(".table-readings input").removeAttr("disabled");
    }).always(function () {
        $(".btn.sendreadings").removeClass("loading");
        $(".btn.sendreadings i").show();
        $(".btn.sendreadings").removeAttr("disabled");
        $(".btn.savereadings").removeAttr("disabled");
    });
}

function showModal(title, content) {
    $('#modalTemplate-Title').text(title);
    $('#modalTemplate-Body').text(content);
    $('#modalTemplate').modal('show');

}

function hideModal() {
    $('#modalTemplate').modal('hide');
    $('#modalTemplate-Body').text('');
}

function clickableRowOnClick() {
    window.location = $(this).data('href');
    return false;
}