#####################################
#Chrissyx Homepage Scripts - Counter#
#####################################


Version: 2.0


Vorwort
Nach langer Zeit die Version 2.0 des Counters - komplett neu geschrieben! Alle Features sind nat�rlich weiterhin vorhanden, einzig
die Backup-Funktion arbeitet nun per E-Mail. Kein Einloggen mehr n�tig, um den Counter zu sichern - statt dessen wird einfach
bequem per Mail gesichert.


Vorraussetzungen
-PHP ab 4.3
-chmod und ggf. Mail f�higer Webspace


Installation
Die Installation ist gewohnt einfach: Lade in dem Ordner, wo die Webseite ist (auf welcher der Counter zum
Einsatz kommen soll), den Ordner "counter" samt Inhalt hoch. Rufe danach die "index.php" aus dem Ordner "counter"
auf und folge dann den Anweisungen.

WICHTIG: Die V2.0 ist NICHT abw�rtskompatibel zu allen Versionen davor! Falls Du also eine �ltere Version betreibst,
notiere dir den Counterstand und sichere (wenn vorhanden) die "ip.dat"-Datei aus dem "counter"-Ordner. Danach
deinstallieren, den "counter"-Ordner l�schen und auch den damals eingef�gten Code von deiner Seite entfernen! Bei
der Installation der V2.0 kannst Du dann deinen vorhandenen Counterstand wieder eingeben und wenn Du vorher die
"ip.dat" gesichert hattest, die Einstellung daf�r anpassen. Merke dir dabei den angegebenen Pfad und Dateinamen,
denn dort musst Du nach der Installation die "ip.dat" wieder hochladen und ggf. umbenennen.


FAQ
-Ich erhalte beim Aufruf die Meldung "ERROR: Datei/Ordner nicht gefunden!"?!?
Lies dir die Installationsanlietung hier genaustens durch!

-Ich erhalte beim Aufruf die Meldung "ERROR: Konnte keine Rechte setzen!"?!?
Setzte mit deinem FTP Programm per chmod Befehl die Rechte auf "775" f�r die/den angegebene Datei/Ordner.

-Kann man andere Bilder f�r die Zahlenausgabe nutzen?
Na klar, Du brauchst nur die vorhandenen PNG Bilder durch deine eigenen zu ersetzen. F�r jede Zahl ein eigenes Bild,
also die "0" muss "0.png" heissen, die "1" muss "1.png" heissen, usw. Die Zahlenbilder m�ssen alle im "counter"-Ordner
sein.


Credits
� 2004 - 2008 by Chrissyx
Powered by V4 Technology
http://www.chrissyx.de(.vu)/
http://www.chrissyx.com/