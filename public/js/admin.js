// on startup

$('clickable-row').on('click', clickableRowOnClick);

//   functions

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
    window.location = $(this).find("a").first().attr("href");
    return false;
}