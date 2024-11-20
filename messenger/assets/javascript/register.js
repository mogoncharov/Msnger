$('#register-btn').click(function (e) {
    e.preventDefault();
    console.log(123);
    $(`input`).removeClass('error');

    let login = $('input[name="email"]').val(),
        password = $('input[name="password"]').val(),
        nickname = $('input[name="nickname"]').val(),
        password_confirm = $('input[name="password_confirm"]').val();

    let formData = new FormData();
    formData.append('login', login);
    formData.append('password', password);
    formData.append('password_confirm', password_confirm);
    formData.append('nickname', nickname);


    $.ajax({
        url: 'user/register',
        type: 'POST',
        dataType: 'json',
        processData: false,
        contentType: false,
        cache: false,
        data: formData,
        success (data) {

            if (data.status) {
                document.location.href = '/index.php';
            } else {

                if (data.type === 1) {
                    data.fields.forEach(function (field) {
                        $(`input[name="${field}"]`).addClass('error');
                    });
                }
                $('.msg').removeClass('none').text(data.message);
            }
        }
    });
});