// on startup

$('.clickable-row').on('click', clickableRowOnClick);



//   functions

function savereadings() {
    let readings = {};
    $(".table-readings input").each(function (index, el) {
        $(el).attr("disabled", true);
        readings[$(el).attr("name")] = $(el).val();
    });
    let btnSend = $(".btn.sendreadings");
    let btnSave = $(".btn.savereadings");
    btnSend.addClass("loading");
    btnSend.find('i').hide();
    btnSend.attr("disabled", true);
    btnSave.attr("disabled", true);

    $.ajax({
        method: "post",
        url: "/savereadings",
        data: readings,
    }).done(function (data) {
        showModal('Сохранение показаний', 'Данные были сохранены.');
        $(".table-readings input").removeAttr("disabled");
        btnSend.removeAttr("disabled");
        btnSave.removeAttr("disabled");
    }).fail(function (jqXHR, textStatus) {
        alert("ошибка сохранения данных: " + textStatus);
        $(".table-readings input").removeAttr("disabled");
    }).always(function () {
        btnSend.removeClass("loading");
        btnSend.find('i').show();
        btnSend.removeAttr("disabled");
        btnSave.removeAttr("disabled");
    });
}

function sendreadings(saveonly = false) {
    let readings = {};
    $(".table-readings input").each(function (index, el) {
        $(el).attr("disabled", true);
        readings[$(el).attr("name")] = $(el).val();
    });
    let btnSend = $(".btn.sendreadings");
    let btnSave = $(".btn.savereadings");
    btnSend.addClass("loading");
    btnSend.find('i').hide();
    btnSend.attr("disabled", true);
    btnSave.attr("disabled", true);

    $.ajax({
        method: "post",
        url: "/sendreadings",
        data: readings,
    }).done(function (data) {
        showModal('Передача показаний', 'Данные были переданы на обработку. Копия данных отправлена на указанный в настройках электронный ящик.');
    }).fail(function (jqXHR, textStatus) {
        alert("техническая ошибка сохранения данных: " + textStatus);
        $(".table-readings input").removeAttr("disabled");
        btnSend.removeAttr("disabled");
        btnSave.removeAttr("disabled");
    }).always(function () {
        btnSend.removeClass("loading");
        btnSend.find('i').show();
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