<?php

session_start();

/*
  Copyright (C) 2015 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma è distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */


/* Programma per la visualizzazione dell'elenco delle tbl_classi. */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
$idalunno = stringa_html('idalunno');
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

$titolo = "Elenco presenze forzate";
$script = "";
stampa_head($titolo, "", $script, "SDMAP");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", $_SESSION['nome_scuola'], $_SESSION['comune_scuola']);


//
//    Fine parte iniziale della pagina
//
//Connessione al server SQL
$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));


$query = "SELECT * FROM tbl_alunni LEFT JOIN tbl_classi
         ON tbl_alunni.idclasse=tbl_classi.idclasse
         ORDER BY cognome,nome,anno, sezione, specializzazione";

$ris = eseguiQuery($con, $query);

print "<form name='selealu' action='vispresenzeforzate.php' method='post'>";
print "<table align='center'>";
print "<tr><td>Alunno</td>";
print "<td>";
print "<select name='idalunno' ONCHANGE='selealu.submit();'><option value=''>Tutti</option>";
while ($rec = mysqli_fetch_array($ris))
{
    if ($idalunno == $rec['idalunno'])
    {
        $sele = " selected";
    } else
    {
        $sele = "";
    }
    print ("<option value='" . $rec['idalunno'] . "'$sele>" . $rec['cognome'] . " " . $rec['nome'] . " (" . $rec['datanascita'] . ") - " . $rec['anno'] . " " . $rec['sezione'] . " " . $rec['specializzazione'] . "</option>");
}
print "
 </select>
 </td>

 </tr>

 </table></form><br><br>";

if ($idalunno != "")
{
    $selealunno = " AND tbl_presenzeforzate.idalunno=$idalunno ";
} else
{
    $selealunno = " ";
}
//Esecuzione query
$query = "SELECT * FROM tbl_presenzeforzate,tbl_alunni,tbl_classi
            WHERE tbl_presenzeforzate.idalunno = tbl_alunni.idalunno
              AND tbl_alunni.idclasse = tbl_classi.idclasse
              $selealunno
              ORDER BY data desc,cognome,nome,anno,sezione,specializzazione,data";
$ris = eseguiQuery($con, $query);

print "<CENTER><TABLE BORDER='1'>";
print "<TR class='prima'><TD ALIGN='CENTER'><B>Alunno</B></TD><TD ALIGN='CENTER'><B>Data</B></TD><TD ALIGN='CENTER'><B>Motivo</B></TD></TD><TD COLSPAN='2' ALIGN='CENTER'><B>Azioni</B></TD></TR>";
while ($dati = mysqli_fetch_array($ris))
{


    print "<TR class='oddeven'><TD>" . $dati['cognome'] . " " . $dati['nome'] . " " . data_italiana($dati['datanascita']) . " (" . $dati['anno'] . " " . $dati['sezione'] . " " . $dati['specializzazione'] . ")" . "</TD><TD>" . data_italiana($dati['data']) . " " . giorno_settimana($dati['data']) . "</TD><TD>" . $dati['motivo'] . "</TD>";
    print "<TD align='center'>";


    print "<A HREF='delpresenzeforzate.php?idpre=" . $dati['idpresenzaforzata'] . "&idalunno=$idalunno'><img src='../immagini/delete.png' title='Elimina'></A>";

    print "</TD></TR>";
}
print "</CENTER></TABLE>";


stampa_piede("");
mysqli_close($con);




