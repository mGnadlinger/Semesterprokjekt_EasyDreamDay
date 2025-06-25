let guestRegistrationContent = document.querySelector('.guestRegistrationContent');
isLoggedIn();

// Prüft, ob der Benutzer eingeloggt ist
function isLoggedIn() {
    fetch('../db/loggedIn.php')
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn === true) {
                document.getElementById('loginStatus').innerHTML = `<p onclick="logOut()">Abmelden</p>`;
            }
        })
        .catch(error => {
            guestRegistrationContent.innerHTML = "<h2>Fehler beim Laden Login</h2>";
            console.error("Fehler:", error);
        });
}

// Logout-Funktion
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
            guestRegistrationContent.innerHTML = "<h2>Fehler beim Logout</h2>";
            console.error("Fehler:", error);
        });
}

// Benutzerregistrierung
function createAcc() {
    const formData = new FormData(document.querySelector('form'));

    fetch('../db/registration_guest.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            // Fehler ausgeben, falls vorhanden
            if (!data.success) {
                document.getElementById("error-guest-wedding-code").innerHTML = data.errors[0];
                document.getElementById("error-guest-code").innerHTML = data.errors[1];
                document.getElementById("error-guest-username").innerHTML = data.errors[2];
                document.getElementById("error-guest-password").innerHTML = data.errors[3];
                document.getElementById("error-guest-password2").innerHTML = data.errors[4];
            }

            // Bei Erfolg weiterleiten
            if (data.success) {
                window.location.href = "profil.html";
            }
        })
        .catch(error => {
            document.querySelector('#errorGuest').innerHTML = "<h2>Fehler beim Laden</h2>";
            console.error("Fehler:", error);
        });
}
