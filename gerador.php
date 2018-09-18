<?php
setlocale( LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'portuguese' );
date_default_timezone_set( 'America/Sao_Paulo' );
require('fpdf/image_alpha.php');
require('PHPMailer/class.phpmailer.php');

// --------- Conectando ao Banco de Dados --- //
    $link = mysqli_connect("mysql556.umbler.com", "luccas", "lucc061294", "certificados");;
    $link->set_charset("utf8");
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

// --------- Variáveis do Formulário ----- //
    $email    = $_POST['email'];
    $cpf      = $_POST['cpf'];
    $textoGeral = utf8_decode("pela participação na 2ª Conferência Nacional dos Conselhos Profissionais, realizado em Brasília/DF, entre os dias 14 e 17 de Agosto de 2018 com carga horária total de 32 horas.");
    $textoData = utf8_decode("Brasília, ".utf8_encode(strftime( '%d de %B de %Y', strtotime( date( 'Y-m-d' ) ) )));
    $textoVerso = utf8_decode("\nPerguntas - Fixação do Aprendizado Citação de Acórdãos do TCU.");

// --------- Busca de CPF Cadastrado no Banco de Dados ----- //
    $cpf_onlyNumber = preg_replace("/[^0-9]/", "", $cpf);
    $sql = "SELECT * FROM Participante WHERE cpf = '$cpf_onlyNumber' ";
    $verifica_cpf = mysqli_query($link, $sql) or die(mysql_error());
    $result = mysqli_fetch_array($verifica_cpf);
    $nome = utf8_decode($result['nome']);

// --------- Verificando Workshops realizados ----- //
if(mysqli_num_rows($verifica_cpf) == 1) {
    $sql = "SELECT * FROM Participante_has_Workshop
    INNER JOIN Workshop ON (Participante_has_Workshop.idWorkshop = Workshop.idWorkshop)
    INNER JOIN Participante ON (Participante_has_Workshop.idParticipante = Participante.idParticipante) 
    WHERE Participante.cpf = '$cpf_onlyNumber'";

    $verifica_workshops = mysqli_query($link, $sql);
    while($row = $verifica_workshops->fetch_array()) {
        $rows[] = $row;
    }
    
    $pdf = new PDF_ImageAlpha();
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddFont('Copperplate','', 'Copperplate Bold.php');
    // CERTIFICADO GERAL
        $pdf->AddPage('L');
        $pdf->SetLineWidth(1.5);
        $pdf->Image('layout/certificado-geral.jpg',0,0,300);

        // Mostrar o nome
        $pdf->SetFont('Arial', '', 30); // Tipo de fonte e tamanho
        $pdf->SetXY(110,110); //Parte chata onde tem que ficar ajustando a posição X e Y
        $pdf->MultiCell(170, 10, $nome, '', 'C', 0); // Tamanho width e height e posição
        // Mostrar o corpo
        $pdf->SetFont('Arial', '', 15); // Tipo de fonte e tamanho
        $pdf->SetXY(110,125); //Parte chata onde tem que ficar ajustando a posição X e Y
        $pdf->MultiCell(170, 10, $textoGeral, '', 'J', 0); // Tamanho width e height e posição
    
    // VERSO GERAL
        $pdf->AddPage('L');
        $pdf->SetLineWidth(1.5);
        $pdf->Image('layout/verso-geral.jpg',0,0,300);

    // WORKSHOPS
        foreach($rows as $row) {
            $curso    = $row[3]; //Descricao do Workshop
            $palestrante = utf8_decode($row[4]);
            $data     = $row[5]; //Data do Workshop
            $carga_h  = $row[6]; //Carga Horária do Workshop
            $assinatura = $row[7]; //Assinatura do palestrante (Certificar que JPG está com o mesmo nome)
            $programacao = utf8_decode($row[8]); //Programação no verso do certificado

            $textoWorkshop = utf8_decode("pela participação no Workshop \"".$curso."\", realizado em ".$data." com carga horária total de ".$carga_h." horas.");

            $pdf->AddPage('L');
            $pdf->SetLineWidth(1.5);
            $pdf->Image('layout/certificado-workshop.jpg',0,0,300);

            // Mostrar o nome
            $pdf->SetFont('Arial', '', 30); // Tipo de fonte e tamanho
            $pdf->SetXY(110,110); //Parte chata onde tem que ficar ajustando a posição X e Y
            $pdf->MultiCell(170, 10, $nome, '', 'C', 0); // Tamanho width e height e posição

            // Mostrar o corpo
            $pdf->SetFont('Arial', '', 15); // Tipo de fonte e tamanho
            $pdf->SetXY(110,130); //Parte chata onde tem que ficar ajustando a posição X e Y
            $pdf->MultiCell(170, 10, $textoWorkshop, '', 'J', 0); // Tamanho width e height e posição

            // Mostrar a assinatura
            $pdf->SetFont('Copperplate', '', 10);
            $pdf->SetXY(138, 187);
            $pdf->MultiCell(100, 10, $palestrante, '', 'C', 0);
            $pdf->ImagePngWithAlpha('assinaturas/'. $assinatura . '.png', 154, 138);

    // VERSO WORKSHOP
        $pdf->AddPage('L');
        $pdf->SetLineWidth(1.5);
        $pdf->Image('layout/verso.jpg',0,0,300);

        // Mostrar o corpo
        $pdf->SetFont('Arial', 'I', 12); // Tipo de fonte e tamanho
        $pdf->SetXY(75,45); //Parte chata onde tem que ficar ajustando a posição X e Y
        $pdf->MultiCell(170, 7, $programacao . $textoVerso, '', 'L', 0); // Tamanho width e height e posição
    }
    
    $certificado="arquivos/$cpf.pdf"; //atribui a variável $certificado com o caminho e o nome do arquivo que será salvo (vai usar o CPF digitado pelo usuário como nome de arquivo)
    $pdfdoc = $pdf->Output('', 'S');
    mysqli_free_result($verifica_workshops);
    mysqli_free_result($verifica_cpf);
    $pdf->Output();

    // --------- Enviando documento por e-mail --- //
        $subject = utf8_decode('Certificado - 2ª Conferência Nacional dos Conselhos');
        $messageBody = utf8_decode("Olá ") . $nome . utf8_decode("!
            <br><br>
        É com grande prazer que entregamos o seu certificado.
        <br>
        Ele está em anexo nesse e-mail.
        <br><br>
        Atenciosamente,
        <br>SILP | Eventos & Treinamentos
        <br>
        <a href='https://www.silp.com.br/'>https://www.silp.com.br/</a>");
    $setFrom = utf8_decode("Certificado - Conferência dos Conselhos");
    $mail = new PHPMailer();
    $mail->SetFrom("contato@silp.com.br", $setFrom);
    $mail->Subject = $subject;
    $mail->MsgHTML($messageBody);	
    $mail->AddAddress($email); 
    $mail->addStringAttachment($pdfdoc, 'certificado.pdf');
    $mail->Send();

}else {
    print
    '<script type="text/javascript">
        alert("CPF não cadastrado.");
        location="../certificados.html";
    </script>';
}

mysqli_close($link);

?>