# Datenbankstruktur

User (Benutzer)

    UserId (PK)

    Vorname

    Nachname

    Telefon

    Email

    Rolle (Planer, Gast)

    PasswortHash

Hochzeit

    HochzeitId (PK)

    Datum

    Uhrzeit

    Ort1

    Ort2

    Trauung 

    Partner1Id (FK → Gast)

    Partner2Id (FK → Gast)

Gäste

    GastId (PK)

    HochzeitId (FK → Hochzeit)

    UserId (FK → User)

    Vorname

    Nachname

    Telefon

    Email

    Zusatztext 

    Zusage 

    GästegruppeId (FK → Gästegruppe, falls in Gruppe)

Gästegruppe (Für Gruppierungen wie Familien)

    GästegruppeId (PK)

    HochzeitId (FK → Hochzeit)

Gästegruppe_Mitglied

    GästegruppeId (FK → Gästegruppe)
    
    GastId (FK → Gäste)

Ablauf

    AblaufId (PK)

    HochzeitId (FK → Hochzeit)

    Uhrzeit

    Programmpunkt   

    Treffpunkt

ToDos

    ToDoId (PK)

    HochzeitId (FK → Hochzeit)

    Name

    Beschreibung

    Datum

    Uhrzeit

Geschenke

    GeschenkId (PK)

    HochzeitId (FK → Hochzeit)

    Beschreibung

    Name

    Geschenke_ReservierungId

Geschenke_Reservierung

    Geschenke_ReservierungId

    GeschenkId

    GastId

Fotos

    FotoId (PK)

    HochzeitId (FK → Hochzeit)

    Link

    Beschreibung