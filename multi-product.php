<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="multi-product.css">
    <title>Sudhanshu</title>
</head>
<body>
    <header>
        <div class="header-menu">
            <div class="menu-container">
                <div class="left-container">
                    <a href="/">
                        <!-- <img title="logo" src="https://images.bewakoof.com/web/ic-desktop-bwkf-trademark-logo.svg" alt="logo"> -->
                        SUDHANSHU
                    </a>
                    <nav class="header-left-menu">
                        <ul class="header-left-menu-list">
                            <li>MEN</li>
                            <li>WOMEN</li>
                            <li>MOBILE COVERS</li>
                        </ul>
                    </nav>
                </div>

                <div class="right-container">
                    <ul class="header-right-item">
                        <li>
                            <div class="header-search-container">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input class="search_field" type="search" placeholder="Search by products">
                            </div>
                        </li>
                        <span>|</span>
                        <a href="login.html">
                            <span>LOGIN</span>
                        </a>
                        <a href="/wishlist">
                            <span>W</span>
                        </a>
                        <a href="/cart">
                            <span>C</span>
                        </a>
                    </ul>
                </div>
            </div>
        </div>
    </header>

   <section class="announcement-bar">
  <p>FREE SHIPPING on all orders above &#8377 499</p>
</section>

<section class="product-page">
<div class="navigation">
  <span>Home</span>
  <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="none" viewBox="0 0 24 25" class="" stroke="none" style="height: 12px; width: 12px;"><path stroke="#737E93" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8 4.5 8 8-8 8"></path></svg>
  <span>Women Clothing</span>
</div>

<div class="container">

  <div class="filters-container">
<div class="title">
  <h2>FILTER</h2>
  </div>
    <?php
      include 'filters.php';
      $filters = [
        "Category" => ["T-Shirt", "Top", "Hoodies", "Shirts"],
        "Sizes" => ["XS", "S", "M", "L", "XL"],
        "Brand" => ["Bewakoof", "H&M", "Zara"],
        "Color" => ["Black", "White", "Red", "Blue"]
      ];
      renderFilters($filters);
    ?>
  </div>

  <div class="product-section">
       <!-- New Section for Title & Sorting -->
  <?php
    $sortOptions = ['popularity', 'price-low-to-high', 'price-high-to-low', 'name-a-z', 'name-z-a'];
    $currentSort = $_GET['sort'] ?? 'popularity';
    $productCount = 2674;
    $categoryName = "Women's Clothing";
  ?>
  <div class="product-header">
    <div class="product-title">
      <h2><?php echo $categoryName; ?></h2>
      <span class="product-count"><?php echo $productCount; ?> Products</span>
    </div>
    <form id="sortForm" method="GET" class="sort-section">
      <label for="sort">
        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
          <path fill="#666" d="M4 6h16v2H4zm0 5h12v2H4zm0 5h8v2H4z"/>
        </svg> Sort by:
      </label>
      <select name="sort" id="sort" onchange="document.getElementById('sortForm').submit()">
        <option value="popularity" <?php if($currentSort === 'popularity') echo 'selected'; ?>>Popularity</option>
        <option value="price-low-to-high" <?php if($currentSort === 'price-low-to-high') echo 'selected'; ?>>Price: Low to High</option>
        <option value="price-high-to-low" <?php if($currentSort === 'price-high-to-low') echo 'selected'; ?>>Price: High to Low</option>
        <option value="name-a-z" <?php if($currentSort === 'name-a-z') echo 'selected'; ?>>Name: A-Z</option>
        <option value="name-z-a" <?php if($currentSort === 'name-z-a') echo 'selected'; ?>>Name: Z-A</option>
      </select>
    </form>
  </div>


    <!-- Products Grid -->
  <main class="product-grid" id="productGrid">

    <div class="product-card" data-category="tshirt" data-size="S">
      <img src="https://images.bewakoof.com/t640/women-s-black-mickey-graphic-printed-oversized-t-shirt-581989-1726046921-1.jpg" alt="Product">
      <h4>Mickey Oversized T-Shirt</h4>
      <p>₹699 <span class="discount">46% OFF</span></p>
    </div>

    <div class="product-card" data-category="hoodie" data-size="M">
      <img src="https://images.bewakoof.com/t640/women-s-black-mickey-graphic-printed-oversized-t-shirt-581989-1726046921-1.jpg" alt="Product">
      <h4>Garfield Hoodie</h4>
      <p>₹849 <span class="discount">50% OFF</span></p>
    </div>

    <div class="product-card" data-category="top" data-size="XS">
      <img src="https://images.bewakoof.com/t640/women-s-black-mickey-graphic-printed-oversized-t-shirt-581989-1726046921-1.jpg" alt="Product">
      <h4>Starry Sky Top</h4>
      <p>₹599 <span class="discount">60% OFF</span></p>
    </div>

    <div class="product-card" data-category="top" data-size="XS">
      <img src="https://images.bewakoof.com/t640/women-s-black-mickey-graphic-printed-oversized-t-shirt-581989-1726046921-1.jpg" alt="Product">
      <h4>Starry Sky Top</h4>
      <p>₹599 <span class="discount">60% OFF</span></p>
    </div>
    <!-- Add more products similarly -->

  </main>
  </div>
</div>
</section>

<script src="filters.js"></script>
</body>
</html>