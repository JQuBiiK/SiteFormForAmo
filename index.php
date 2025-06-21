<?php $started = time(); ?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Форма отправки данных в Amo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <form id="orderForm" autocomplete="off">
        <div class="form__item">
            <label><span>Имя</span>
                <input name="name"  type="text"  placeholder="Антон" required>
            </label>
        </div>
        <div class="form__item">
            <label><span>Почта</span>
                <input name="email" type="email" placeholder="mail@mail.ru" required>
            </label>
        </div>
        <div class="form__item">
            <label><span>Телефон</span>
                <input name="phone" type="tel"   placeholder="+7(999)999-99-99" required>
            </label>
        </div>
        <div class="form__item">
            <label><span>Цена</span>
                <input name="price" type="text"  placeholder="3000" required>
            </label>
        </div>

        <input type="hidden" name="started" value="<?= $started ?>">

        <button>Отправить</button>
    </form>
</main>

<script>
    document.getElementById('orderForm').addEventListener('submit', async e => {
        e.preventDefault();

        try {
            const body = new FormData(e.target);
            const request = await fetch('lead.php', { method: 'POST', body });
            const res = await request.json();

            if (res.ok) {
                alert('Спасибо! Заявка создана.');
                e.target.reset();
            } else {
                throw new Error(res.message || 'Ошибка сервера');
            }
        } catch (err) {
            alert('Что-то пошло не так');
            console.error(err);
        }
    });
</script>
</body>
</html>
