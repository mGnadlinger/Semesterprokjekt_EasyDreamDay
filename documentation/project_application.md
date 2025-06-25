# Projektantrag Sommersemesterprojekt Medientechnik

## Projektname

**EasyDreamDay**

Eine Website zum Planen der perfekten Traumhochzeit für das Brautpaar oder die, die die Hochzeit planen. Es können die
Hochzeitsinfos eingetragen, Einladungen verschickt, Wunschlisten erstellt und Fotos der Hochzeit geteilt werden.

## Funktionen die implementiert werden

- Hochzeitsinformationen anzeigen
- Gäste Einladungen mit Zu- und Absagen
- Geschenk und Wunschlisten
- Kalender mit Checklisten und Erinnerungen
- Fotos miteinander teilen

## USP

- Planung von Anfang bis zum Ende einer Hochzeit
- Viele Interaktionsmöglichkeiten für Gäste und den Planer der Hochzeit
- Die Nutzung der Website ist für Gäste optional, damit Menschen ohne technische Affinität keine Probleme haben
- Reduziert den Planungsaufwand und erleichtert die Koordination mit Gästen
- Einsicht auf alle aktuellen Informationen der Hochzeit

## UI & UX | Projekt aus Sicht des Users

### Aufbau der Oberfläche, Inhalte, Interaktionsmöglichkeiten

#### Brautpaar bzw. die Planer der Hochzeit

- Gästeliste managen – Zu- und Absagen, Einladungen
- Wünsche posten – Geschenkwünsche und Ablaufwünsche
- Persönliche Erinnerungen und Checklisten
- Geben Informationen für die Hochzeit frei – Zeitplan

#### Hochzeitsgäste

- Anmelden mittels individuellem Code für die Hochzeit
- "Anmelden" und "Abmelden" von der Hochzeit
- Sehen Informationen zur Hochzeit
- Sehen Wünsche ein
- Fotos bei der Hochzeit mit allen teilen

## Coder Plan | Projekt aus Sicht des Entwicklers

### Technologien, technische Umsetzung, grobe Datenbankstruktur

#### Technologien:

**Frontend:**

- HTML/CSS/JavaScript

**Backend:**

- PHP (Server)

**Datenbank:**

- MySQL (relationale Datenbankstruktur)
- phpMyAdmin (Verwaltung der MySQL-Datenbanken)

### Sicherheit

Voraussetzung für Interaktionen und Einsicht der Hochzeitsdetails:

- Anmeldung mit individuellem Code für die Hochzeit und zusätzlich für jeden einzelnen Hochzeitsgast.
- Beides wird über die Einladung verschickt.

### Datenbankstruktur

**Tabellen:**

- User (UserId, Vorname, Nachname, Telefon, Email, Rolle, ...)
- Gästeliste(GästelisteId, UserId, HochzeitId)
- Hochzeit(HochzeitID, Datum, Uhrzeit, Ort, Person1, Person2, Gästelist, Zeitplan, ...)
- Geschenke(GeschenkId, Beschreibung, Name, ...)
- Checklisten bzw. Erinnerungen(...Id, Name, Beschreibung, Datum, Uhrzeit, ...)
- Fotos (FotoId, Link, Beschreibung, ...)
