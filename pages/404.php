<!doctype html>
<html lang="en">
    <?php

    $page_description = '404 Not Found';
    $page_name = 'Page Not Found';

    require __DIR__ . '/../templates/header.php';

    ?>
<body>
    <div class="container">
        <div class="alert alert-warning" role="alert">
            <h3 class="mb-lg-5 alert-heading">Requested Page Was Not Found!</h3>
            <p>
                The requested page <code><?php echo $_SERVER['REQUEST_URI'] ?></code> was not found on the server.
            </p>
            <p>Maybe you misspelled the name?</p>
            <hr>
            <p><?php echo date('d/m/Y H:i:s') ?></p>
        </div>
    </div>

    <!-- Bootstrap JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
