<?php
    require_once("./../lib.php");

    $productsList = new Products("./../products.xml");
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $productsList->add_product_to_xml($_POST);

            header('Location: index.php?added=1');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>List of products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

    <h1>List of products</h1>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">The product was added to the xml file.</div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#newProductModal">
        New product
    </button>

    <?php
        $productsList->print_html_table_with_all_products();
    ?>

    <div class="modal fade" id="newProductModal" tabindex="-1" aria-labelledby="newProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="post" action="index.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newProductModalLabel">New product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="NAME" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="NAME" name="NAME" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="PRICE" class="form-label">Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="PRICE" name="PRICE">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="QUANTITY" class="form-label">Quantity</label>
                                <input type="number" min="0" class="form-control" id="QUANTITY" name="QUANTITY">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="CATEGORY" class="form-label">Category</label>
                                <input type="text" class="form-control" id="CATEGORY" name="CATEGORY">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="CATEGORY_ID" class="form-label">Category id</label>
                                <input type="text" class="form-control" id="CATEGORY_ID" name="CATEGORY_ID">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="MANUFACTURER" class="form-label">Manufacturer</label>
                                <input type="text" class="form-control" id="MANUFACTURER" name="MANUFACTURER">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="BARCODE" class="form-label">Barcode</label>
                                <input type="text" class="form-control" id="BARCODE" name="BARCODE">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="WEIGHT" class="form-label">Weight</label>
                                <input type="text" class="form-control" id="WEIGHT" name="WEIGHT" placeholder="6.1kg">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="INSTOCK" class="form-label">In stock</label>
                                <select class="form-select" id="INSTOCK" name="INSTOCK">
                                    <option value="Y" selected>Y</option>
                                    <option value="N">N</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="AVAILABILITY" class="form-label">Availability</label>
                                <input type="text" class="form-control" id="AVAILABILITY" name="AVAILABILITY" placeholder="Άμεσα Διαθέσιμο">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
