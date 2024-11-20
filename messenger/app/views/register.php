<?php 

    // session_start();
    // unset($_SESSION['errors']);
    if(isset($_SESSION['user'])){
        header("location: /chat");
    }
?>

    <div class="wrapper">
        <section class="form register">
        <header>Регистрация в чате</header>
        <?php 
            if(isset($_SESSION['errors'])) {
                echo ' <div class="error-text">' . $_SESSION['errors'] . '</div>';
            }
        ?>

        <form action="/user/register" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="field input">
                <label>Имя пользователя</label>
                <input type="text" name="nickname" required>
            </div>
            <div class="field input">
                <label>Логин (адрес эл.почты)</label>
                <input type="text" name="email" required>
            </div>
            <div class="field input">
                <label>Пароль</label>
                <input type="password" name="password" required>
                <i class="fas fa-eye"></i>
            </div>
            <div class="field input">
                <label>Подтверждение пароля</label>
                <input type="password" name="password_confirm" required>
                <i class="fas fa-eye"></i>
            </div>
            <div class="field image">
                <label>Загрузить аватар</label>
                <input type="file" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg">
            </div>
            <div class="field button">
                <button type="submit" id="register-btn">Зарегистрироваться</button>
            </div>

            <p class="msg none"></p>

        </form>
        <div class="link">Уже есть учетная запись? <a class="login_link" href="login">Войти</a></div>
        </section>
    </div>

