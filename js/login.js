function login() {
        
    var user_id = document.getElementById("user_id").value;
    var password = document.getElementById("password").value;

    post('api/login.php', {
        action: 'login',
        user_id: user_id,
        password: password,
        r: r
    }, function (response) {
        alert(response.result);
        location.reload();
    });
}