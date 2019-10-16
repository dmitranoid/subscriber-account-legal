// on startup

$('.clickable-row').on('click', clickableRowOnClick);

//   functions

function sendreadings() {
    let readings = {};
    $(".table-readings input").each(function(index, el){
        $(el).attr("disabled", true);
        readings[$(el).attr("name")] = $(el).val();
    });

    console.log(readings);
    $(".btn.sendreadings").addClass("loading");
    $(".btn.sendreadings i").hide();
    $(".btn.sendreadings").attr("disabled", true);
    $.ajax({
        method: "post",
        url: "/contract/sendreadings",
        data: readings,
    }).done(function(data) {
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
    $('#modal-id .modal-title').text(title);
    $('#modal-id .content').text(content);
    $('#modal-id').addClass('active');

}

function hideModal() {
    $('#modal-id').removeClass('active');
    $('#modal-id .content').text('');
}

function clickableRowOnClick(){
    window.location = $(this).data('href');
    return false;
}