<footer>
    <div class="row">
        <h1><a href="<?php echo home_url(); ?>" class="logo" style="font-size: 1.5rem; text-decoration: none; color: white;">Club<span style="color: red;">Council</span></a></h1>
        <ul>
            <li><a href="<?php echo home_url('/'); ?>">Home</a></li>
            <li><a href="<?php echo home_url('/clubs'); ?>">Clubs</a></li>
            <li><a href="<?php echo home_url('/events'); ?>">Events</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="shareables">
            <i class="fa-solid fa-copy"></i>
            <i class="fa-brands fa-square-twitter"></i>
            <i class="fa-brands fa-square-facebook"></i>
            <i class="fa-brands fa-discord"></i>
            <i class="fa-regular fa-envelope"></i>
        </div>
    </div>
    <div class="row">
        <p>support@clubcouncil.iiit.ac.in</p>
        <p>Copyright © <?php echo date('Y'); ?> Club Council</p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
