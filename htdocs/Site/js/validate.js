$(document).ready(function () {

    // Validate Username
    $("#usercheck").hide();
    let usernameError = true;
    $("#name").keyup(function () {
        validateUsername();
    });

    function validateUsername() {
        let usernameValue = $("#name").val();
        if (usernameValue.length === "") {
            $("#usercheck").show();
            $('#name').css({'border-color': 'red', 'border-radius': '12px', 'color':'#ffb5b5'});
            $('#name').text("Не может быть пустым!");
            usernameError = false;
            return false;
        } else if (usernameValue.length < 3 || usernameValue.length > 20) {
            $("#usercheck").show();
            $("#usercheck").html("**length of username must be between 3 and 20");
            $('#name').css({'border-color': 'red', 'border-radius': '12px', 'background-color':'#ffb5b5'});
            usernameError = false;
            return false;
        } else {
            $('#name').css({'border-color': '#4a6644', 'border-radius': '30px','background-color':'#63db7da9', 'color':'black', 'color':'black'});
            $("#usercheck").hide();
            usernameError = true;
        }
    }

    $('#submit').click(function (e) {
        e.preventDefault();
        const email = $('#email').val();
        const passwordValue = $("#password").val();
        const usernameValue = $("#name").val();
        if (passwordValue === '' && email === '' && usernameValue === ''){
            $("#passcheck").show();
            $("#passcheck").html("Input Field can not be Empty!!");
            $('#password').css({'border-color': 'red', 'border-radius': '12px', 'background-color':'#faf8e7', 'border-size': '5px'});
            passwordError = false;
            $('#invalid_email').text("Input Field can not be Empty!!");
            $('#invalid_email').css("color", "red");
            $('#email').text("Не может быть пустым!");
            $('#email').css({'border-color': 'red', 'border-radius': '12px', 'color':'red','background-color':'#faf8e7', 'border-size': '5px'});
            $("#usercheck").show();
            $('#name').css({'border-color': 'red', 'border-radius': '12px', 'color':'#ffb5b5', 'border-size': '5px'});
            $('#name').text("Не может быть пустым!");
            usernameError = false;
        }
        else if (passwordValue === '' && email === ''){
            $("#passcheck").show();
            $("#passcheck").html("Input Field can not be Empty!!");
            $('#password').css({'border-color': 'red', 'border-radius': '12px', 'background-color':'#faf8e7', 'border-size': '5px'});
            passwordError = false;
            $('#invalid_email').text("Input Field can not be Empty!!");
            $('#invalid_email').css("color", "red");
            $('#email').text("Не может быть пустым!");
            $('#email').css({'border-color': 'red', 'border-radius': '12px', 'color':'red','background-color':'#faf8e7', 'border-size': '5px'});
        }
        else if (passwordValue === '') {
            $("#passcheck").show();
            $("#passcheck").html("Input Field can not be Empty!!");
            $('#password').css({'border-color': 'red', 'border-radius': '12px', 'background-color':'#faf8e7', 'border-size': '5px'});
            $('#email').css({'border-color': '#4a6644', 'border-radius': '30px','background-color':'#63db7da9', 'color':'black', 'border-size': '2px'});
            $("#invalid_email").hide();
            passwordError = false;
            return false;
        }
        else if (passwordValue.length < 8 || passwordValue.length > 15) {
            $("#passcheck").show();
            $("#passcheck").html("**length of your password must be between 8 and 15");
            $("#passcheck").css("color", "red");
            $('#password').css({'border-color': 'red', 'border-radius': '12px', 'background-color':'#ffb5b5', 'border-size': '5px'});
            $('#email').css({'border-color': '#4a6644', 'border-radius': '30px','background-color':'#63db7da9', 'color':'black', 'border-size': '2px'});
            $("#invalid_email").hide();
            passwordError = false;
            return false;
        }
        else if (email === '') {
            $('#invalid_email').text("Input Field can not be Empty!!");
            $('#invalid_email').css("color", "red");
            $('#email').text("Не может быть пустым!");
            $('#email').css({'border-color': 'red', 'border-radius': '12px', 'color':'red','background-color':'#faf8e7', 'border-size': '5px'});
            $("#invalid_email").hide();
            return false;
        }
        else if (IsEmail(email) === false) {
            $('#invalid_email').text("Entered Email is not Valid!!");
            $('#invalid_email').css("color", "red");
            $('#email').css({'border-color': 'red', 'border-radius': '12px', 'background-color':'#ffb5b5','color':'black', 'border-size': '5px'});
            return false;
        }
        else {
            $('#email').css({'border-color': '#4a6644', 'border-radius': '30px','background-color':'#63db7da9', 'color':'black', 'border-size': '2px'});
            $('#password').css({'border-color': '#4a6644', 'border-radius': '30px','background-color':'#63db7da9', 'color':'black', 'border-size': '2px'});
            $('#name').css({'border-color': '#4a6644', 'border-radius': '30px','background-color':'#63db7da9', 'color':'black', 'border-size': '2px'});
            $("#invalid_email").hide();
        }
        return false; 
    });
    function IsEmail(email) {
        const regex =/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!regex.test(email)) {
            return false;
        }
        else {
            return true;
        }
    }

    // Validate Password
    $("#passcheck").hide();
    let passwordError = true;
    $("#password").keyup(function () {
        validatePassword();
    });
    function validatePassword() {
        let passwordValue = $("#password").val();
        if (passwordValue === '') {
            $("#passcheck").show();
            $("#passcheck").html("Input Field can not be Empty!!");
            $('#password').css({'border-color': 'red', 'border-radius': '12px', 'background-color':'#faf8e7'});
            passwordError = false;
            return false;
        }
        if (passwordValue.length < 8 || passwordValue.length > 15) {
            $("#passcheck").show();
            $("#passcheck").html("**length of your password must be between 8 and 15");
            $("#passcheck").css("color", "red");
            $('#password').css({'border-color': 'red', 'border-radius': '12px', 'background-color':'#ffb5b5'});
            passwordError = false;
            return false;
        } else {
            $("#passcheck").hide();
            $('#password').css({'border-color': '#4a6644', 'border-radius': '30px','background-color':'#63db7da9'});
            passwordError = true;
        }
    }
    // Submit button
    $("#submitbtn").click(function () {
        validateUsername();
        validatePassword();
        validateConfirmPassword();
        email.dispatchEvent(new Event('blur')); 
        
        if (
            usernameError &&
            passwordError &&
            confirmPasswordError &&
            emailError
        ) {
            return true;
        } else {
            return false;
        }
    });
});

