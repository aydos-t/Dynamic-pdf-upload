<!doctype html>
<html lang="ru">
<head>
    <title>PDF upload</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width"/>
    <!-- *Note: You must have internet connection on your laptop or pc other wise below code is not working -->
    <!-- Add icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- bootstrap css and js -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <!-- JS for jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <div align="center">
            <br>
            <h5 align="center">Динамическая генерация PDF файла</h5>
            <br>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Инвойс #</th>
                    <th>Имя клиента</th>
                    <th>Контакты #</th>
                    <th>Адресс</th>
                </tr>
                </thead>
                <tbody>
                <?php
                require 'connect.php';
                $display_query = "SELECT T1.MST_ID, T1.INV_NO, T1.CUSTOMER_NAME, T1.CUSTOMER_MOBILENO, T1.ADDRESS FROM INVOICE_MST T1";
                $results = mysqli_query($connect, $display_query);
                $count = mysqli_num_rows($results);
                if ($count > 0) {
                    while ($data_row = mysqli_fetch_array($results, MYSQLI_ASSOC)) {
                        ?>
                        <tr>
                            <td><?php echo $data_row['INV_NO']; ?></td>
                            <td><?php echo $data_row['CUSTOMER_NAME']; ?></td>
                            <td><?php echo $data_row['CUSTOMER_MOBILENO']; ?></td>
                            <td><?php echo $data_row['ADDRESS']; ?></td>
                            <td>
                                <a href="pdf-maker.php?MST_ID=<?php echo $data_row['MST_ID']; ?>&ACTION=VIEW"
                                   class="btn btn-success"><i class="fa fa-file-pdf-o"></i> Посмотреть PDF</a> &nbsp;&nbsp;
                                <a href="pdf-maker.php?MST_ID=<?php echo $data_row['MST_ID']; ?>&ACTION=DOWNLOAD"
                                   class="btn btn-danger"><i class="fa fa-download"></i> Скачать PDF</a>
                                &nbsp;&nbsp;
                                <a href="pdf-maker.php?MST_ID=<?php echo $data_row['MST_ID']; ?>&ACTION=UPLOAD"
                                   class="btn btn-warning"><i class="fa fa-upload"></i> Загрузить PDF</a>
                                &nbsp;&nbsp;
                                <a href="pdf-maker.php?MST_ID=<?php echo $data_row['MST_ID']; ?>&ACTION=EMAIL"
                                   class="btn btn-info"><i class="fa fa-envelope"></i> Email PDF</a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<br>
<center>Разработано
    <a href="https://github.com/aydos-t/" style="display: inline-block; color: red">
        Aydos-T
    </a>
</center>
</body>
</html> 