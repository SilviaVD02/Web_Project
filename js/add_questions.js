

document.addEventListener('DOMContentLoaded', function () {

    var form = document.getElementById('csvForm');

    form.addEventListener('submit', async function () {

        event.preventDefault();

        const formData = new FormData();

        const username = sessionStorage['username'];

        formData.append('username', username);
        formData.append('csvFile', document.querySelector('input[type="file"]').files[0]);

        try {
            response = await fetch('../php/import.php', {
                method: 'POST',
                body: formData
            });

            data = await response.json();

            if (data.success) {
                alert('Файлът беше успешно качен и въпросите бяха успешно записани!');
            } else {
                alert('Възникна грешка: ' + data.message);
            }
        }
        catch (error) {
            alert('Възникна грешка: ' + error.message);
        }




    });
   
      

});

