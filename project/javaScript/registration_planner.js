let plannerRegistrationContent = document.querySelector('.plannerRegistrationContent');
isLoggedIn();

// Prüft, ob Benutzer eingeloggt ist
function isLoggedIn() {
    fetch('../db/loggedIn.php')
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn === true) {
                document.getElementById('loginStatus').innerHTML = `<p onclick="logOut()">Abmelden</p>`;
            }
        })
        .catch(error => {
            plannerRegistrationContent.innerHTML = "<h2>Fehler beim Laden Login</h2>";
            console.error("Fehler:", error);
        });
}

function logOut() {
    fetch('../db/logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.code === 200) {
                localStorage.removeItem('type');
                document.getElementById('loginStatus').innerHTML = `<a href="./profil.html"><p>Anmelden</p></a>`;
            }
        })
        .catch(error => {
            plannerRegistrationContent.innerHTML = "<h2>Fehler beim Logout</h2>";
            console.error("Fehler:", error);
        });
}

let selectedTodoDate = null; // global gespeichert

let calendar = new Calendar({
    id: "#calendar",
    theme: "basic",
    calendarSize: "large",
    customMonthValues: [
        "Januar", "Februar", "März", "April", "Mai", "Juni",
        "Juli", "August", "September", "Oktober", "November", "Dezember"
    ],
    customWeekdayValues: ["Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"],
    dateChanged: (date) => {
        selectedTodoDate =
            date.getFullYear() + '-' +
            String(date.getMonth() + 1).padStart(2, '0') + '-' +
            String(date.getDate()).padStart(2, '0');
    }
});

const todoBody = document.getElementById('todo-body');
if (todoBody) {
    todoBody.addEventListener('focusin', function (e) {
        if (e.target.tagName === 'INPUT' && e.target.name.startsWith('todo-date')) {
            if (selectedTodoDate) {
                e.target.value = selectedTodoDate;
            }
        }
    });
}

const popupClose = document.getElementById("popup-close");
if (popupClose) {
    popupClose.addEventListener("click", () => {
        document.getElementById("popup").style.display = "none";
    });
}

function addRowToSchedule() {
    const tbody = document.getElementById('schedule-body');
    const row = document.createElement('tr');

    ['schedule-time', 'schedule-program', 'schedule-meeting'].forEach(name => {
        const td = document.createElement('td');
        const input = document.createElement('input');
        input.name = `${name}[]`;
        input.type = name === 'schedule-time' ? 'time' : 'text';
        td.appendChild(input);
        row.appendChild(td);
    });

    tbody.appendChild(row);
}

function addPersonRowToGuest() {
    const tbody = document.getElementById('guest-body');
    const row = document.createElement('tr');

    ['email', 'firstname', 'lastname', 'note'].forEach(field => {
        const td = document.createElement('td');
        const input = document.createElement('input');
        input.name = `guest-${field}[]`;
        input.type = field === 'email' ? 'email' : 'text';
        td.appendChild(input);
        row.appendChild(td);
    });

    tbody.appendChild(row);
}

let groupCount = 0;

function addGroup() {
    const tmpl = document.getElementById('group-template').innerHTML;
    const html = tmpl.replace(/\{i\}/g, groupCount);
    const div = document.createElement('div');
    div.innerHTML = html;
    div.querySelector('.group-index').textContent = groupCount + 1;
    div.querySelector('.add-member-btn').addEventListener('click', () => addMember(div, groupCount));
    document.getElementById('groups-container').append(div);
    groupCount++;
}

function addMember(groupDiv, idx) {
    const list = groupDiv.querySelector('.member-list');
    const members = list.querySelectorAll('tr').length;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="email" name="guestgroup[${idx - 1}][members][${members}][email]" required /></td>
        <td><input type="text"  name="guestgroup[${idx - 1}][members][${members}][firstname]" required /></td>
        <td><input type="text"  name="guestgroup[${idx - 1}][members][${members}][lastname]" required /></td>
        <td><input type="text"  name="guestgroup[${idx - 1}][members][${members}][note]" /></td>
    `;
    list.append(row);
}

function addRowToWishes() {
    const tbody = document.getElementById('wish-body');
    const row = document.createElement('tr');
    const td = document.createElement('td');
    const input = document.createElement('input');

    input.type = 'text';
    input.name = 'wish[]';

    td.appendChild(input);
    row.appendChild(td);
    tbody.appendChild(row);
}

function addRowToToDo() {
    const tbody = document.getElementById('todo-body');
    const row = document.createElement('tr');

    ['todo-date', 'todo-time', 'todo-text'].forEach(name => {
        const td = document.createElement('td');
        const input = document.createElement('input');

        input.name = `${name}[]`;
        input.type = name === 'todo-date' ? 'date' : (name === 'todo-time' ? 'time' : 'text');

        if (name === 'todo-date' && selectedTodoDate) {
            input.value = selectedTodoDate;
        }

        td.appendChild(input);
        row.appendChild(td);
    });

    tbody.appendChild(row);
}

function createWedding() {
    const form = document.querySelector('form');
    const formData = new FormData(form);

    fetch('../db/registration_planner.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                document.querySelector('#errorPlanner').innerHTML = data.errors.join("<br>");
            }
            if (data.success) {
                window.location.href = "profil.html";
            }
        })
        .catch(error => {
            document.querySelector('#errorPlanner').innerHTML = "<h2>Fehler beim Laden. Überprüfe deine Eingaben</h2>";
            console.error("Fehler:", error);
        });
}
