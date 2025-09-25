<html>
    <head> 
        <title>Kelas Asas PHP</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="bg-light">

        <?php
            $nama_kasut = "Air Max"; //String
            $brand = "Nike"; //String
            $size = 42; //Integer
            $harga_kasut = 20 ; 
            $diskaun = 10; //Integer
            $stock = 2; //Integer
            $onlineOnly = true; //Boolean
        ?>

        <!-- <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="mb-0"> <?php echo $nama_kasut; ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Nama Kasut:</strong>
                                </div>
                                <div class="col-6">
                                    <?php echo $nama_kasut . " " . "(RM" . $harga_kasut . ")"  ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Brand:</strong>
                                </div>
                                <div class="col-6">
                                    <?php echo $brand; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Size:</strong>
                                </div>
                                <div class="col-6">
                                    <?php echo $size; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Harga:</strong>
                                </div>
                                <div class="col-6">
                                    RM<?php echo $harga_kasut; ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Diskaun:</strong>
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-success"><?php echo $diskaun; ?>%</span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Stock:</strong>
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-<?php echo $stock > 0 ? 'info' : 'danger'; ?>"><?php echo $stock; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>