<!DOCTYPE html>
 <html>

    <head>
<!--DO NOT TOUCH-->
        <link href="style.css" type="text/css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <script src="script.js"></script>
    </head>



    <body>
<!--Registrierungs Formular-->
        <div id="createAccountWrapper">
            <form id="createAccountForm" action="registrierung.php" method="post">

                <?php
                //prüfung auf lehrheit der Felder
                if (empty($_POST["createAccountNName"]) or empty($_POST["createAccountVName"]) or empty($_POST["createAccountEmail"]) or empty($_POST["createAccountGender"]) or empty($_POST["createAccountAge"]) or empty($_POST["createAccountPlz"]) or empty($_POST["createAccountAdress"]) or empty($_POST["createAccountPassword"]) or empty($_POST["createAccountPasswordConfirm"])){
                    echo"bitte befüllen Sie alle Felder! \n"; //Ausgabe wen lehr
                }else{
                    //wenn alle Felder gefüllt
                    //Prüfung auf die Gleichheit der Passwörter
                    if ($_POST["createAccountPassword"] != $_POST["createAccountPasswordConfirm"]){
                        echo"Ihre Passwörter Stimmen nicht überein! \n"; //Ausgabe wenn nicht gleich
                    }else{
                        //wenn Gleich los legen
                        //Daten in Variabeln speichern
                        $nname = $_POST["createAccountNName"];
                        $vname = $_POST["createAccountVName"];
                        $email = $_POST["createAccountEmail"];
                        $gender = $_POST["createAccountGender"];
                        $Geburtsdatum = $_POST["createAccountAge"];
                        $plz = $_POST["createAccountPlz"];
                        $adresse = $_POST["createAccountAdress"];
                        $pas = $_POST["createAccountPassword"];
                        echo"gespeichert! \n"; //test Ausgabe
                    }
                }
                
                ?>

                <div id="createAccountNameWrapper">
                    <label id="createAccountNNamelabel" class="createAccountLabel">Name</label>
                    <input type="text" name="createAccountNName" id="createAccountNName" /> <br />
                </div>

                <div id="createAccountVNameWrapper">
                    <label id="createAccountVNameLabel" class="createAccountLabel">Vorname</label>
                    <input type="text" name="createAccountVName" id="createAccountVName" /> <br />
                </div>

                <div id="createAccountEmailWrapper">
                    <label id="createAccountEmailLabel" class="createAccountLabel">Email</label>
                    <input type="email" name="createAccountEmail" id="createAccountEmail"/> <br />
                </div>

                <div id="createAccountGenderWrapper">
                    <label id="createAccountGenderLabel">Geschlecht</label> <br />
                    <input type="radio" name="createAccountGender" id="createAccountGenderMale" value="M">
                    <label>Männlich</label> <br />
                    <input type="radio" name="createAccountGender" value="W"/>
                    <label>Weiblich</label> <br />
                    <input type="radio" name="createAccountGender" value="D"/>
                    <label>Divers</label> <br />
                </div>

                <div id="createAccountAgeWrapper">
                    <label id="createAccountAgeLabel">Geburtsdatum,</label>
                    <input type="date" name="createAccountAge" id="createAccountAge" class="createAccountInput"/> <br />
                </div>

                <div id="createAccountAdressWrapper">
                    <label id="createAccountPlzLabel" class="createAccountLabel">PLZ</label>
                    <input type="text" name="createAccountPlz" id="createAccountPlz" class="createAccountInput"/> <br />
                    
                    <label id="createAccountAdressLabel" class="createAccountLabel">Adresse</label>
                    <input type="text" name="createAccountAdress" id="createAccountAdress" class="createAccountInput"/>
                </div>

                <div id="createAccountPasswordWrapper">
                    <label id="createAccountPasswordLabel" class="createAccountLabel">Passwort</label>
                    <input type="password" name="createAccountPassword" id="createAccountPassword" class="createAccountInput"/> <br />
                    <label id="createAccountPasswordConfirmLabel" class="createAccountLabel">Passwort bestätigen</label> 
                    <input type="password" name="createAccountPasswordConfirm" id="createAccountPasswordConfirm" class="createAccountInput"/> <br /> 
                </div>

                <input type="submit" value="Submit" name="createAccountSubmit" id="createAccountSubmit" class="createAccountInput" />
            </form>
        </div>
    </body>
</html>