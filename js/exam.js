function date_time_now() {
    const now = new Date();
    const options = {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false 
    };
    return now.toLocaleString('en-GB', options);

}

document.addEventListener('DOMContentLoaded', async function () {

    current_user = sessionStorage['username'];

    const data = JSON.stringify({ username: current_user });

    const response = await fetch('../php/get_fn.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: data
    });

    const response_data = await response.json();
  
    const select = document.getElementById('studentNumber');

    response_data.forEach(number => {
        const option = document.createElement('option');
        option.value = number;
        option.textContent = number;
        select.appendChild(option);
    });
    

});

option = document.getElementById('selectForm');

option.addEventListener('submit', async function () {

    event.preventDefault();

    const studentNumber = document.getElementById('studentNumber').value;

    if (studentNumber) {

        const data = JSON.stringify({ studentFN: studentNumber });

        const response = await fetch('../php/get_questions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: data
        });

        response_data = await response.json();

        const container = document.getElementById('testForm');
        container.innerHTML = '';
        q_Number = 1;

        response_data.questions.forEach(question => {

            questionElement = document.createElement('section');
            questionElement.innerHTML = `
                        
                        <h3>Въпрос ${q_Number}: ${question.question}</h3>
                        <p>Ниво на трудност - ${question.difficulty}</p>
                        <p><input type="radio" name="ans${q_Number}" value="${question.opt1}" required> ${question.opt1}<p>
                        <p><input type="radio" name="ans${q_Number}" value="${question.opt2}" required> ${question.opt2}<p>
                        <p><input type="radio" name="ans${q_Number}" value="${question.opt3}" required> ${question.opt3}<p>
                        <p><input type="radio" name="ans${q_Number}" value="${question.opt4}" required> ${question.opt4}<p>
                        
                    `;
            container.appendChild(questionElement);
            q_Number++;

        });

        containerSubmit = document.createElement('p');
        containerSubmit.innerHTML = `
                        <button type="submit">Завърши тест</button>`;
        container.appendChild(containerSubmit);


    }

    test = document.getElementById('testForm');

        test.addEventListener('submit', async function () {

            event.preventDefault();

            date_test = date_time_now();

            test_questions = response_data.questions;
            examinee = sessionStorage['username'];

            var sections = document.getElementsByTagName('section');

            testData = [];

            for (var i = 0; i < sections.length; i++) {

                var examinee_answer = document.querySelector(`input[name="ans${i + 1}"]:checked`).value;
                var answer_res = 0;

                if (examinee_answer == response_data.questions[i]['answer']) {

                    answer_res = 1;

                } 

                var question = response_data.questions[i]['question'];

                const test = {
                    question: question,
                    examinee: examinee,
                    date: date_test,
                    ans: answer_res
                };

                testData.push(test);
            }

            const testDataJSON = JSON.stringify(testData);


            const response = await fetch('../php/save_tests.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: testDataJSON
            });

            final_response = await response.json();

            if (final_response.success) {
                alert('Тестът беше успешно изпратен!');

                window.location.href = '../html/exam.html';

            } else {
                alert('Възникна грешка! Тестът не беше запазен!');
            }

        });

});    
