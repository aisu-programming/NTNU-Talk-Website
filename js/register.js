function register() {
        
    var user_id = document.getElementById("user_id").value;
    var password = document.getElementById("password").value;
    var pwdcheck = document.getElementById("pwdcheck").value;
    var nickname = document.getElementById("nickname").value;

    if (user_id.length != 9) {
        alert("The length of User ID should be 9!");
        return;
    }

    if (password != pwdcheck) {
        alert("Password not same!");
        return;
    }

    post('api/register.php', {
        action: 'register',
        user_id: user_id,
        password: password,
        nickname: nickname,
        r: r
    }, function (response) {
        alert('Register succeed!');
        check('register');
    });
}