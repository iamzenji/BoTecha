<?php
include 'includes/connection.php';
include 'includes/header.php';

if (strlen($_SESSION['employee_id']) === 0) {
    header('location:login.php');
    session_destroy();
} else {
    $query = "SELECT inventory.*, cart.category, cart.brand, cart.type, cart.quantity, cart.unit_qty
              FROM inventory    
              INNER JOIN cart ON inventory.cart_id = cart.cart_id";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0) {
?>
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-8">
                    <h2>Manage Inventory</h2>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <table class="inv-color-table table">
                                <thead>
                                    <tr>
                                        <td colspan="9">
                                            <div class="container">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <button class="btn btn-outline-primary" style="height: 40px;">Export Inventory</button>
                                                    </div>
                                                    <div class="align-middle col-md-6">
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button" class="btn btn-outline-primary" id="toggleSearch">
                                                                <i class="lni lni-search-alt"></i>
                                                            </button>
                                                            <div id="searchContainer" class="col-md-6" style="display: none;">
                                                                <div class="d-flex justify-content-end">
                                                                    <input type="text" id="searchInput" class="form-control col-md-6" style="width: 260px; height: 30px; font-size: 12px;" placeholder="Search by Category, Brand name, Type ">
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </td>

                                    </tr>
                                </thead>
                                <thead>
                                    <tr class="align-middle text-center">
                                        <th>Category</th>
                                        <th>Brand name</th>
                                        <th>Type</th>
                                        <th>Quantity Stock</th>
                                        <th>Unit Quantity</th>
                                        <th>Storage Location</th>
                                        <th>Showroom Quantity Stock</th>
                                        <th>Showroom Location</th>
                                        <th>Quantity to Reorder</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // total quantity available stock
                                        $total_quantity = $row['unit_inv_qty'] - $row['showroom_quantity_stock'];

                                        // Check if showroom quantity if below 10 and need to stock
                                        if ($row['showroom_quantity_stock'] <= 10) {
                                            // Refill showroom quantity to 100 standard stock
                                            $row['showroom_quantity_stock'] = 100;
                                        }

                                        // default 0
                                        if ($row['showroom_quantity_stock'] < 0) {
                                            $row['showroom_quantity_stock'] = 0;
                                        }
                                    ?>
                                        <tr class="edit-row align-middle text-center" data-toggle="modal" data-target="#editModal_<?php echo $row['inventory_id']; ?>">
                                            <td><?php echo $row['category']; ?></td>
                                            <td><?php echo $row['brand']; ?></td>
                                            <td><?php echo $row['type']; ?></td>
                                            <td><?php echo $row['qty_stock'] ?></td>
                                            <td><?php echo $total_quantity ?></td>
                                            <td><?php echo $row['storage_location']; ?></td>
                                            <td><?php echo $row['showroom_quantity_stock']; ?></td>
                                            <td><?php echo $row['showroom_location']; ?></td>
                                            <td><?php echo $row['quantity_to_reorder']; ?></td>
                                        </tr>

                                        <div class="modal fade" id="editModal_<?php echo $row['inventory_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel_<?php echo $row['inventory_id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="editModalLabel_<?php echo $row['inventory_id']; ?>">Edit Inventory</h5>
                                                        <button type="button" class="close close-modal-button" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="update_inventory.php" method="post">
                                                            <input type="hidden" name="inventory_id" value="<?php echo $row['inventory_id']; ?>">
                                                            <div class="form-group">
                                                                <label for="qty_stock">Quantity Stock:</label>
                                                                <input type="text" class="form-control" id="qty_stock" name="qty_stock" value="<?php echo $row['qty_stock']; ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="unit_inv_qty">Unit Quantity:</label>
                                                                <input type="text" class="form-control" id="unit_inv_qty" name="unit_inv_qty" value="<?php echo $row['unit_inv_qty']; ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="storage_location">Storage Location:</label>
                                                                <input type="text" class="form-control" id="storage_location" name="storage_location" value="<?php echo $row['storage_location']; ?>">
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="showroom_quantity_stock">Showroom Quantity Stock:</label>
                                                                <input type="text" class="form-control" id="showroom_quantity_stock" name="showroom_quantity_stock" value="<?php echo $row['showroom_quantity_stock']; ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="showroom_location">Showroom Location:</label>
                                                                <input type="text" class="form-control" id="showroom_location" name="showroom_location" value="<?php echo $row['showroom_location']; ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="quantity_to_reorder">Quantity to Reorder:</label>
                                                                <input type="text" class="form-control" id="quantity_to_reorder" name="quantity_to_reorder" value="<?php echo $row['quantity_to_reorder']; ?>">
                                                            </div>
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10">
                                            <div class="container pt-3">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <nav aria-label="Page navigation">
                                                            <ul class="pagination justify-content-start">
                                                                <li class="page-item disabled">
                                                                    <span class="page-link">&laquo;</span>
                                                                </li>
                                                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                                <li class="page-item">
                                                                    <a class="page-link" href="#" aria-label="Next">
                                                                        <span aria-hidden="true">&raquo;</span>
                                                                        <span class="sr-only">Next</span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </nav>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="d-flex justify-content-end">
                                                            <label for="rowsPerPage" class="mr-5" style="flex-shrink: 0;">Rows per page:</label>
                                                            <select class="form-control pl-5" id="rowsPerPage" style="width: 60px;">
                                                                <option>10</option>
                                                                <option>25</option>
                                                                <option>50</option>
                                                                <option>100</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.getElementById('toggleSearch').addEventListener('click', function() {
                var searchContainer = document.getElementById('searchContainer');
                searchContainer.style.display = (searchContainer.style.display === 'none' || searchContainer.style.display === '') ? 'block' : 'none';
            });
        </script>
<?php
    }
}
?>