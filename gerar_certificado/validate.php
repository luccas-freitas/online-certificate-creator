<?php
    require('fpdf/image_alpha.php');
    require('PHPMailer/class.phpmailer.php');

    // --------- Variáveis do Formulário ----- //
    $id = $_GET['busca'];

    // --------- Conectando ao Banco de Dados --- //
    define( 'MYSQL_HOST', 'localhost' );
    define( 'MYSQL_USER', 'lucca' );
    define( 'MYSQL_PASSWORD', '061294' );
    define( 'MYSQL_DB_NAME', 'certificados' );

    try{
        $PDO = new PDO( 'mysql:host=' . MYSQL_HOST 
        . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD );
        $PDO->exec("set names utf8");
    }
    catch ( PDOException $e ){
        echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
    }

    class Participante {
        public $id, $nome, $cpf, $evento_id;
    }

    // --------- Busca de CPF Cadastrado no Banco de Dados ----- //
    $sql = "SELECT * FROM participantes WHERE id = '$id' ";
    $stmt = $PDO->prepare($sql);
    $stmt->execute();

    // --------- Gerando Certificado em PDF ----- //
    if($stmt->rowCount() > 0) {
        $row = $stmt->fetchObject('Participante');
        
        $sql = "SELECT filename FROM eventos WHERE id = $row->evento_id";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        $filename = $stmt->fetchColumn();

        $pdf = new PDF_ImageAlpha();
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddFont('ITCEDSCR','', 'ITCEDSCR.php');
        
        // Frente
        $pdf->AddPage('L');
        $pdf->SetLineWidth(1.5);
        $pdf->Image("layout/$filename.jpg",0,0,300);
            // Nome
            $pdf->SetFont('ITCEDSCR', '', 40); // Tipo de fonte e tamanho
            $pdf->SetXY(73,83); //Parte chata onde tem que ficar ajustando a posição X e Y
            $pdf->MultiCell(160, 10, $row->nome, '', 'C', 0); // Tamanho width e height e posição

        // Verso
        $pdf->AddPage('L');
        $pdf->SetLineWidth(1.5);
        $pdf->Image("layout/$filename-verso.jpg",0,0,300);

        $certificado="arquivos/$row->cpf.pdf";
        $pdfdoc = $pdf->Output('', 'S');

        $pdf->Output();
    }else {
        print
        '<script type="text/javascript">
            alert("Certificado não encontrado.");
            location="../index.php";
        </script>';
    }

    $stmt->closeCursor();
?>
