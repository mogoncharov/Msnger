<?php 
    // session_start();
    // unset($_SESSION);
    // var_dump($_SESSION); die();
    if(isset($_SESSION['user'])){
        header("location: /chat");
    }
?>

    <div class="wrapper">
        <section class="form login">
            <header>Авторизация</header>
            <?php 
                if(isset($_SESSION['errors'])) {
                    echo ' <div class="error-text">' . $_SESSION['errors'] . '</div>';
                }
            ?>
            <form action="/user/login" method="POST" enctype="multipart/form-data" autocomplete="off">
                <div class="field input">
                <label>Логин(почта)</label>
                <input type="text" name="email" required>
                </div>
                <div class="field input">
                <label>Пароль</label>
                <input type="password" name="password" required>
                <i class="fas fa-eye"></i>
                </div>
                <div class="field button">
                    <button type="submit" class="register-btn">Войти</button>

                </div>
            </form>
            <div class="link">Еще нет учетной записи? <a class="register_link" href="register">Зарегистрироваться</a></div>
        </section>
    </div>
    
