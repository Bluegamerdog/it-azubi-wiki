<?php
session_start();
// impressum.php
include 'F:\xamp\htdocs\it-azubi-wiki\includes\header.php';

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impressum</title>
    <link rel="stylesheet" href="styles.css"> <!-- optional: dein Stylesheet -->
</head>

<body>

    <header class="top-nav">
        <h1>Impressum</h1>
    </header>

    <main class="container">
        <section>
            <h2>Angaben gemäß § 5 TMG</h2>
            <p><strong>IT Forum</strong><br>
                Deutschland
            </p>
        </section>

        <section>
            <h2>Kontakt</h2>
            <p>
                Telefon: +49 123 456789<br>
                E-Mail: <a href="mailto:info@itforum.de">info@itforum.de</a><br>
                Website: <a href="https://www.itforum.de">www.itforum.de</a>
            </p>
        </section>

        <section>
            <h2>Haftungsausschluss</h2>
            <p>Die Inhalte dieser Website wurden mit größtmöglicher Sorgfalt erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte übernehmen wir jedoch keine Haftung.</p>
        </section>

        <section>
            <h2>Urheberrecht</h2>
            <p>Alle Inhalte dieser Website, einschließlich Texte, Bilder, Grafiken und Logos, sind urheberrechtlich geschützt. Die Vervielfältigung, Bearbeitung oder Verbreitung dieser Inhalte ist ohne ausdrückliche Zustimmung des Urhebers nicht gestattet.</p>
        </section>
    </main>
    <?php
    include 'F:\xamp\htdocs\it-azubi-wiki\includes\footer.php';
    ?>
</body>

</html>