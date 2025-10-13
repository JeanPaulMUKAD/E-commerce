<?php
    if (isset ($_GET["search"]) and ($_GET["search"] == "connexion/login.php"))
     {
        include("connexion/login.php");
     }
     elseif(isset ($_GET["search"]) and ($_GET["search"] == "connexion/reset.php"))
     {
        include("Connexion/reset.php");
     }
     elseif(isset ($_GET["search"]) and ($_GET["search"] == "connexion/inscription.php"))
    {
        include("connexion/inscription.php");
    }
     else
     {
         include("connexion/login.php");
     }
 ?>