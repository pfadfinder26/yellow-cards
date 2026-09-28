# Cards 0.2.2

Macht Karten aus Seiten und ihren Einstellungen. Entwickelt von Liam Perlaki.

Eine Karte wird aus einer anderen Seite gebaut und nicht ein zweites Mal von Hand geschrieben: Titel,
Beschreibung und jede Einstellung der Seite, auf die sie zeigt. Das HTML kommt aus einem Layout, das
du selbst bestimmst, damit kann eine Karte alles sein, eine Stufenkachel, ein Neuigkeitenteaser, eine
Person.

## Wie man eine Erweiterung installiert

[ZIP-Datei herunterladen](https://github.com/pfadfinder26/yellow-cards/archive/refs/heads/main.zip) und in den Ordner `system/extensions` kopieren. [Mehr über Erweiterungen](https://github.com/annaesvensson/yellow-update).

## Wie man Karten macht

Eine Karte für eine Seite:

    [card /stufen/biber/]

Karten für alle Unterseiten einer Seite:

    [cards /stufen/]

Ohne Ort, oder mit `-`, ist die aktuelle Seite gemeint. `[cards]` macht eine Karte für jede
Unterseite der aktuellen Seite.

**Vorlagen:** das zweite Argument wählt das Layout `card-<vorlage>.html` im Ordner `system/layouts`,
voreingestellt ist `card-default.html`. Eigene Vorlagen kommen daneben, eine pro Kartenart:

    [cards /leitung/ person]

Themes können eigene Versionen mitbringen, `includeLayout` nimmt `<theme>-card-<vorlage>.html` vor
`card-<vorlage>.html`, wie bei jedem anderen Layout auch.

**Filter:** das dritte Argument behält nur die Seiten, deren Einstellung passt, geschrieben als
`einstellung:wert`, Groß- und Kleinschreibung egal. Die Einstellung auf der Seite darf eine Liste
sein, `Stufe: gusp, raro` passt zu `stufe:gusp` und zu `stufe:raro`:

    [cards /leitung/ person stufe:gusp]

Mehrere Filter dürfen aufeinander folgen, eine Seite muss zu allen passen:

    [cards /leitung/ person stufe:gusp funktion:gruppenleitung]

Ein `*` als Wert behält jede Seite, die diese Einstellung überhaupt hat,
`[cards /leitung/ person stufenleitung:*]` sammelt die Leitungen aller Stufen in einer Reihe. Mit `-`
als Vorlage bleibt die Standardvorlage und es wird trotzdem gefiltert, zum Beispiel
`[cards /leitung/ - stufe:gusp]`. Ein Filter mit leerem Wert behält die Seiten, die diese Einstellung
gar nicht haben, `[cards /leitung/ person stufe:]`. So gruppiert eine Seite nach einer Einstellung:
ein Block pro Wert, einer für den Rest.

**Optionen:** jedes Argument ohne Doppelpunkt geht an die Vorlage, die entscheidet, was es bedeutet,
zum Beispiel `[cards /stufen/ stufe link]`, dort macht die Vorlage der Beispielseite die ganze Karte
zum Link auf diese Seite, eine Mailadresse darin bleibt trotzdem anklickbar. Eine Option
behandelt die Erweiterung selbst: `unlisted` nimmt auch die Seiten mit `Status: unlisted` mit, so
kann eine Stufe, die nicht im Menü steht, trotzdem in einer Übersicht erscheinen.

**Seiten, die warten:** eine Seite, deren `Published`-Datum noch bevorsteht, bleibt bis dahin aus
den Kartenreihen. Die Option `scheduled` nimmt sie mit und stellt sie ans Ende der Reihe, das
nächste Datum zuerst, damit eine Reihe von Neuigkeiten von heute in das führt, was kommt.

**Eine Reihe, die scrollt:** die Option `scroll` kennzeichnet eine Reihe, die seitlich scrollt,
statt umzubrechen, `<div class="cards cards-scroll">`. Wie das aussieht, entscheidet das Theme.

**Versteckte Seiten:** eine Seite mit `Status: unlisted` steht nicht im Menü und in keiner
Kartenreihe, sie ist aber weiterhin erreichbar. So wird ein Ordner voller Seiten zu einer kleinen
Datenbank, zum Beispiel eine Seite pro Leiter*in, die auf mehreren anderen Seiten als Karten
erscheinen.

**Vorlagen bekommen die Filter:** das vierte Layout-Argument ist die Liste der Optionen und Filter
dieses Aufrufs. Eine Vorlage kann damit zeigen, was zur Reihe passt, `Rolle-gusp` in einer
`stufe:gusp`-Reihe und sonst `Rolle`, siehe `card-person.html` der Beispielseite.

## Wie man eine Kartenvorlage schreibt

Eine Kartenvorlage ist ein normales Layout. Sie bekommt die Seite der Karte und den Namen der
Vorlage:

    <?php list($name, $page, $type) = $this->yellow->getLayoutArguments() ?>
    <div class="card">
    <h3><a href="<?php echo $page->getLocation(true) ?>"><?php echo $page->getHtml("title") ?></a></h3>
    <p><?php echo $page->getHtml("description") ?></p>
    </div>

Jede Einstellung am Anfang der Zielseite steht zur Verfügung, `$page->getHtml("stufe")` für
`Stufe: gusp` und so weiter. Seiten ohne `Description` bekommen eine aus den ersten Sätzen ihres
Inhalts, damit eine Karte ohne Zusatzarbeit etwas zu zeigen hat. [Mehr über
Layouts](https://datenstrom.se/yellow/help/how-to-customise-html-and-css).

Die Erweiterung bringt kein CSS mit, die Klassen in der Vorlage stylt das Theme.

Hast du Fragen? [Hier gibt's Hilfe](https://datenstrom.se/yellow/help/).
