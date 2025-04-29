<?php include 'includes/header.php'; ?>

<main class="product-details">
    <section class="product-image">
        <img src="assets/img/garfield-tshirt.jpg" alt="Garfield T-shirt">
    </section>

    <section class="product-info">
        <h2>Garfield Runner T-shirt</h2>
        <p class="price">₹399 <span class="original">₹1299</span> <span class="offer">(69% OFF)</span></p>

        <form method="post" action="">
            <label for="size">Size:</label>
            <select name="size" id="size">
                <option>XS</option>
                <option>S</option>
                <option>M</option>
                <option>L</option>
                <option>XL</option>
            </select>

            <label for="color">Color:</label>
            <input type="text" id="color" name="color" value="Orange">

            <label for="desc">Description:</label>
            <textarea id="desc" name="desc">Graphic printed T-shirt in boyfriend fit.</textarea>

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" value="Women's Clothing">

            <button type="submit">Save Product</button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
