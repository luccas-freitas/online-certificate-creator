<?php
// --------- Conectando ao Banco de Dados --- //
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'lucca');
define('MYSQL_PASSWORD', '061294');
define('MYSQL_DB_NAME', 'certificados');

try {
    $PDO = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD);
    $PDO->exec("set names utf8");
} catch (PDOException $e) {
    echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
}

$sql = "SELECT * FROM eventos ORDER BY dia DESC";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>SILP | Eventos & Treinamentos</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
    <script src="typed.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>

<body>

    <nav class="navbar navbar-default" style="height: 90px;">
        <div class="container">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="https://www.silp.com.br/">
                        <img width="280" src="https://www.silp.com.br/wp-content/uploads/2018/08/silp.svg" alt="SILP"></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="https://www.silp.com.br/">Home</a></li>
                        <li><a href="https://www.silp.com.br/sobre/">Sobre</a></li>
                        <li><a href="https://www.silp.com.br/treinamentos/">Cursos</a></li>
                        <li class="active"><a href="http://www.silp.com.br/cursos/certificado/">Certificados</a></li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Eventos <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="https://silp.com.br/conferencia-nacional">Conferência Nacional 2017</a></li>
                                <li><a href="http://www.conferencianacionaldosconselhos.com/">Conferência Nacional 2018</a></li>
                            </ul>
                        </li>
                        <li><a href="https://www.silp.com.br/palestrantes/" class="nav-top-link">Palestrantes</a></li>
                        <li><a href="https://www.silp.com.br/nossas-certidoes/" class="nav-top-link">Certidões</a></li>
                        <li><a href="https://www.silp.com.br/contato/" class="nav-top-link">Fale conosco</a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </div>
        </div>
    </nav>

    <div class="row" style="text-align:center;">
        <h1>
            <span id="typed"></span>
        </h1>
    </div>
    <div class="container">
        <form class="form-horizontal" action="gerar_certificado/generate.php" method="post" id="generate_form">
            <fieldset>
                <p>&nbsp;</p>

                <div class="form-group dropdown">
                    <label class="col-md-4 control-label">Curso</label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fas fa-award"></i>
                            </span>
                            <select class="form-control" name="curso">
                                <?php foreach ($results as $row) : ?>
                                    <option value="<?= $row['id']; ?>">
                                        <?= $row['cidade'] . ' / ' . $row['uf'] . ' - ' . date('d/m/y', strtotime($row['dia'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label">CPF</label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fas fa-user"></i>
                            </span>
                            <input name="cpf" placeholder="CPF" class="form-control" type="text" maxlength="14" onkeypress="formatar('###.###.###-##', this);">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label">E-Mail</label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input name="email" placeholder="E-Mail" class="form-control" type="text">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label"></label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline col-md-12">Gerar Certificado</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <div class="container">
        <form class="form-horizontal" action="gerar_certificado/validate.php" method="get" id="validate_form">
            <div class="col-md-4 inputGroupContainer">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fas fa-search text-white" aria-hidden="true"></i>
                    </span>
                    <input name="busca" class="form-control" type="text" placeholder="Insira o código impresso no certificado" aria-label="Search">
                </div>
            </div>
            <button class="btn btn-outline-white" type="submit">Validar Certificado</button>
        </form>
    </div>

    <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src='http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'></script>
    <script src='http://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.4.5/js/bootstrapvalidator.min.js'></script>
    <script src="script.js"></script>

    <!-- ***** Typed.js ******* -->
    <script>
        var options = {
            strings: ["# Emita aqui seu Certificado Online!"],
            typeSpeed: 40,
        }
        var typed = new Typed("#typed", options);
    </script>
</body>

</html>