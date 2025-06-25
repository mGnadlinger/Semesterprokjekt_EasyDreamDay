isLoggedIn();

// Prüft, ob Benutzer eingeloggt ist
function isLoggedIn() {
    fetch('./db/loggedIn.php')
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn === true) {
                document.getElementById('loginStatus').innerHTML = `<p onclick="logOut()">Abmelden</p>`;
            } else {
                document.getElementById('loginStatus').innerHTML = `<p onclick="login()">Anmelden</p>`;
            }
        })
        .catch(error => {
            console.error("Fehler:", error);
        });
}

function login() {
    window.location.href = "pages/profil.html";
}

function logOut() {
    fetch('./db/logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.code === 200) {
                localStorage.removeItem('type');
                document.getElementById('loginStatus').innerHTML = `<p onclick="login()">Anmelden</p>`;
                isLoggedIn();
            }
        })
        .catch(error => {
            console.error("Fehler:", error);
        });
}
