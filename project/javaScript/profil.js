let profilContent = document.querySelector('.profilContent');

isLoggedIn();

// Prüft, ob Benutzer eingeloggt ist
function isLoggedIn() {
    fetch('../db/loggedIn.php')
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn === true) {
                document.getElementById('loginStatus').innerHTML = `<p onclick="logOut()">Abmelden</p>`;
                if (localStorage['type'] === 'Guest') {
                    guestProfil();
                } else if (localStorage['type'] === 'Planner') {
                    plannerProfil();
                }
            } else {
                profilContent.style.background = 'url("../images/Login/rings.jpg")';
                profilContent.style.backgroundSize = 'cover';
                profilContent.innerHTML = `
                    <div class="registrationLoginBox">
                        <h2>Login</h2>
                        <form action="../db/login.php" method="POST">
                            <input id="login-username" name="login-username" placeholder="Benutzername" type="text" value="">
                            <div id="error-login-username"></div><br>
                            <input id="login-password" name="login-password" placeholder="Passwort" type="password" value="">
                            <div id="error-login-password"></div><br><br>
                            <button name="submit" onclick="loginAcc()" type="button">Login</button>
                        </form>
                        <div id="errorLogin"></div>
                        <p>Du hast noch keinen Account – <a href="registration_guest.html">Registrieren als Hochzeitsgast</a></p>
                    </div>
                `;
            }
        })
        .catch(error => {
            profilContent.innerHTML = "<h2>Fehler beim Laden Login</h2>";
            console.error("Fehler:", error);
        });
}

function logOut() {
    fetch('../db/logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.code === 200) {
                localStorage.removeItem('type');
                document.getElementById('loginStatus').innerHTML = `<p>Anmelden</p>`;
                isLoggedIn();
            }
        })
        .catch(error => {
            profilContent.innerHTML = "<h2>Fehler beim Logout</h2>";
            console.error("Fehler:", error);
        });
}

// Login-Funktion
function loginAcc() {
    const formData = new FormData(document.querySelector('form'));
    fetch('../db/login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (!data.loggedIn) {
                document.getElementById("error-login-username").innerHTML = data.errors[0];
                document.getElementById("error-login-password").innerHTML = data.errors[1];
            }

            localStorage['type'] = data.data;
            document.getElementById('loginStatus').innerHTML = `<p onclick="logOut()">Abmelden</p>`;

            if (data.data === 'Guest') {
                guestProfil();
            } else if (data.data === 'Planner') {
                plannerProfil();
            }
        })
        .catch(error => {
            document.querySelector('#errorLogin').innerHTML = "<h2>Fehler beim Laden Login</h2>";
            console.error("Fehler:", error);
        });
}

// Profil für Gast anzeigen
function guestProfil() {
    fetch(`../db/guestProfil.php`)
        .then(response => response.json())
        .then(data => {
            let schedulePoints = '';
            for (let i = 0; i < data.data.weddingSchedule.length; i++) {
                const point = data.data.weddingSchedule[i];
                schedulePoints += `
                    <p>
                        <strong style="color: white">${point.EventName}</strong><br>
                        ${point.Time} Uhr<br>
                        ${point.MeetingPoint}
                    </p>
                `;
            }

            let giftPoints = '';
            for (let i = 0; i < data.data.weddingGift.length; i++) {
                const gift = data.data.weddingGift[i];
                const isReserved = gift.IsReservedByUser === 1;
                const imgOpacity = isReserved ? '1' : '0';
                const clickHandler = isReserved ? `unchooseGift(this, ${gift.GiftId})` : `chooseGift(this, ${gift.GiftId})`;

                giftPoints += `
                    <div class="wish-item" onclick="${clickHandler}">
                        <div class="wish-icon">
                            <img src="../images/icons/prufen.png" alt="checkmark" style="opacity: ${imgOpacity};">
                        </div>
                        <div class="wish-name">${gift.Name}</div>
                        <div><p><strong>${gift.ReservationCount}</strong>x ausgewählt</p></div>
                    </div>
                `;
            }

            let html = `
                <div id="profilWeddingInformation">
                    <h2>${data.data.partner1} & ${data.data.partner2}</h2>
                    <div class="row">
                        <div class="informationsboxen">${data.data.weddingTime}</div>
                        <div class="informationsboxen">${data.data.weddingStyle}</div>
                    </div>
                    <div class="row">
                        <div id="informationDate"><p>${data.data.weddingDate}</p></div>
                    </div>
                    <div class="row">
                        <div class="informationsboxen">${data.data.weddingDresscode}</div>
                        <div class="informationsboxen">${data.data.weddingLocation}</div>
                    </div>
                </div>
            `;

            const today = new Date();
            const todayFormatted = normalizeDate(formatDateToDDMMYYYY(today));
            const weddingDate = normalizeDate(data.data.weddingDate);

            if (weddingDate === todayFormatted) {
                html += `
                    <div id="pictureGallery">
                        <h2>Gemeinsame Fotogalerie</h2>
                        <a href="pictureGallery.html"><div>Galerie ansehen</div></a>      
                    </div>`;
            }

            html += `
                <div id="schedule">
                    <h2>Ablauf</h2>
                    <div class="row">
                        <div id="verticalLine" style="width: 1px; background-color: black;"></div>
                        <ul>${schedulePoints}</ul>
                    </div>
                </div>
                <div id="wishes">
                    <h2>Wünsche</h2>
                    <div id="wishlist">${giftPoints}</div>
                </div>
            `;

            profilContent.style.background = 'white';
            profilContent.innerHTML = html;
        })
        .catch(error => {
            console.error("Fehler:", error);
            profilContent.innerHTML = "<div><h2>Fehler beim Laden</h2></div>";
        });
}

function chooseGift(icon, giftId) {
    fetch(`../db/chooseGift.php?giftId=${giftId}&action=add`)
        .then(response => response.json())
        .then(data => {
            if (data.code === 200) {
                icon.onclick = () => unchooseGift(icon, giftId);
                guestProfil();
            }
        })
        .catch(error => {
            profilContent.innerHTML = "<h2>Fehler beim Hinzufügen des Geschenks</h2>";
            console.error("Fehler:", error);
        });
}

function unchooseGift(icon, giftId) {
    fetch(`../db/chooseGift.php?giftId=${giftId}&action=remove`)
        .then(response => response.json())
        .then(data => {
            if (data.code === 200) {
                icon.onclick = () => chooseGift(icon, giftId);
                guestProfil();
            }
        })
        .catch(error => {
            profilContent.innerHTML = "<h2>Fehler beim Entfernen des Geschenks</h2>";
            console.error("Fehler:", error);
        });
}

// Profil für Planer anzeigen
function plannerProfil() {
    fetch(`../db/plannerProfil.php`)
        .then(response => response.json())
        .then(data => {
            let schedulePoints = '';
            for (let i = 0; i < data.data.weddingSchedule.length; i++) {
                const point = data.data.weddingSchedule[i];
                schedulePoints += `
                    <p>
                        <strong style="color: white">${point.EventName}</strong><br>
                        ${point.Time} Uhr<br>
                        ${point.MeetingPoint}
                    </p>
                `;
            }

            let guestListHtml = '';
            for (let i = 0; i < data.data.weddingGuests.length; i++) {
                const guest = data.data.weddingGuests[i];
                if (guest.AdditionalText !== 'Braut' && guest.AdditionalText !== 'Braeutigam') {
                    const backgroundStyle = guest.RSVP === 'Yes' ? 'background-color: #E1C3C5;' : '';
                    guestListHtml += `
                        <div class="guest-card" style="${backgroundStyle}" onclick="guestAttending(${guest.GuestId}, this)">
                            <h4>${guest.FirstName} ${guest.LastName}</h4>
                            ${guest.AdditionalText ? `<p>${guest.AdditionalText}</p>` : ''}
                        </div>
                    `;
                }
            }

            let todoPoints = '';
            for (let i = 0; i < data.data.todos.length; i++) {
                const todo = data.data.todos[i];
                todoPoints += `
                    <div class="wish-item" onclick="toggleTodoDone(this, ${todo.ToDoId})">
                        <div class="wish-icon">
                            <img src="../images/icons/prufen.png" alt="ToDo" style="opacity: ${todo.Done == 1 ? '1' : '0'};">
                        </div>
                        <div class="wish-name">${todo.Name}</div>
                        <div><p>${todo.Date} – ${todo.Time} Uhr</p></div>
                    </div>
                `;
            }

            let html = `
                <div id="profilWeddingInformation">
                    <h2>Deine Hochzeit</h2>
                    <div class="row">
                        <div class="informationsboxen">${data.data.weddingTime}</div>
                        <div class="informationsboxen">${data.data.weddingStyle}</div>
                    </div>
                    <div class="row">
                        <div id="informationDate"><p>${data.data.weddingDate}</p></div>
                    </div>
                    <div class="row">
                        <div class="informationsboxen">${data.data.weddingGuestNumber} Gäste</div>
                        <div class="informationsboxen">${data.data.weddingLocation}</div>
                    </div>
                </div>
            `;

            const today = new Date();
            const todayFormatted = normalizeDate(formatDateToDDMMYYYY(today));
            const weddingDate = normalizeDate(data.data.weddingDate);

            if (weddingDate === todayFormatted) {
                html += `
                    <div id="pictureGallery">
                        <h2>Gemeinsame Fotogalerie</h2>
                        <a href="pictureGallery.html"><div>Galerie ansehen</div></a>      
                    </div>`;
            }

            html += `
                <div id="schedule">
                    <h2>Ablauf</h2>
                    <div class="row">
                        <div id="verticalLine" style="width: 1px; background-color: black;"></div>
                        <ul>${schedulePoints}</ul>
                    </div>
                </div>
                <div id="guests">
                    <div id="invitationRow">
                        <div id="loader" style="display: none; text-align: center;"><p>Einladungskarten werden erstellt...</p></div>
                        <button onclick="sendInvitation()"><p>Lade Gästeeinladungskarten herunter</p></button>
                    </div>
                    <h2>Deine Gäste</h2>
                    <div class="legend">
                        <div class="legend-item">
                            <div class="legend-color yes"></div>
                            <span>Zusage</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color pending"></div>
                            <span>Noch am Überlegen</span>
                        </div>
                    </div>
                    <div id="guestList">${guestListHtml}</div>
                </div>
                <div id="plannerTodos">
                    <h2>ToDos</h2>
                    <div id="todoCalendar"></div>
                    <div id="wishlist">${todoPoints}</div>
                </div>
            `;

            profilContent.style.background = 'white';
            profilContent.innerHTML = html;

            new Calendar({
                id: "#todoCalendar",
                theme: "basic",
                calendarSize: "large",
                customMonthValues: [
                    "Januar", "Februar", "März", "April", "Mai", "Juni",
                    "Juli", "August", "September", "Oktober", "November", "Dezember"
                ],
                customWeekdayValues: ["Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"]
            });
        })
        .catch(error => {
            console.error("Fehler:", error);
            profilContent.innerHTML = "<div><h2>Fehler beim Laden</h2></div>";
        });
}

function toggleTodoDone(element, todoId) {
    fetch(`../db/toggleTodo.php?todoId=${todoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.code === 200) {
                plannerProfil();
            }
        })
        .catch(error => {
            console.error("Fehler beim ToDo-Status ändern:", error);
        });
}

function normalizeDate(dateString) {
    return dateString.replace(/\s*\.\s*/g, ".").trim();
}

function formatDateToDDMMYYYY(date) {
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Monate: 0–11
    const year = date.getFullYear();
    return `${day}.${month}.${year}`;
}

function sendInvitation() {
    const loader = document.getElementById('loader');
    loader.style.display = 'block'; // Ladeanzeige einblenden

    fetch('../db/generateGuestList.php', {
        method: 'POST'
    })
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'hochzeitsgaeste.pdf';
            document.body.appendChild(a);
            a.click();
            a.remove();
        })
        .catch(error => {
            console.error('Fehler beim Erstellen:', error);
            alert("Es gab ein Problem beim Erstellen der Datei.");
        })
        .finally(() => {
            loader.style.display = 'none';
        });
}


function guestAttending(guestId, element) {
    fetch(`../db/toggleGuest.php?guestId=${guestId}`)
        .then(response => response.json())
        .then(data => {
            if (data.code === 200) {
                if (data.response === 'Pending') {
                    element.style.background = '#FFFFFF';
                } else if (data.response === 'Yes') {
                    element.style.background = '#E1C3C5';
                }
            }
        })
        .catch(error => {
            console.error("Fehler beim Gastzusagen-Status ändern:", error);
        });
}

