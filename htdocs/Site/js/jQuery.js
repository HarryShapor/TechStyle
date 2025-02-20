$(document).ready(function() {
    // Открытие модального окна
    $("#openModal").click(function() {
        $("#myModal").show();
    });

    // Закрытие модального окна
    $(".close").click(function() {
        $("#myModal").hide();
    });

    // Закрытие модального окна при клике вне его
    // $(window).click(function(event) {
    //     if ($(event.target).is("#myModal")) {
    //         $("#myModal").hide();
    //     }
    // });
    
    $(".inBasket").click(function() {
        $("#basketForm").show();
        // $('body').css('filter','blur(10px)');
    });

    // Закрытие модального окна
    $(".close").click(function() {
        $("#basketForm").hide();
    });

    // Закрытие модального окна при клике вне его
    $(window).click(function(event) {
        if ($(event.target).is("#basketForm")) {
            $("#basketForm").hide();
        }
    });

    $('#registrationForm').on('submit', function(e) {
        // e.preventDefault();
        let isValid = true;
        const name = $('#name').val();
        const email = $('#email').val();
        const password = $('#password').val();
        if (name.length < 1) {
            alert('Имя должно содержать не менее 1 символа.');
            $('#name').css('border-color: red;')
            isValid = false;
        }
        // Проверка на корректность электронной почты
        if (!validateEmail(email)) {
            alert('Введите корректный адрес электронной почты.');
            $('#email').css('border-color: red;')
            isValid = false;
        }

        // Проверка на длину пароля
        if (password.length < 6) {
            alert('Пароль должен содержать не менее 6 символов.');
            $('#password').css('border-color: red;')
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault(); // Отменяем отправку формы
        }
    });

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});

