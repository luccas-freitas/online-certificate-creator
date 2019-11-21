<?php
    require('fpdf/image_alpha.php');
    require('PHPMailer/class.phpmailer.php');

    // --------- Variáveis do Formulário ----- //
    $email    = $_POST['email'];
    $cpf      = $_POST['cpf'];
    $curso    = $_POST['curso'];

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

    // --------- Busca de CPF Cadastrado no Banco de Dados ----- //
    $cpf_onlyNumber = preg_replace("/[^0-9]/", "", $cpf);
    $sql = "SELECT nome FROM participantes WHERE evento_id = $curso AND cpf = '$cpf_onlyNumber' ";
    $stmt = $PDO->prepare($sql);
    $stmt->execute();

    // --------- Gerando Certificado em PDF ----- //
    if($stmt->rowCount() > 0) {
        $nome = $stmt->fetchColumn();
        
        $sql = "SELECT filename FROM eventos WHERE id = $curso";
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
            $pdf->MultiCell(160, 10, $nome, '', 'C', 0); // Tamanho width e height e posição

        // Verso
        $pdf->AddPage('L');
        $pdf->SetLineWidth(1.5);
        $pdf->Image("layout/$filename-verso.jpg",0,0,300);

        $certificado="arquivos/$cpf.pdf";
        $pdfdoc = $pdf->Output('', 'S');

        $pdf->Output();

        // --------- Enviando documento por e-mail --- //
        $subject = utf8_decode('Certificado - Silp Eventos & Treinamentos');
        $messageBody = utf8_decode("Ola $nome!
            <br><br>
        Com grande prazer entregamos o seu certificado.
        <br>
        Ele segue em anexo nesse e-mail.
        <br><br>
        Atenciosamente,
        <br>SILP | Eventos & Treinamentos
        <br>
        <a href='https://www.silp.com.br/'>https://www.silp.com.br/</a>");

        $mail = new PHPMailer();
        $mail->SetFrom("contato@silp.com.br", "Certificado - Silp Eventos & Treinamentos");
        $mail->Subject = $subject;
        $mail->MsgHTML(utf8_decode($messageBody));	
        $mail->AddAddress($email); 
        $mail->addStringAttachment($pdfdoc, 'certificado.pdf');
        $mail->Send();

    }else {
        print
        '<script type="text/javascript">
            alert("CPF não cadastrado. Favor entrar em contato com nosso suporte via e-mail: contato@silp.com.br");
            location="../index.php";
        </script>';
    }

    $stmt->closeCursor();
?>
