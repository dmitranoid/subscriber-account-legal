// on startup


$('.clickable-row').on('click', clickableRowOnClick);

//   functions

function sendreadings(saveonly=false) {
    let readings = {};
    $(".table-readings input").each(function(index, el){
        $(el).attr("disabled", true);
        readings[$(el).attr("name")] = $(el).val();
    });

    $(".btn.sendreadings").addClass("loading");
    $(".btn.sendreadings i").hide();
    $(".btn.sendreadings").attr("disabled", true);
    $.ajax({
        method: "post",
        url: "/sendreadings?saveonly=" + saveonly,
        data: readings,
    }).done(function(data) {
        if (saveonly) {
            showModal('Сохранение показаний', 'Данные были сохранены.');
        }
        showModal('Передача показаний', 'Данные были переданы на обработку. Копия данных отправлена на указанный в настройках электронный ящик.');

    }).fail(function (jqXHR, textStatus ) {
      alert("ошибка: " + textStatus);
    }).always(function () {
        $(".btn.sendreadings").removeClass("loading");
        $(".btn.sendreadings i").show();
    });
}

function showModal(title, content)
{
    $('#modalTemplate-Title').text(title);
    $('#modalTemplate-Body').text(content);
    $('#modalTemplate').modal('show');

}

function hideModal() {
    $('#modalTemplate').modal('hide');
    $('#modalTemplate-Body').text('');
}

function clickableRowOnClick(){
    window.location = $(this).data('href');
    return false;
}