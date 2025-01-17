<?php
    session_start();

    if (isset($_GET['value'])) {
        $_SESSION["category"] = $_GET['value'];
    }

    $category = $_SESSION["category"];

    ob_start();

    include "dbconnect.php";
    include("page_navigation.php");
    $connection = new Connection();
    $pdo = $connection->CheckConnect();

    $limit = 12;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    if (isset($_GET['sort'])) {
        if($category != 0)
        {
            $query = "SELECT COUNT(*) AS total_rows FROM products WHERE Category_ID=:category";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':category', $category);
            $stmt->execute();

            $total_rows = $stmt->fetch(PDO::FETCH_ASSOC)['total_rows'];

            $p = new Pager();
            $pages = $p->findPages($total_rows, $limit);
            $vt = $p->findStart($limit);

            // Xử lý sự kiện sắp xếp
            switch ($_GET['sort']) {
                case "price":
                    $query = "SELECT * FROM products WHERE Category_ID=:category AND IsActive = 1 ORDER BY price_after_discount LIMIT $vt, $limit";
                    break;
                case "priceh":
                    $query = "SELECT * FROM products WHERE Category_ID=:category AND IsActive = 1 ORDER BY price_after_discount DESC LIMIT $vt, $limit";
                    break;
                case "discount":
                    $query = "SELECT * FROM products WHERE Category_ID=:category AND IsActive = 1 ORDER BY Discount DESC LIMIT $vt, $limit";
                    break;
                case "discountl":
                    $query = "SELECT * FROM products WHERE Category_ID=:category AND IsActive = 1 ORDER BY Discount LIMIT $vt, $limit";
                    break;
                default:
                    $query = "SELECT * FROM products WHERE Category_ID=:category AND IsActive = 1 LIMIT $vt, $limit";
            }

            // Thực hiện truy vấn sử dụng PDO Prepared Statement
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':category', $category);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        else
        {
            $query = "SELECT COUNT(*) AS total_rows FROM products";
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            $total_rows = $stmt->fetch(PDO::FETCH_ASSOC)['total_rows'];

            $p = new Pager();
            $pages = $p->findPages($total_rows, $limit);
            $vt = $p->findStart($limit);

            // Xử lý sự kiện sắp xếp
            switch ($_GET['sort']) {
                case "price":
                    $query = "SELECT * FROM products WHERE IsActive = 1 ORDER BY price_after_discount LIMIT $vt, $limit";
                    break;
                case "priceh":
                    $query = "SELECT * FROM products WHERE IsActive = 1 ORDER BY price_after_discount DESC LIMIT $vt, $limit";
                    break;
                case "discount":
                    $query = "SELECT * FROM products WHERE IsActive = 1 ORDER BY Discount DESC LIMIT $vt, $limit";
                    break;
                case "discountl":
                    $query = "SELECT * FROM products WHERE IsActive = 1 ORDER BY Discount LIMIT $vt, $limit";
                    break;
                default:
                    $query = "SELECT * FROM products WHERE IsActive = 1 LIMIT $vt, $limit";
            }

            // Thực hiện truy vấn sử dụng PDO Prepared Statement
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        
    } else {
        // Nếu không có sự kiện POST 'sort', thực hiện truy vấn bình thường
        if($category != 0)
        {
            $query = "SELECT COUNT(*) AS total_rows FROM products WHERE Category_ID=:category AND IsActive = 1";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':category', $category);
            $stmt->execute();

            $total_rows = $stmt->fetch(PDO::FETCH_ASSOC)['total_rows'];

            $p = new Pager();
            $pages = $p->findPages($total_rows, $limit);
            $vt = $p->findStart($limit);

            $query = "SELECT * FROM products WHERE Category_ID=:category AND IsActive = 1 LIMIT $vt, $limit";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':category', $category);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        else
        {
            $query = "SELECT COUNT(*) AS total_rows FROM products WHERE IsActive = 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            $total_rows = $stmt->fetch(PDO::FETCH_ASSOC)['total_rows'];

            $p = new Pager();
            $pages = $p->findPages($total_rows, $limit);
            $vt = $p->findStart($limit);

            $query = "SELECT * FROM products WHERE IsActive = 1 LIMIT $vt, $limit";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        }
    }

    if($category != 0)
    {
        $query = "SELECT * FROM category WHERE Category_ID=:category";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        $c = $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    $sql = "select * from category";
    $sta = $pdo->prepare($sql);
    $sta->execute();
    $all_category = $sta->fetchAll(PDO::FETCH_OBJ);
?>

<div style="margin-top: 20px">
    <div class="row">
        <div class="col-md-3 col-lg-3" id="category">
            <div style="background:#D67B22;color:#fff;font-weight:800;border:none;padding:15px;">Danh mục</div>
            <ul>
                <li>
                    <a href="product_page.php?value=0" style="padding-left: 10px">Tất cả sản phẩm</a>
                </li>
                <?php
                foreach ($all_category as $ca) {
                    ?>
                        <li>
                            <a href="product_page.php?value=<?php echo $ca->Category_ID; ?>" style="padding-left: 10px"><?php echo $ca->Category_Name; ?></a>
                        </li>
                    <?php
                }
                ?>
            </ul>
        </div>

        <div class="col-md-9 col-lg-9">
            <?php
                if($category == 0)
                {
                    ?>
                        <h2 style="color:rgb(228, 55, 25);text-transform:uppercase;margin-bottom:0px;">Tất cả sách</h2>
                    <?php
                }
                else
                {
                    ?>
                        <h2 style="color:rgb(228, 55, 25);text-transform:uppercase;margin-bottom:0px;">Sách <?php echo $c[0]->Category_Name ?></h2>
                    <?php
                }
            ?>

            <div class="text-end" style="margin: 20px 0 50px 0;">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="pull-right">
                    <label for="sort">Sort by &nbsp: &nbsp</label>
                    <select name="sort" id="select" onchange="form.submit()">
                        <option value="default" name="default" selected="selected">Select</option>
                        <option value="price" name="price">Low To High Price </option>
                        <option value="priceh" name="priceh">Highest To Lowest Price </option>
                        <option value="discountl" name="discountl">Low To High Discount </option>
                        <option value="discount" name="discount">Highest To Lowest Discount</option>
                    </select>
                </form>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); grid-gap: 20px;">
            <?php
                foreach($result as $all)
                {
                    ?>
                        <div class="product_item" style="text-align: center; position: relative; margin-top: 20px; border: 1px #c6c6c6 solid;">
                            <div class="Product_thumnail">
                                <?php
                                    if($all->Discount != 0)
                                    {
                                        ?>
                                            <span class="label_sale">-<?php echo $all->Discount; ?>%</span>
                                        <?php
                                    }
                                ?>
                                <a href="description.php?value=<?php echo $all->Product_ID; ?>"><img src="img/books/<?php echo $all->Image; ?>" alt="<?php echo $all->Title; ?>" style="max-height: 300px;"></a>
                            </div>
                            <div style="background-color: #f1f1f1;">
                                <div style="height: 64px; margin-bottom: 5px;" class="Product_name">
                                    <a href="description.php?value=<?php echo $all->Product_ID; ?>"><?php echo $all->Title; ?></a>
                                </div>
                                <div>
                                    <?php
                                        $star_rating = $all->Stars; // Điểm số đánh giá
                                        $full_stars = floor($star_rating); // Số sao full
                                        $half_star = ceil($star_rating) != $full_stars; // Có nửa sao hay không
                                        $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0); // Số sao trống

                                        // Hiển thị các sao
                                        for ($i = 0; $i < $full_stars; $i++) {
                                            echo '<i class="fa-solid fa-star"></i>';
                                        }
                                        if ($half_star) {
                                            echo '<i class="fa-solid fa-star-half-alt"></i>';
                                        }
                                        for ($i = 0; $i < $empty_stars; $i++) {
                                            echo '<i class="fa-regular fa-star"></i>';
                                        }
                                    ?>
                                </div>
                                <span style="font-weight: bold; color: #707070;">Mã sản sách: <?php echo $all->Product_ID; ?></span>
                                <div>
                                    <?php
                                        if($all->Discount != 0)
                                        {
                                            ?>
                                                <span class="Product_after_discount"><?php echo number_format($all->price_after_discount); ?> ₫</span>
                                                <span class="Product_full_price"><?php echo number_format($all->Price); ?> ₫</span>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                                <span class="Product_after_discount"><?php echo number_format($all->price_after_discount); ?> ₫</span>
                                            <?php
                                        }
                                    ?>
                                </div>
                                <div class="product_buttons">
                                    <?php
                                        if(isset($_SESSION['user']))
                                        {
                                            ?>
                                                <a class="add_to_cart_button" href="cart.php?value=<?php echo $all->Product_ID; ?>"><i class="fa-solid fa-cart-shopping"></i></a>
                                            <?php
                                        }
                                    ?>
                                    <a class="view_details_button" href="description.php?value=<?php echo $all->Product_ID; ?>"><i class="fa-regular fa-eye"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php
                }
            ?>
        </div>
        <!-- Hiển thị phân trang -->
        <?php
            if($pages > 1)
            {
                ?>
                    <div class="d-flex justify-content-end" style="margin-top: 20px; font-size:20px;">
                        <?php echo $p->pageList($current_page, $pages); ?>
                    </div>
                <?php
            }
        ?>
    </div>
</div>

<?php
    $content = ob_get_clean();
    include ("page_layout.php");
?>