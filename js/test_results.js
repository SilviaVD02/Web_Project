
document.addEventListener('DOMContentLoaded', async function () {

    const username = sessionStorage.getItem('username');

    
    if (username) {

        data = JSON.stringify({ username: username });

     
        response = await fetch('../php/get_test_results.php', {
            method: 'POST',
            body: data,
        })

        tests = await response.json();

        const testContainer = document.getElementById('test_table');

        testContainer.innerHTML = '';
       
        if (!tests.empty) {
            const table = document.createElement('table');
            table.border = 1;

           
            const headers = [
                'Дата на решаване', 'Тестов отговор ID', 'Въпрос', 'ФН-въпрос-автор', 'ФН-решаващ', 'Отговорен правилно'
            ];

            const headerRow = document.createElement('tr');

            headers.forEach(header => {
                const th = document.createElement('th');
                th.textContent = header;
                headerRow.appendChild(th);
            });
            table.appendChild(headerRow);

            tests.forEach(test => {

                const values = [
                    test.date, test.t_id, test.question, test.creator_fn,
                    test.examinee_fn, test.t_answer
                ];


                const valueRow = document.createElement('tr');

                values.forEach(value => {
                    var q_Number = 1;

                    if (value == test.question) {

                        const td = document.createElement('td');
                        td.innerHTML = `
                                <p class="q"> Въпрос ${q_Number}: ${test.question}</p >
                                <p class="q">Отговор 1 - ${test.opt1}</p>
                                <p class="q">Отговор 2 - ${test.opt2}</p>
                                <p class="q">Отговор 3 - ${test.opt3}</p>
                                <p class="q">Отговор 4 - ${test.opt4}</p>
                                <p class="q">Верен отговор - ${test.answer}</p>
                                <p class="q">Ниво на трудност - ${test.difficulty}</p>
                                <p class="q">Забележка от автора - ${test.note}</p>

                            `;

                        q_Number++;
                        valueRow.appendChild(td)

                    } else {

                        
                            const td = document.createElement('td');
                            td.textContent = value;
                            valueRow.appendChild(td);
                                           
                        
                        
                    }


                });
                table.appendChild(valueRow);

                testContainer.appendChild(table);
            });

        } else {
            testContainer.innerHTML = `<h3> Няма тестови въпроси за показване!<h3>`;
        }

    } else {
        console.error('Username not found in session storage');
    }

});


    
