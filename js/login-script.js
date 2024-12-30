var role;

document.getElementById('login-form').addEventListener('submit', async function (event) {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    let isValid = validateCredentials(username, password);

    if (isValid) {

        let isRegistered = await validateRegistration(username, password);

        if (isRegistered) {

            if (role == 1) {

                sessionStorage.setItem("username", username);
                window.location.href = '../html/student_index.html';

            } else if (role == 2) {

                sessionStorage.setItem("username", username);
                window.location.href = '../html/teacher_index.html';
            } else {

                alert("Невалидна роля!");
                window.location.href = '../html/login.html';
            }

        } else {
            alert("Нерегистриран потребител!");
        }
    }
});

function validateCredentials(username, password) {
    const userMinLen = 6;
    const userMaxLen = 12;

    if (username.length < userMinLen || username.length > userMaxLen) {

        alert("Невалидни входни данни");
        window.location.href = '../html/login.html';
    }

    const passwordPattern = /^(?=.*[a-zа-я])(?=.*[A-ZА-Я])(?=.*[0-9])[A-Za-z0-9а-яА-Я]{10,}$/;
    const passMinLen = 10;

    if ((!passwordPattern.test(password)) || password.length < passMinLen) {

        alert("Невалидни входни данни");
        window.location.href = '../html/login.html';
    }

    return true;

}

async function validateRegistration(username, password) {

    const data = JSON.stringify({ username: username, password: password });

    /*try {*/
        const response = await fetch('../php/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: data
        });

    const responseJSON = await response.json();

        if (responseJSON.success) {
            role = responseJSON.role;
            return true;

        } else {
            return false;
        }

    //} catch (error) {
    //    console.error('There was a problem with the fetch operation:', error);
    //}
}


