# Connecting-Sports
Semester 3: Ein kleines soziales Netzwerk geschrieben im Zend Framework 2. Es beinhaltet eine Trennung zwischen nicht angemeldeten Nutzer, angemeldeten Nutzer und Administrator. Man kann sich für das Netzwerk registrieren und sein Profil mit Details bearbeiten. Außerdem ermöglicht Connecting Sports eine Teilname an Events und sich über Kommentare mit anderen Teilnehmern auszutauschen. Die Events kann jeder erstellen und ist ein Event vorbei, bewerten sich die Teilnehmer gegenseitig auf Anwesendheit und Sympathie, was wiederum in den Profilen in einer Statistik wiedergespiegelt wird.

## Einrichtung<br/>
Damit unsere Applikation läuft, müssen folgende Dinge gemacht werden:<br/>
1. Die Ordner \config, \module, \public übertragen in eine funktionstüchtige Zend Framework 2 Skeleton Applikation
2. Den Ordner \vendor\Hilfsfunktionen übertragen
3. Via sudo chmod der Applikation Schreibrechte im Ordner \public\upload\sportart für den Bilder-Upload geben
4. Die Datenbank aus dem .sql-File im Repository-Ordner \Datenbank und Konzept\Datenbank\ in die Datenbank "ConnectingSports" importieren
5. Die Konfiguration eventuell auf Ihre Datenbank anpassen in \SourceCode\config\autoload (global.php und database.local.php erstellen)

database.local.php Beispiel:<br/>
<?php<br/>
return array(<br/>
    'db' => array(<br/>
        'username' => 'root',<br/>
        'password' => '123',<br/>
    ),
);<br/>

##Vielen Dank!
