let pictureGalleryContent = document.querySelector('.pictureGalleryContent');
isLoggedIn();

if (localStorage['type'] === 'Planner') {
    document.getElementById('uploadForm').style.display = 'none';
}

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
            pictureGalleryContent.innerHTML = "<h2>Fehler beim Laden Login</h2>";
            console.error("Fehler:", error);
        });
}

function logOut() {
    fetch('../db/logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.code === 200) {
                localStorage.removeItem('type');
                window.location = "profil.html";
            }
        })
        .catch(error => {
            pictureGalleryContent.innerHTML = "<h2>Fehler beim Logout</h2>";
            console.error("Fehler:", error);
        });
}

showGallery();

function showGallery() {
    fetch('../db/pictureGallery.php')
        .then(response => response.json())
        .then(data => {
            let html = '';

            for (let i = 0; i < data.data.length; i++) {
                html += `
                    <div>
                        <img src="../${data.data[i].Link}" alt="${data.data[i].Description || 'Bild'}">
                    </div>
                `;
            }

            if (html === '') {
                const gallery = document.getElementById('gallery');
                gallery.style.justifyContent = 'center';
                gallery.innerHTML = `<div><p style="text-align: center">Noch keine Bilder hochgeladen</p></div>`;
            } else {
                const gallery = document.getElementById('gallery');
                gallery.style.justifyContent = 'start';
                gallery.innerHTML = html;
            }
        })
        .catch(error => {
            pictureGalleryContent.innerHTML = "<h2>Fehler beim Laden PictureGallery</h2>";
            console.error("Fehler:", error);
        });
}

function uploadImages() {
    const form = document.getElementById('uploadForm');
    const formData = new FormData(form);

    fetch('../db/imageupload.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('uploadStatus').innerHTML = `<p>${data.message}</p>`;
                showGallery();
            } else {
                document.getElementById('uploadStatus').innerHTML = `<p>${data.errors.join('<br>')}</p>`;
            }
            document.getElementById('fileToUpload').value = null;
        })
        .catch(error => {
            document.getElementById('uploadStatus').innerHTML = `<p>Fehler beim Upload. Bitte versuche es erneut.</p>`;
            console.error('Fehler:', error);
        });
}
