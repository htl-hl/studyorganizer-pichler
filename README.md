[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/vuGKZSdL)
# Augabenstellung:
Jede Person soll sich registrieren können. Jede Person darf Aufgaben erstellen die einem Fach zugeordnet werden. 
Jeder User sieht nur seine eigenen Aufgaben. Jeder User kann aber seine eigenen Aufgaben auch bearbeiten.
Das Faälligkeitsdatum der Aufgaben soll je nach Abstufung folgende Farbe haben:
    - größer gleich 2 Wochen => keine Farbe
    - kleiner 2 Wochen größer gleich 1 Woche => blau
    - kleiner 1 Woche => gelb
    - kleiner 1 Tag => rot
Jede Aufgabe soll auch als erledigt gespeichert werden können ist aber dann auch nicht mehr veränderbar.

Admins dürfen Fächer und Lehrer anlegen und entsprechend zuordnen. Lehrer können auf Inaktiv gesetzt werden aber nicht gelöscht.

Es muss in Yii2 umgesetzt werden.

# Ablauf/Abgabe
- Ein Datenbank Design soll erstellt werden
- Ein Mockup soll erstellt werden
- Erst nach Abnahme der beiden obigen Punkte darf mit dem Coden angefangen werden

## Hilfestellung
Entwirf und erstelle Tabellen. Überlege selbst, welche Datentypen sinnvoll sind.
- Fächer (Subjects): Sollte den Namen des Fachs speichern (z.B. „Mathe“).
- Hausaufgaben (Homework):
    - Muss Titel und Beschreibung enthalten.
    - Muss ein Fälligkeitsdatum haben.
    - Muss speichern können, ob die Aufgabe „erledigt“ ist oder nicht.
    - Wichtig: Stelle eine Verknüpfung her! Eine Hausaufgabe gehört immer zu einem Fach (1:n Beziehung).
- Lehrer
    - Jeder Lehrer braucht einen Namen und eine ID
    - Jeder Lehrer muss mindestens einem Fach zugeordnet werden (1:n)
    - Jeder Lehre soll inaktiv gesetzt werden können
- User
    - Jeder User benötigt eine ID einen Usernamen und das Passwort soll gehashed  in der Datenbank Hinterlegt werden.
Admins und User sind in derselben Tabelle enthalten!!

Die Admin Übersicht kann relativ einfach mit GII erstellt werden und genügt auch als Eingabe.
   
