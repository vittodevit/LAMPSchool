<?php

session_start();


/*
  Copyright (C) 2015 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma é distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Forzatura assenza per tutti";
$script = "";
stampa_head($titolo, "", $script, "MSPD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);



$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$query = "SELECT idalunno from tbl_alunni where idclasse<>0";
$ris = eseguiQuery($con, $query);
$cont = 0;
while ($rec = mysqli_fetch_array($ris))
{
    if (!esiste_assenza_alunno($rec['idalunno'], date('Y-m-d'), $con))
    {
        $query = "insert into tbl_assenze(idalunno,data) values (" . $rec['idalunno'] . ",'" . date('Y-m-d') . "')";
        eseguiQuery($con, $query);
        elimina_assenze_lezione($con, $rec['idalunno'], date('Y-m-d'));
        inserisci_assenze_per_ritardi_uscite($con, $rec['idalunno'], date('Y-m-d'));
        $cont++;
    }
}

print "<br><br><center><b>$cont assenze correttamente inserite!</center>";

stampa_piede("");

