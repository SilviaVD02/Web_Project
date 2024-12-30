
document.addEventListener('DOMContentLoaded', async function () {

    const username = sessionStorage.getItem('username');

    
    if (username) {

        data = JSON.stringify({ username: username });

        response = await fetch('../php/get_reviews.php', {
            method: 'POST',
            body: data,
        })


        reviews = await response.json();

        const reviewContainer = document.getElementById('reviews_table');

        reviewContainer.innerHTML = '';
        
        if (!reviews.empty) {

            const table = document.createElement('table');
            table.border = 1;

            const headers = [
                'Дата на рецензия', 'Ревю ID', 'Въпрос', 'ФН-въпрос-автор', 'ФН-рецензент',
                'Ревю', 'Оценка'
            ];

            const headerRow = document.createElement('tr');

            headers.forEach(header => {
                const th = document.createElement('th');
                th.textContent = header;
                headerRow.appendChild(th);
            });
            table.appendChild(headerRow);


            reviews.forEach(review => {
                   
                
                const values = [
                    review.date, review.r_id, review.question, review.creator_fn,
                    review.reviewer_fn, review.review, review.rating
                ];

                const valueRow = document.createElement('tr');

                values.forEach(value => {
                    var q_Number = 1;

                    if (value == review.question) {

                        const td = document.createElement('td');
                        td.innerHTML = `
                                <p class="q"> Въпрос ${q_Number}: ${review.question}</p >
                                <p class="q">Отговор 1 - ${review.opt1}</p>
                                <p class="q">Отговор 2 - ${review.opt2}</p>
                                <p class="q">Отговор 3 - ${review.opt3}</p>
                                <p class="q">Отговор 4 - ${review.opt4}</p>
                                <p class="q">Верен отговор - ${review.answer}</p>
                                <p class="q">Ниво на трудност - ${review.difficulty}</p>
                                <p class="q">Забележка от автора - ${review.note}</p>

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

                reviewContainer.appendChild(table);
            });

        } else {
            reviewContainer.innerHTML = `<h3> Няма ревюта за показване!<h3>`;
        }

    } else {
        console.error('Username not found in session storage');
    }

});


    
